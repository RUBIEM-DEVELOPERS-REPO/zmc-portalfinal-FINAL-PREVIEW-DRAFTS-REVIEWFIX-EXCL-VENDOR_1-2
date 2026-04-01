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
                    Application::OFFICER_REVIEW,
                    Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
                    Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR,
                    Application::REGISTRAR_REVIEW,
                    Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT,
                    Application::ACCOUNTS_REVIEW,
                    Application::AWAITING_ACCOUNTS_VERIFICATION,
                    Application::REGISTRATION_FEE_AWAITING_VERIFICATION,
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
 
         $registration = \App\Models\RegistrationRecord::where('contact_user_id', $user->id)
              ->where('status', 'active')
              ->orderByDesc('expires_at')
              ->first();
 
          $yearsRemaining = null;
          if ($registration && $registration->expires_at) {
              $now = now();
              if ($registration->expires_at->isPast()) {
                  $yearsRemaining = 0;
              } else {
                  $yearsRemaining = $now->diffInDays($registration->expires_at) / 365.25;
              }
          }
 
         return view('portal.mediahouse.dashboard', compact('stats', 'recentApplications', 'notices', 'events', 'registration', 'yearsRemaining'));
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
        'collection_region' => 'nullable|in:harare,bulawayo,mutare,masvingo',
        'form_data'         => 'required',
        'documents'         => 'sometimes|array',
        'documents.*'       => 'file|max:10240|mimes:pdf,jpg,jpeg,png,zip',
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
            'collection_region' => $validated['collection_region'] ?? $draft?->collection_region ?? 'harare',
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

            if ($sha256 && \Illuminate\Support\Facades\Schema::hasColumn('application_documents', 'sha256')) {
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
                    'mime' => method_exists($file, 'getMimeType') ? $file->getMimeType() : null,
                    'size' => method_exists($file, 'getSize') ? $file->getSize() : null,
                    'sha256' => $sha256,
                    'status' => 'uploaded'
                ]
            );

            if (\Illuminate\Support\Facades\Schema::hasTable('files')) {
                \App\Models\FileRecord::create([
                    'owner_id' => $user->id,
                    'application_id' => $draft->id,
                    'path' => $path,
                    'mime' => method_exists($file, 'getMimeType') ? $file->getMimeType() : null,
                    'size' => method_exists($file, 'getSize') ? $file->getSize() : null,
                    'sha256' => $sha256,
                ]);
            }
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
        ]);

        $formDataRaw = $validated['form_data'];
        $formData = is_array($formDataRaw) ? $formDataRaw : (json_decode((string) $formDataRaw, true) ?: null);

        if (!is_array($formData)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid form data payload.',
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

        // scope and draft handling
        $scope = $formData['registration_scope'] ?? 'local';

        if ($existingDraft) {
            $existingDraft->update([
                'reference' => $reference,
                'collection_region' => $validated['collection_region'],
                'form_data' => $formData,
                'journalist_scope' => $scope,
                'is_draft' => false,
                'status' => Application::SUBMITTED,
                'submitted_at' => now(),
            ]);
            $application = $existingDraft;
        } else {
            $application = Application::create([
                'reference' => $reference,
                'applicant_user_id' => $user->id,
                'application_type' => 'registration',
                'request_type' => 'new',
                'journalist_scope' => $scope,
                'collection_region' => $validated['collection_region'],
                'form_data' => $formData,
                'is_draft' => false,
                'status' => Application::SUBMITTED,
                'submitted_at' => now(),
            ]);
        }

        $this->storeUploadedDocuments($application, $request);

        $this->mergeIntoProfile([
            'organization_name' => $formData['org_name'] ?? $formData['rep_office_name'] ?? null,
            'head_office_address' => $formData['org_head_office'] ?? $formData['rep_office_address'] ?? $formData['contact_address'] ?? null,
            'mass_media_activities' => $formData['mass_media_activity'] ?? $formData['rep_office_activities'] ?? $formData['foreign_media_type'] ?? null,
            'website' => $formData['website'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'reference' => $application->reference,
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
            'current_step' => 'nullable|integer|min:1|max:5',
            'declaration_confirmed' => 'required|in:1',
            // optional for draft
            'previous_reference' => 'nullable|string|max:150',
            'collection_region' => 'nullable|in:harare,bulawayo,mutare,masvingo',

            'proof_of_payment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'current_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'official_request_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'supporting_docs' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'affidavit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'police_report' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $year = now()->format('Y');
        $reference = 'DRAFT-AP5-' . $year . '-' . Str::upper(Str::random(6));

        // Carry local/foreign scope from previous reference if available
        $prevScope = null;
        if (!empty($validated['previous_reference'])) {
            $prevScope = Application::where('reference', $validated['previous_reference'])
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
            'form_data' => $request->except(['_token']),
            'is_draft' => true,
            'status' => Application::DRAFT,
        ]);

        // Store docs if any were uploaded
        $docMap = ['proof_of_payment' => 'proof_of_payment'];
        if ($validated['request_type'] === 'renewal') {
            if ($request->hasFile('current_certificate')) $docMap['current_certificate'] = 'current_certificate';
            if ($request->hasFile('official_request_letter')) $docMap['official_request_letter'] = 'official_request_letter';
            if ($request->hasFile('supporting_docs')) $docMap['supporting_docs'] = 'supporting_docs';
        } else {
            if ($request->hasFile('affidavit')) $docMap['affidavit'] = 'affidavit';
            if ($request->hasFile('police_report')) $docMap['police_report'] = 'police_report';
        }

        foreach ($docMap as $field => $docType) {
            if (!$request->hasFile($field)) continue;
            $file = $request->file($field);
            $path = $file->store('documents/' . $application->id, 'public');

            ApplicationDocument::updateOrCreate(
                ['application_id' => $application->id, 'doc_type' => $docType],
                ['file_path' => $path, 'original_name' => $file->getClientOriginalName(), 'status' => 'pending']
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
            'current_step' => 'nullable|integer|min:1|max:5',

            'contact_name' => 'required|string|max:150',
            'contact_phone' => 'required|string|max:50',
            'contact_address' => 'required|string|max:500',
            'contact_email' => 'required|email|max:150',

            'entity_name' => 'required|string|max:200',
            'previous_reference' => 'required|string|max:120',
            'head_office' => 'required|string|max:500',
            'postal_address' => 'required|string|max:500',
            'changes' => 'required|in:no,yes',
            'changes_details' => 'nullable|string|max:2000',
            'collection_region' => 'required|in:harare,bulawayo,mutare,masvingo',

            'replacement_reason' => 'required_if:request_type,replacement|in:lost,damaged,stolen',

            'proof_of_payment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
 
            'current_certificate' => 'required_if:request_type,renewal|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'official_request_letter' => 'required_if:request_type,renewal|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'supporting_docs' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:10240',
 
            'affidavit' => 'required_if:request_type,replacement|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'police_report' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if (($validated['request_type'] ?? '') === 'replacement' && ($validated['replacement_reason'] ?? '') === 'stolen') {
            if (!$request->hasFile('police_report')) {
                return response()->json(['success' => false, 'message' => 'Police report is required for stolen certificates.'], 422);
            }
        }

        $year = now()->format('Y');
        $prefix = "ZMC-AP5-{$year}-";

        $lastRef = Application::where('reference', 'like', $prefix . '%')
            ->where('reference', 'not like', 'DRAFT%')
            ->orderByRaw("CAST(SUBSTR(reference, -4) AS INTEGER) DESC")
            ->value('reference');

        $nextNum = $lastRef ? ((int) substr($lastRef, -4) + 1) : 1;
        $reference = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // Carry local/foreign scope from the previous registration reference (for consistent filtering/reporting)
        $prevScope = Application::where('reference', $validated['previous_reference'] ?? '')
            ->where('application_type', 'registration')
            ->value('journalist_scope');

        $application = Application::create([
            'reference' => $reference,
            'applicant_user_id' => $user->id,
            'application_type' => 'registration',
            'request_type' => $validated['request_type'],
            'journalist_scope' => $prevScope ?? 'local',
            'collection_region' => $validated['collection_region'],
            'form_data' => $request->except(['_token']),
            'is_draft' => false,
            'status' => Application::SUBMITTED,
            'submitted_at' => now(),
        ]);

        $docMap = [
            'proof_of_payment' => 'proof_of_payment',
        ];

        if ($validated['request_type'] === 'renewal') {
            $docMap['current_certificate'] = 'current_certificate';
            $docMap['official_request_letter'] = 'official_request_letter';
            if ($request->hasFile('supporting_docs')) $docMap['supporting_docs'] = 'supporting_docs';
        } else {
            $docMap['affidavit'] = 'affidavit';
            if ($request->hasFile('police_report')) $docMap['police_report'] = 'police_report';
        }

        foreach ($docMap as $field => $docType) {
            if (!$request->hasFile($field)) continue;

            $file = $request->file($field);
            $path = $file->store('documents/' . $application->id, 'public');

            ApplicationDocument::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'doc_type' => $docType,
                ],
                [
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'status' => 'pending',
                ]
            );
        }

        $this->mergeIntoProfile([
            'organization_name' => $validated['entity_name'] ?? null,
            'head_office_address' => $validated['head_office'] ?? null,
            'website' => $validated['website'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'AP5 submitted successfully',
            'reference' => $application->reference,
        ]);
    }

    private function storeUploadedDocuments(Application $application, Request $request): void
    {
        if (!$request->hasFile('documents')) return;

        foreach ((array) $request->file('documents') as $docType => $file) {
            if (!$file) continue;

            $path = $file->store("documents/{$application->id}", 'public');

            ApplicationDocument::create([
                'application_id' => $application->id,
                'doc_type' => (string) $docType,
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
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
            ->whereIn('request_type', ['renewal', 'replacement'])
            ->orderByDesc('created_at')
            ->get();

        $draft = null;
        if ($request->filled('draft')) {
            $draft = $drafts->firstWhere('reference', $request->input('draft'));
        }

        return view('portal.mediahouse.renewals', compact('drafts', 'draft'));
    }

    public function payments()
    {
        return view('portal.mediahouse.payments');
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

        return view('portal.mediahouse.notices', compact('notices', 'events'));
    }

    public function howto()
    {
        return view('portal.mediahouse.howto');
    }

    public function profile()
    {
        return view('portal.mediahouse.profile');
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

    /**
     * Download official letter for approved media house application
     */
    public function downloadOfficialLetter(Application $application)
    {
        $user = Auth::user();

        // Verify ownership
        if ($application->applicant_user_id !== $user->id) {
            abort(403, 'Unauthorized access to this application.');
        }

        // Verify application type
        if ($application->application_type !== 'registration') {
            abort(404, 'Official letter not available for this application type.');
        }

        // Verify official letter exists
        if (!$application->officialLetter) {
            abort(404, 'Official letter not found for this application.');
        }

        $officialLetter = $application->officialLetter;

        // Verify file exists
        if (!$officialLetter->fileExists()) {
            abort(404, 'Official letter file not found.');
        }

        // Log download
        ActivityLogger::log('download_official_letter', $application, $application->status, $application->status, [
            'official_letter_id' => $officialLetter->id,
            'file_name' => $officialLetter->file_name,
        ]);

        // Return file download
        return \Storage::disk('public')->download(
            $officialLetter->file_path,
            $officialLetter->file_name
        );
    }

    /**
     * Submit application fee payment (PayNow)
     */
    public function submitApplicationFeePaynow(Request $request, Application $application)
    {
        $user = Auth::user();

        // Verify ownership
        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        // Verify application type
        if ($application->application_type !== 'registration') {
            return response()->json(['ok' => false, 'message' => 'Application fee only applies to media house registrations.'], 422);
        }

        $data = $request->validate([
            'paynow_reference' => ['required', 'string', 'max:255'],
        ]);

        // Create PaymentSubmission record
        $paymentSubmission = \App\Models\PaymentSubmission::create([
            'application_id' => $application->id,
            'payment_stage' => 'application_fee',
            'method' => 'PAYNOW',
            'reference' => $data['paynow_reference'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        ActivityLogger::log('submit_application_fee_paynow', $application, $application->status, $application->status, [
            'payment_submission_id' => $paymentSubmission->id,
            'reference' => $data['paynow_reference'],
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Application fee payment reference submitted successfully.',
        ]);
    }

    /**
     * Submit application fee payment (Proof Upload)
     */
    public function submitApplicationFeeProof(Request $request, Application $application)
    {
        $user = Auth::user();

        // Verify ownership
        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        // Verify application type
        if ($application->application_type !== 'registration') {
            return response()->json(['ok' => false, 'message' => 'Application fee only applies to media house registrations.'], 422);
        }

        $data = $request->validate([
            'payer_name' => ['nullable', 'string', 'max:200'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:255'],
            'proof_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $file = $request->file('proof_file');
        $path = $file->store('payment_proofs', 'public');
        $hash = hash_file('sha256', \Storage::disk('public')->path($path));

        // Create PaymentSubmission record
        $paymentSubmission = \App\Models\PaymentSubmission::create([
            'application_id' => $application->id,
            'payment_stage' => 'application_fee',
            'method' => 'PROOF_UPLOAD',
            'reference' => $data['reference'] ?? null,
            'amount' => $data['amount'],
            'currency' => 'USD',
            'status' => 'submitted',
            'submitted_at' => now(),
            'proof_path' => $path,
            'proof_metadata' => [
                'payer_name' => $data['payer_name'] ?? null,
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'file_name' => $file->getClientOriginalName(),
                'file_hash' => $hash,
            ],
        ]);

        ActivityLogger::log('submit_application_fee_proof', $application, $application->status, $application->status, [
            'payment_submission_id' => $paymentSubmission->id,
            'amount' => $data['amount'],
            'file_hash' => $hash,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Application fee proof uploaded successfully.',
        ]);
    }

    /**
     * Submit registration fee payment (PayNow)
     */
    public function submitRegistrationFeePaynow(Request $request, Application $application)
    {
        $user = Auth::user();

        // Verify ownership
        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        // Verify status
        if ($application->status !== Application::REGISTRAR_APPROVED_PENDING_REG_FEE) {
            return response()->json(['ok' => false, 'message' => 'Registration fee can only be submitted after Registrar approval.'], 422);
        }

        $data = $request->validate([
            'paynow_reference' => ['required', 'string', 'max:255'],
        ]);

        $from = $application->status;

        \DB::transaction(function() use ($application, $data, $from) {
            // Create PaymentSubmission record
            $paymentSubmission = \App\Models\PaymentSubmission::create([
                'application_id' => $application->id,
                'payment_stage' => 'registration_fee',
                'method' => 'PAYNOW',
                'reference' => $data['paynow_reference'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Transition to awaiting verification
            ApplicationWorkflow::transition(
                $application,
                Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
                'submit_registration_fee_paynow',
                ['payment_submission_id' => $paymentSubmission->id]
            );

            ActivityLogger::log('submit_registration_fee_paynow', $application, $from, $application->status, [
                'payment_submission_id' => $paymentSubmission->id,
                'reference' => $data['paynow_reference'],
            ]);
        });

        return response()->json([
            'ok' => true,
            'message' => 'Registration fee payment reference submitted successfully. Accounts will verify it shortly.',
        ]);
    }

    /**
     * Submit registration fee payment (Proof Upload)
     */
    public function submitRegistrationFeeProof(Request $request, Application $application)
    {
        $user = Auth::user();

        // Verify ownership
        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        // Verify status
        if ($application->status !== Application::REGISTRAR_APPROVED_PENDING_REG_FEE) {
            return response()->json(['ok' => false, 'message' => 'Registration fee can only be submitted after Registrar approval.'], 422);
        }

        $data = $request->validate([
            'payer_name' => ['nullable', 'string', 'max:200'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:255'],
            'proof_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $file = $request->file('proof_file');
        $path = $file->store('payment_proofs', 'public');
        $hash = hash_file('sha256', \Storage::disk('public')->path($path));

        $from = $application->status;

        \DB::transaction(function() use ($application, $data, $file, $path, $hash, $from) {
            // Create PaymentSubmission record
            $paymentSubmission = \App\Models\PaymentSubmission::create([
                'application_id' => $application->id,
                'payment_stage' => 'registration_fee',
                'method' => 'PROOF_UPLOAD',
                'reference' => $data['reference'] ?? null,
                'amount' => $data['amount'],
                'currency' => 'USD',
                'status' => 'submitted',
                'submitted_at' => now(),
                'proof_path' => $path,
                'proof_metadata' => [
                    'payer_name' => $data['payer_name'] ?? null,
                    'payment_date' => $data['payment_date'],
                    'amount' => $data['amount'],
                    'file_name' => $file->getClientOriginalName(),
                    'file_hash' => $hash,
                ],
            ]);

            // Transition to awaiting verification
            ApplicationWorkflow::transition(
                $application,
                Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
                'submit_registration_fee_proof',
                ['payment_submission_id' => $paymentSubmission->id]
            );

            ActivityLogger::log('submit_registration_fee_proof', $application, $from, $application->status, [
                'payment_submission_id' => $paymentSubmission->id,
                'amount' => $data['amount'],
                'file_hash' => $hash,
            ]);
        });

        return response()->json([
            'ok' => true,
            'message' => 'Registration fee proof uploaded successfully. Accounts will verify it shortly.',
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
