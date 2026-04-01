<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Support\MasterSettings;

class MediaHousePortalController extends Controller
{
    private function mergeIntoProfile(array $data): void
    {
        $user = Auth::user();
        if (!$user) return;

        $profile = $user->profile_data ?? [];
        foreach ($data as $k => $v) {
            if ($v === null || $v === '') continue;
            $profile[$k] = $v;
        }
        $user->update(['profile_data' => $profile]);
    }

    public function index()
    {
        return redirect()->route('mediahouse.portal');
    }

    public function dashboard()
    {
        $user = Auth::user();

        $baseQuery = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'registration');

        $stats = [
            'drafts' => (clone $baseQuery)->where('is_draft', true)->count(),
            'active' => (clone $baseQuery)->where('is_draft', false)
                ->whereNotIn('status', [
                    Application::ISSUED,
                    Application::OFFICER_REJECTED,
                    Application::REGISTRAR_REJECTED,
                ])
                ->count(),
            'approved' => (clone $baseQuery)->where('is_draft', false)
                ->whereIn('status', [
                    Application::REGISTRAR_APPROVED,
                    Application::PAID_CONFIRMED,
                    Application::PRODUCTION_QUEUE,
                    Application::CERT_GENERATED,
                    Application::PRINTED,
                    Application::ISSUED,
                ])
                ->count(),
            'pending' => (clone $baseQuery)->where('is_draft', false)
                ->whereIn('status', [
                    Application::SUBMITTED,
                    Application::SUBMITTED_WITH_APP_FEE,
                    Application::OFFICER_REVIEW,
                    Application::REGISTRAR_REVIEW,
                    Application::ACCOUNTS_REVIEW,
                    Application::APPROVED_AWAITING_PAYMENT,
                    Application::AWAITING_ACCOUNTS_VERIFICATION,
                    Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
                ])
                ->count(),
            'renewals_due' => 0,
        ];

        $recentApplications = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $notices = \App\Models\Notice::where('is_published', true)
            ->whereIn('target_portal', ['mediahouse', 'both'])
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $events = \App\Models\Event::where('is_published', true)
            ->whereIn('target_portal', ['mediahouse', 'both'])
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        $latestRecord = \App\Models\RegistrationRecord::where('contact_user_id', $user->id)
            ->orderByDesc('issued_at')
            ->first();

        $levyReminders = \App\Models\Reminder::where('target_type', 'media_house')
            ->where('target_id', $user->id)
            ->whereNull('acknowledged_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('portal.mediahouse.dashboard', compact('stats', 'recentApplications', 'notices', 'events', 'latestRecord', 'levyReminders'));
    }

    public function newRegistration()
    {
        $draft = Application::where('applicant_user_id', Auth::id())
            ->where('application_type', 'registration')
            ->where('request_type', 'new')
            ->where('is_draft', true)
            ->first();

        return view('portal.mediahouse.newregistration', compact('draft'));
    }


    /**
     * If a registration application was returned for correction, allow the applicant to edit and resubmit.
     */
    public function editCorrection(Application $application)
    {
        $userId = Auth::id();
        abort_unless($userId && $application->applicant_user_id === $userId, 403);
        abort_unless($application->application_type === 'registration', 404);
        abort_unless($application->status === Application::CORRECTION_REQUESTED, 403);

        // Load existing docs
        $application->setRelation('documents', ApplicationDocument::where('application_id', $application->id)->get());

        $draft = $application;
        return view('portal.mediahouse.newregistration', compact('draft'));
    }

    public function resubmitCorrection(Request $request, Application $application)
    {
        $userId = Auth::id();
        abort_unless($userId && $application->applicant_user_id === $userId, 403);
        abort_unless($application->application_type === 'registration', 404);
        abort_unless($application->status === Application::CORRECTION_REQUESTED, 403);

        $validated = $request->validate([
            'collection_region' => 'required|in:harare,bulawayo,mutare,masvingo',
            'form_data' => 'required',
            'documents' => 'sometimes|array',
            'documents.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,zip',
        ]);

        $formDataRaw = $validated['form_data'];
        $formData = is_array($formDataRaw) ? $formDataRaw : (json_decode((string) $formDataRaw, true) ?: null);
        if (!is_array($formData)) {
            return response()->json(['success' => false, 'message' => 'Invalid form data payload.'], 422);
        }

        $scope = $formData['registration_scope'] ?? $application->journalist_scope;

        $application->update([
            'collection_region' => $validated['collection_region'],
            'form_data' => $formData,
            'journalist_scope' => $scope,
            'is_draft' => false,
            'status' => Application::SUBMITTED,
            'submitted_at' => now(),
        ]);

        $this->storeUploadedDocuments($application, $request);

        $this->mergeIntoProfile([
            'organization_name' => $formData['org_name'] ?? $formData['rep_office_name'] ?? null,
            'head_office_address' => $formData['org_head_office'] ?? $formData['rep_office_address'] ?? $formData['contact_address'] ?? null,
            'mass_media_activities' => $formData['mass_media_activity'] ?? $formData['rep_office_activities'] ?? $formData['foreign_media_type'] ?? null,
            'website' => $formData['website'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application updated and resubmitted for review.',
            'reference' => $application->reference,
        ]);
    }

    public function saveDraft(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'collection_region' => 'required|in:harare,bulawayo,mutare,masvingo',
        'form_data' => 'required',
        'documents' => 'sometimes|array',
        'documents.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,zip',
    ]);

    $raw = $validated['form_data'];
    $formData = is_array($raw) ? $raw : (json_decode((string) $raw, true) ?: null);

    if (!is_array($formData)) {
        return response()->json(['success' => false, 'message' => 'Invalid form data payload.'], 422);
    }

    $draft = Application::where('applicant_user_id', $user->id)
        ->where('application_type', 'registration')
        ->where('request_type', 'new')
        ->where('is_draft', true)
        ->first();

    $reference = $draft?->reference ?: ('DRAFT-AP1-' . now()->format('Y') . '-' . Str::random(6));

    $draft = Application::updateOrCreate(
        [
            'applicant_user_id' => $user->id,
            'application_type'  => 'registration',
            'request_type'      => 'new',
            'is_draft'          => true,
        ],
        [
            'reference'         => $reference,
            'collection_region' => $validated['collection_region'],
            'form_data'         => $formData,
            'journalist_scope'  => $formData['registration_scope'] ?? null,
            'status'            => Application::DRAFT,
        ]
    );

    // Save draft documents (update or create per doc_type)
    if ($request->hasFile('documents')) {
        foreach ((array) $request->file('documents') as $docType => $file) {
            if (!$file) continue;

            $sha256 = null;
            try { $sha256 = hash_file('sha256', $file->getRealPath()); } catch (\Throwable $e) {}

            if ($sha256) {
                $exists = \App\Models\ApplicationDocument::where('application_id', $draft->id)
                    ->where('sha256', $sha256)
                    ->exists();
                if ($exists) continue;
            }

            $path = $file->store("documents/{$draft->id}", 'public');

            ApplicationDocument::updateOrCreate(
                ['application_id' => $draft->id, 'doc_type' => (string) $docType],
                [
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'owner_id' => $user->id,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'sha256' => $sha256,
                    'file_data' => null,
                    'status' => 'uploaded'
                ]
            );
        }
    }

    $this->mergeIntoProfile([
        'organization_name' => $formData['org_name'] ?? $formData['rep_office_name'] ?? null,
        'head_office_address' => $formData['org_head_office'] ?? $formData['rep_office_address'] ?? $formData['contact_address'] ?? null,
        'mass_media_activities' => $formData['mass_media_activity'] ?? $formData['rep_office_activities'] ?? $formData['foreign_media_type'] ?? null,
        'website' => $formData['website'] ?? null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Draft saved successfully',
        'draft_id' => $draft->id,
    ]);
}


    public function submit(Request $request)
    {
        $this->ensureWindowOpen('registration');
        $user = Auth::user();

        $validated = $request->validate([
            'collection_region' => 'required|in:harare,bulawayo,mutare,masvingo',
            'form_data' => 'required',
            'documents' => 'sometimes|array',
            'documents.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,zip',
            'app_fee_type' => 'nullable|in:paynow_ref,proof',
            'app_fee_paynow_ref' => 'nullable|string|max:100',
            'app_fee_first_name' => 'nullable|string|max:100',
            'app_fee_last_name' => 'nullable|string|max:100',
            'app_fee_payment_date' => 'nullable|date',
            'app_fee_amount_paid' => 'nullable|numeric|min:0',
            'app_fee_bank_name' => 'nullable|string|max:120',
            'app_fee_proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $formDataRaw = $validated['form_data'];
        $formData = is_array($formDataRaw) ? $formDataRaw : (json_decode((string) $formDataRaw, true) ?: null);

        if (!is_array($formData)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid form data payload.',
            ], 422);
        }

        $appFeeType = $validated['app_fee_type'] ?? null;
        if (!$appFeeType) {
            return response()->json([
                'success' => false,
                'message' => 'Application fee is required. Please provide a PayNow reference or upload proof of payment.',
            ], 422);
        }

        $reference = 'ZMC-AP1-' . now()->format('Y') . '-' . str_pad(
            Application::where('application_type', 'registration')->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        $existingDraft = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'registration')
            ->where('request_type', 'new')
            ->where('is_draft', true)
            ->first();

        $scope = $formData['registration_scope'] ?? 'local';

        $appFeeFields = ['payment_stage' => 'application_fee'];

        if ($appFeeType === 'paynow_ref') {
            $appFeeFields['paynow_ref_submitted'] = $validated['app_fee_paynow_ref'] ?? null;
        } elseif ($appFeeType === 'proof') {
            $appFeeFields['proof_payer_first_name'] = $validated['app_fee_first_name'] ?? null;
            $appFeeFields['proof_payer_last_name'] = $validated['app_fee_last_name'] ?? null;
            $appFeeFields['proof_payment_date'] = $validated['app_fee_payment_date'] ?? null;
            $appFeeFields['proof_amount_paid'] = $validated['app_fee_amount_paid'] ?? null;
            $appFeeFields['proof_bank_name'] = $validated['app_fee_bank_name'] ?? null;

            if ($request->hasFile('app_fee_proof_file')) {
                $proofFile = $request->file('app_fee_proof_file');
                $proofPath = $proofFile->store('payment_proofs', 'public');
                $appFeeFields['payment_proof_path'] = $proofPath;
                $appFeeFields['payment_proof_uploaded_at'] = now();
                $appFeeFields['proof_status'] = 'submitted';
                $appFeeFields['proof_original_name'] = $proofFile->getClientOriginalName();
                $appFeeFields['proof_mime'] = $proofFile->getMimeType();
            }
        }

        $baseFields = array_merge([
            'reference' => $reference,
            'collection_region' => $validated['collection_region'],
            'form_data' => $formData,
            'journalist_scope' => $scope,
            'is_draft' => false,
            'status' => Application::SUBMITTED_WITH_APP_FEE,
            'submitted_at' => now(),
        ], $appFeeFields);

        if ($existingDraft) {
            $existingDraft->update($baseFields);
            $application = $existingDraft;
        } else {
            $application = Application::create(array_merge([
                'applicant_user_id' => $user->id,
                'application_type' => 'registration',
                'request_type' => 'new',
            ], $baseFields));
        }

        $this->storeUploadedDocuments($application, $request);

        $this->mergeIntoProfile([
            'organization_name' => $formData['org_name'] ?? $formData['rep_office_name'] ?? null,
            'head_office_address' => $formData['org_head_office'] ?? $formData['rep_office_address'] ?? $formData['contact_address'] ?? null,
            'mass_media_activities' => $formData['mass_media_activity'] ?? $formData['rep_office_activities'] ?? $formData['foreign_media_type'] ?? null,
            'website' => $formData['website'] ?? null,
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Application submitted successfully with application fee.',
            'reference'      => $application->reference,
            'application_id' => $application->id,
        ]);
    }

    /**
     * AP5 Draft (Media House): allow multiple drafts per user.
     * Every click creates a new draft record (no overwrite).
     */
    public function saveDraftAp5(Request $request)
    {
        $this->ensureWindowOpen('registration');
        $user = Auth::user();

        $validated = $request->validate([
            'request_type' => 'required|in:renewal,replacement',
            'draft_reference' => 'nullable|string|max:64',
            'current_step' => 'nullable|integer|min:1|max:4',
            'registration_number' => 'nullable|string|max:200',
            'collection_region' => 'nullable|string|max:150',

            'previous_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'official_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'affidavit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'police_report' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $year = now()->format('Y');
        $reference = 'DRAFT-AP5-' . $year . '-' . Str::upper(Str::random(6));

        $prevScope = null;
        if (!empty($validated['registration_number'])) {
            $prevScope = Application::where('reference', $validated['registration_number'])
                ->where('application_type', 'registration')
                ->value('journalist_scope');
        }

        $application = Application::create([
            'reference' => $reference,
            'applicant_user_id' => $user->id,
            'application_type' => 'registration',
            'request_type' => $validated['request_type'],
            'journalist_scope' => $prevScope ?? 'local',
            'collection_region' => $validated['collection_region'] ?? 'harare',
            'form_data' => $request->except(['_token', 'previous_certificate', 'official_letter', 'affidavit', 'police_report', 'payment_proof']),
            'is_draft' => true,
            'status' => Application::DRAFT,
        ]);

        $docMap = [];
        if ($validated['request_type'] === 'renewal') {
            if ($request->hasFile('previous_certificate')) $docMap['previous_certificate'] = 'previous_certificate';
            if ($request->hasFile('official_letter')) $docMap['official_letter'] = 'official_letter';
        } else {
            if ($request->hasFile('affidavit')) $docMap['affidavit'] = 'affidavit';
            if ($request->hasFile('police_report')) $docMap['police_report'] = 'police_report';
        }
        if ($request->hasFile('payment_proof')) $docMap['payment_proof'] = 'payment_proof';

        foreach ($docMap as $field => $docType) {
            if (!$request->hasFile($field)) continue;
            $file = $request->file($field);
            $path = $file->store('documents/' . $application->id, 'public');

            $sha256 = null;
            try { $sha256 = hash_file('sha256', $file->getRealPath()); } catch (\Throwable $e) {}

            ApplicationDocument::updateOrCreate(
                ['application_id' => $application->id, 'doc_type' => $docType],
                [
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'owner_id' => $user->id,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'sha256' => $sha256,
                    'file_data' => null,
                    'status' => 'pending',
                ]
            );
        }

        $this->mergeIntoProfile([
            'organization_name' => $request->input('entity_name') ?? null,
            'head_office_address' => $request->input('head_office') ?? null,
            'website' => $request->input('website') ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully.',
            'reference' => $application->reference,
        ]);
    }

    public function submitAp5(Request $request)
    {
        $this->ensureWindowOpen('registration');
        $user = Auth::user();

        $validated = $request->validate([
            'request_type' => 'required|in:renewal,replacement',
            'current_step' => 'nullable|integer|min:1|max:4',

            'registration_number' => 'required|string|max:200',
            'has_changes' => 'nullable|in:yes,no',
            'changes_data' => 'nullable',
            'collection_region' => 'nullable|in:harare,bulawayo,mutare,masvingo',

            'replacement_reason' => 'required_if:request_type,replacement|in:lost,damaged,stolen',

            'previous_certificate' => 'required_if:request_type,renewal|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'official_letter' => 'required_if:request_type,renewal|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'affidavit' => 'required_if:request_type,replacement|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'police_report' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'payment_method' => 'nullable|in:paynow,proof_upload',
            'paynow_reference' => 'nullable|string|max:120',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if (($validated['request_type'] ?? '') === 'replacement' && ($validated['replacement_reason'] ?? '') === 'stolen') {
            if (!$request->hasFile('police_report')) {
                return response()->json(['success' => false, 'message' => 'Police report is required for stolen certificates.'], 422);
            }
        }

        $changesData = $request->input('changes_data');
        if (is_string($changesData)) {
            $changesData = json_decode($changesData, true);
        }

        $year = now()->format('Y');
        $prefix = "ZMC-AP5-{$year}-";

        $lastRef = Application::where('reference', 'like', $prefix . '%')
            ->where('reference', 'not like', 'DRAFT%')
            ->orderBy('reference', 'desc')
            ->value('reference');

        $nextNum = $lastRef ? ((int) substr($lastRef, -4) + 1) : 1;
        $reference = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $prevScope = Application::where('reference', $validated['registration_number'] ?? '')
            ->where('application_type', 'registration')
            ->value('journalist_scope');

        $status = ($validated['request_type'] === 'renewal')
            ? Application::AWAITING_ACCOUNTS_VERIFICATION
            : Application::SUBMITTED;

        $formPayload = $request->except(['_token', 'previous_certificate', 'official_letter', 'affidavit', 'police_report', 'payment_proof']);
        $formPayload['changes_data'] = $changesData;

        $application = Application::create([
            'reference' => $reference,
            'applicant_user_id' => $user->id,
            'application_type' => 'registration',
            'request_type' => $validated['request_type'],
            'journalist_scope' => $prevScope ?? 'local',
            'collection_region' => $validated['collection_region'] ?? 'harare',
            'form_data' => $formPayload,
            'is_draft' => false,
            'status' => $status,
            'submitted_at' => now(),
        ]);

        $docMap = [];

        if ($validated['request_type'] === 'renewal') {
            if ($request->hasFile('previous_certificate')) $docMap['previous_certificate'] = 'previous_certificate';
            if ($request->hasFile('official_letter')) $docMap['official_letter'] = 'official_letter';
        } else {
            if ($request->hasFile('affidavit')) $docMap['affidavit'] = 'affidavit';
            if ($request->hasFile('police_report')) $docMap['police_report'] = 'police_report';
        }

        if ($request->hasFile('payment_proof')) $docMap['payment_proof'] = 'payment_proof';

        foreach ($docMap as $field => $docType) {
            if (!$request->hasFile($field)) continue;

            $file = $request->file($field);
            $path = $file->store('documents/' . $application->id, 'public');

            $sha256 = null;
            try { $sha256 = hash_file('sha256', $file->getRealPath()); } catch (\Throwable $e) {}

            ApplicationDocument::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'doc_type' => $docType,
                ],
                [
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'owner_id' => $user->id,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'sha256' => $sha256,
                    'file_data' => null,
                    'status' => 'pending',
                ]
            );
        }

        if (!empty($validated['paynow_reference'])) {
            $application->update(['paynow_ref_submitted' => $validated['paynow_reference']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'AP5 submitted successfully',
            'reference' => $application->reference,
            'application_id' => $application->id,
        ]);
    }

    private function storeUploadedDocuments(Application $application, Request $request): void
    {
        if (!$request->hasFile('documents')) return;

        $user = Auth::user();

        foreach ((array) $request->file('documents') as $docType => $file) {
            if (!$file) continue;

            $path = $file->store("documents/{$application->id}", 'public');

            $sha256 = null;
            try { $sha256 = hash_file('sha256', $file->getRealPath()); } catch (\Throwable $e) {}

            ApplicationDocument::create([
                'application_id' => $application->id,
                'doc_type' => (string) $docType,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'owner_id' => $user?->id,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'sha256' => $sha256,
                'file_data' => null,
                'status' => 'pending',
            ]);
        }
    }

    public function renewals(Request $request)
    {
        $user = Auth::user();

        $drafts = Application::where('applicant_user_id', $user->id)
            ->where('is_draft', true)
            ->where('application_type', 'registration')
            ->where('request_type', 'renewal')
            ->orderByDesc('created_at')
            ->get();

        $draft = null;
        if ($request->filled('draft')) {
            $draft = $drafts->firstWhere('reference', $request->input('draft'));
        }

        return view('portal.mediahouse.renewals', compact('drafts', 'draft'));
    }

    public function replacement(Request $request)
    {
        $user = Auth::user();

        $drafts = Application::where('applicant_user_id', $user->id)
            ->where('is_draft', true)
            ->where('application_type', 'registration')
            ->where('request_type', 'replacement')
            ->orderByDesc('created_at')
            ->get();

        $draft = null;
        if ($request->filled('draft')) {
            $draft = $drafts->firstWhere('reference', $request->input('draft'));
        }

        return view('portal.mediahouse.replacement', compact('drafts', 'draft'));
    }

    public function saveDraftReplacement(Request $request)
    {
        // Use the same logic as saveDraftAp5 but force request_type to 'replacement'
        $request->merge(['request_type' => 'replacement']);
        return $this->saveDraftAp5($request);
    }

    public function submitReplacement(Request $request)
    {
        // Use the same logic as submitAp5 but force request_type to 'replacement'
        $request->merge(['request_type' => 'replacement']);
        return $this->submitAp5($request);
    }


    public function lookupRegistrationNumber(string $number)
    {
        $application = Application::where('reference', $number)
            ->where('application_type', 'registration')
            ->where('is_draft', false)
            ->first();

        if (!$application) {
            $record = \App\Models\AccreditationRecord::where('record_number', $number)
                ->orWhere('certificate_no', $number)
                ->first();
            if ($record) {
                $application = $record->application;
            }
        }

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'No record found for this registration number.'], 404);
        }

        $formData = $application->form_data ?? [];

        return response()->json([
            'success' => true,
            'record' => [
                'reference' => $application->reference,
                'entity_name' => $formData['org_name'] ?? $formData['entity_name'] ?? $formData['rep_office_name'] ?? '',
                'head_office' => $formData['org_head_office'] ?? $formData['head_office'] ?? $formData['rep_office_address'] ?? '',
                'postal_address' => $formData['postal_address'] ?? $formData['org_postal_address'] ?? '',
                'contact_name' => $formData['contact_name'] ?? '',
                'contact_phone' => $formData['contact_phone'] ?? '',
                'contact_email' => $formData['contact_email'] ?? '',
                'contact_address' => $formData['contact_address'] ?? '',
                'collection_region' => $application->collection_region ?? '',
                'journalist_scope' => $application->journalist_scope ?? '',
                'application_type' => $application->application_type ?? '',
                'status' => $application->status ?? '',
            ],
        ]);
    }

    public function payments()
    {
        $user = Auth::user();
        $applications = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'registration')
            ->where(function($q) use ($user) {
                $q->whereNotNull('payment_status')
                  ->orWhereIn('status', [
                      \App\Models\Application::AWAITING_ACCOUNTS_VERIFICATION,
                      \App\Models\Application::PAYMENT_VERIFIED,
                      \App\Models\Application::PAID_CONFIRMED,
                      \App\Models\Application::ACCOUNTS_REVIEW,
                      \App\Models\Application::APPROVED_AWAITING_PAYMENT,
                  ]);
            })
            ->orderByDesc('submitted_at')
            ->get();

        return view('portal.mediahouse.payments', compact('applications'));
    }

    public function notices()
    {
        $notices = \App\Models\Notice::where('is_published', true)
            ->whereIn('target_portal', ['mediahouse', 'both'])
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $events = \App\Models\Event::where('is_published', true)
            ->whereIn('target_portal', ['mediahouse', 'both'])
            ->orderBy('starts_at')
            ->limit(20)
            ->get();

        $news = \App\Models\News::where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();

        return view('portal.mediahouse.notices', compact('notices', 'events', 'news'));
    }

    public function howto()
    {
        return view('portal.mediahouse.howto');
    }

    public function requirements()
    {
        return view('portal.mediahouse.requirements');
    }

    public function profile()
    {
        return view('portal.mediahouse.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $payload = $request->validate([
            'organization_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'phone2' => ['required', 'string', 'max:20'],
            'head_office_address' => ['nullable', 'string', 'max:500'],
            'mass_media_activities' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.facebook' => ['nullable', 'url', 'max:255'],
            'social_media.twitter' => ['nullable', 'url', 'max:255'],
            'social_media.instagram' => ['nullable', 'url', 'max:255'],
            'social_media.youtube' => ['nullable', 'url', 'max:255'],
            'social_media.tiktok' => ['nullable', 'url', 'max:255'],
            'social_media.website' => ['nullable', 'url', 'max:255'],
        ]);

        $updateData = [
            'phone_number' => $payload['phone_number'],
            'phone2' => $payload['phone2'],
            'social_media' => $payload['social_media'] ?? [],
        ];

        if (!empty($payload['email']) && $payload['email'] !== $user->email) {
            $request->validate(['email' => 'unique:users,email']);
            $updateData['email'] = $payload['email'];
        }

        $user->update($updateData);

        $profile = $user->profile_data ?? [];
        if (!empty($payload['organization_name'])) {
            $profile['organization_name'] = $payload['organization_name'];
        }
        if (!empty($payload['head_office_address'])) {
            $profile['head_office_address'] = $payload['head_office_address'];
        }
        if (!empty($payload['mass_media_activities'])) {
            $profile['mass_media_activities'] = $payload['mass_media_activities'];
        }
        $user->update(['profile_data' => $profile]);

        return back()->with('success', 'Organization profile updated successfully.');
    }

    public function settings()
    {
        return view('portal.mediahouse.settings');
    }

    public function communication()
    {
        return view('portal.mediahouse.communication');
    }

    public function deleteDraft(Application $application)
    {
        $userId = Auth::id();
        abort_unless($userId && $application->applicant_user_id === $userId, 403);
        abort_unless($application->is_draft, 403);

        $application->documents()->each(function ($doc) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
        });

        $application->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Draft deleted successfully.'
        ]);
    }

    public function withdraw(Application $application)
    {
        $userId = Auth::id();
        abort_unless($userId && $application->applicant_user_id === $userId, 403);
        abort_unless(!$application->is_draft, 403);
        
        $allowed = [
            Application::SUBMITTED, 
            Application::OFFICER_REVIEW, 
            Application::REGISTRAR_REVIEW, 
            Application::ACCOUNTS_REVIEW
        ];
        abort_unless(in_array($application->status, $allowed), 403);

        $application->update([
            'status' => Application::WITHDRAWN,
            'is_draft' => true,
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Application withdrawn and moved back to drafts.'
        ]);
    }

    private function ensureWindowOpen(string $windowKey): void
    {
        $win = (array) MasterSettings::get("portal_specific.public.{$windowKey}_window", ['open' => null, 'close' => null]);
        $open = $win['open'] ?? null;
        $close = $win['close'] ?? null;

        if (!$open && !$close) {
            return;
        }

        $now = now();
        if ($open && $now->lt(now()->parse($open))) {
            abort(403, ucfirst($windowKey) . ' window is not open yet.');
        }
        if ($close && $now->gt(now()->parse($close))) {
            abort(403, ucfirst($windowKey) . ' window is closed.');
        }
    }
}
