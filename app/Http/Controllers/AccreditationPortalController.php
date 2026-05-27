<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Support\MasterSettings;

class AccreditationPortalController extends Controller
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
        return redirect()->route('accreditation.home');
    }

    public function dashboard()
    {
        $user = Auth::user();

        $baseQuery = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'accreditation');

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
                    Application::CARD_GENERATED,
                    Application::PRINTED,
                    Application::ISSUED,
                ])
                ->count(),
            'pending' => (clone $baseQuery)->where('is_draft', false)
                ->whereIn('status', [
                    Application::SUBMITTED,
                    Application::OFFICER_REVIEW,
                    Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
                    Application::REGISTRAR_REVIEW,
                    Application::ACCOUNTS_REVIEW,
                    Application::AWAITING_ACCOUNTS_VERIFICATION,
                    Application::REGISTRAR_RAISED_FIX_REQUEST,
                    Application::RETURNED_TO_OFFICER,
                ])
                ->count(),
            'renewals_due' => 0,
        ];

        $recentApplications = (clone $baseQuery)
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $drafts = (clone $baseQuery)
            ->where('is_draft', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $notices = \App\Models\Notice::where('is_published', true)
            ->whereIn('target_portal', ['journalist', 'both'])
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $events = \App\Models\Event::where('is_published', true)
            ->whereIn('target_portal', ['journalist', 'both'])
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        $reminders = collect();
        if (Schema::hasTable('reminders')) {
            $reminders = \App\Models\Reminder::active()
                ->forUser($user->id)
                ->whereDoesntHave('reads', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->whereNotNull('acknowledged_at');
                })
                ->latest()
                ->limit(5)
                ->get();
        }

        return view('portal.accreditation.dashboard', compact('stats', 'recentApplications', 'drafts', 'notices', 'events', 'reminders'));
    }

    public function new()
    {
        $draft = Application::where('applicant_user_id', Auth::id())
            ->where('application_type', 'accreditation')
            ->where('request_type', 'new')
            ->where('is_draft', true)
            ->first();

        // IMPORTANT: load docs so blade review can show "saved" documents
        if ($draft) {
            $draft->setRelation('documents', ApplicationDocument::where('application_id', $draft->id)->get());
        }

        return view('portal.accreditation.new', compact('draft'));
    }

    /**
     * If an application was returned for correction, allow the applicant to edit and resubmit.
     */
    public function editCorrection(Application $application)
    {
        $userId = Auth::id();
        abort_unless($userId && $application->applicant_user_id === $userId, 403);
        abort_unless($application->application_type === 'accreditation', 404);
        abort_unless($application->status === Application::CORRECTION_REQUESTED, 403);

        // Load docs for review section
        $application->setRelation('documents', ApplicationDocument::where('application_id', $application->id)->get());

        // Reuse the existing "new" form view (it already supports editing via $draft)
        $draft = $application;
        return view('portal.accreditation.new', compact('draft'));
    }

    public function resubmitCorrection(Request $request, Application $application)
    {
        $this->ensureWindowOpen('accreditation');
        $userId = Auth::id();
        abort_unless($userId && $application->applicant_user_id === $userId, 403);
        abort_unless($application->application_type === 'accreditation', 404);
        abort_unless($application->status === Application::CORRECTION_REQUESTED, 403);

        // form_data may arrive as JSON string
        $rawFormData = $request->input('form_data');
        $formData = $rawFormData;
        if (is_string($rawFormData)) {
            $decoded = json_decode($rawFormData, true);
            $formData = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($formData)) {
            $formData = [];
        }

        // Validate National Reg. No from form_data (if local)
        $scope = $request->input('journalist_scope', $formData['journalist_scope'] ?? $application->journalist_scope);
        if ($scope === 'local' && !empty($formData['national_reg_no'])) {
            if (!preg_match('/^\d{2}-\d{6,7}-[A-Z]-\d{2}$/i', $formData['national_reg_no'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid National Reg. No format. Please use the format: 63-1234567-X-89',
                ], 422);
            }
        }

        $application->update([
            'journalist_scope'  => $request->input('journalist_scope', $formData['journalist_scope'] ?? $application->journalist_scope),
            'collection_region' => $request->input('collection_region', $formData['collection_region'] ?? $application->collection_region),
            'form_data'         => $formData,
            'is_draft'          => false,
            'status'            => Application::SUBMITTED,
            'submitted_at'      => now(),
        ]);

        // Allow replacing documents
        $this->saveDraftDocuments($request, $application, [
            'passport_photo',
            'id_scan',
            'employment_letter',
            'reference_letter',
            'educational_certificate',
            'passport_biodata_page',
            'clearance_letter',
        ]);

        $this->mergeIntoProfile([
            'title' => $formData['title'] ?? null,
            'first_name' => $formData['first_name'] ?? null,
            'surname' => $formData['surname'] ?? null,
            'other_names' => $formData['other_names'] ?? null,
            'phone' => ($formData['phone_country_code'] ?? '') . ($formData['phone'] ?? ''),
            'email' => $formData['email'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application updated and resubmitted for review.',
            'reference' => $application->reference,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $data = $request->validate([
            'id_number' => ['nullable', 'string', 'regex:/^\d{2}-\d{6,7}-[A-Z]-\d{2}$/i'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'phone2' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'profile' => ['nullable', 'array'],
        ]);

        $updates = [];
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'id_number')) {
            $updates['id_number'] = $data['id_number'] ?? $user->id_number;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'passport_number')) {
            $updates['passport_number'] = $data['passport_number'] ?? $user->passport_number;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone2')) {
            $updates['phone2'] = $data['phone2'] ?? $user->phone2;
        }
        if (isset($data['phone_number'])) {
            $updates['phone_number'] = $data['phone_number'];
        }

        $profile = $user->profile_data ?? [];
        if (!empty($data['profile'])) {
            $profile = array_merge($profile, $data['profile']);
        }
        if (isset($data['nationality'])) {
            $profile['nationality'] = $data['nationality'];
        }
        if ($profile !== ($user->profile_data ?? [])) {
            $updates['profile_data'] = $profile;
        }

        $user->update($updates);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Save Draft (FILES + JSON)
     * NOTE: This endpoint MUST be called with multipart/form-data (FormData),
     * not JSON, otherwise files will never arrive here.
     */
    public function saveDraft(Request $request)
    {
        $user = Auth::user();

        // form_data may arrive as JSON string
        $rawFormData = $request->input('form_data');
        $formData = $rawFormData;

        if (is_string($rawFormData)) {
            $decoded = json_decode($rawFormData, true);
            $formData = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($formData)) {
            $formData = [];
        }

        $draft = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'accreditation')
            ->where('request_type', 'new')
            ->where('is_draft', true)
            ->first();

        $reference = $draft?->reference ?: ('DRAFT-AP3-' . now()->format('Y') . '-' . Str::random(6));

        $draft = Application::updateOrCreate(
            [
                'applicant_user_id' => $user->id,
                'application_type'  => 'accreditation',
                'request_type'      => 'new',
                'is_draft'          => true,
            ],
            [
                'reference'         => $reference,
                'journalist_scope'  => $request->input('journalist_scope', $formData['journalist_scope'] ?? null),
                'collection_region' => $request->input('collection_region', $formData['collection_region'] ?? 'harare'),
                'form_data'         => $formData,
                'status'            => Application::DRAFT,
            ]
        );

        // Save draft documents (NEW: support all doc fields used in your blade)
        $this->saveDraftDocuments($request, $draft, [
            'passport_photo',
            'id_scan',
            'employment_letter',
            'reference_letter',
            'educational_certificate',
            'passport_biodata_page',
            'clearance_letter',
        ]);

        // Keep profile updated using formData values
        $this->mergeIntoProfile([
            'title' => $formData['title'] ?? null,
            'first_name' => $formData['first_name'] ?? null,
            'surname' => $formData['surname'] ?? null,
            'other_names' => $formData['other_names'] ?? null,
            'phone' => ($formData['phone_country_code'] ?? '') . ($formData['phone'] ?? ''),
            'email' => $formData['email'] ?? null,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Draft saved successfully',
            'draft_id' => $draft->id,
            'reference' => $draft->reference,
        ]);
    }

    private function saveDraftDocuments(Request $request, Application $application, array $fields, string $status = 'draft'): void
    {
        foreach ($fields as $field) {
            if (!$request->hasFile($field)) continue;

            $file = $request->file($field);
            if (!$file) continue;

            $sha256 = null;
            try { $sha256 = hash_file('sha256', $file->getRealPath()); } catch (\Throwable $e) {}

            // Block duplicates per application if possible
            if ($sha256 && Schema::hasColumn('application_documents', 'sha256')) {
                $exists = ApplicationDocument::where('application_id', $application->id)
                    ->where('sha256', $sha256)
                    ->exists();
                if ($exists) continue;
            }

            $path = $file->store('documents/' . $application->id, 'public');

            $docData = [
                'file_path'      => $path,
                'original_name'  => $file->getClientOriginalName(),
                'status'         => $status,
                'owner_id'       => auth()->id(),
                'mime'           => method_exists($file, 'getMimeType') ? $file->getMimeType() : null,
                'size'           => method_exists($file, 'getSize')     ? $file->getSize()     : null,
                'sha256'         => $sha256,
            ];

            ApplicationDocument::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'doc_type'       => $field,
                ],
                $docData
            );
        }

        // Support multiple past work samples
        if ($request->hasFile('past_work_samples')) {
            $samples = $request->file('past_work_samples');
            if (is_array($samples)) {
                foreach ($samples as $idx => $file) {
                    $docType = 'past_work_sample_' . ($idx + 1);
                    $path = $file->store('documents/' . $application->id, 'public');
                    $sha256 = null;
                    try { $sha256 = hash_file('sha256', $file->getRealPath()); } catch (\Throwable $e) {}

                    $docData = [
                        'file_path'      => $path,
                        'original_name'  => $file->getClientOriginalName(),
                        'status'         => $status,
                        'owner_id'       => auth()->id(),
                        'mime'           => method_exists($file, 'getMimeType') ? $file->getMimeType() : null,
                        'size'           => method_exists($file, 'getSize')     ? $file->getSize()     : null,
                        'sha256'         => $sha256,
                    ];

                    ApplicationDocument::updateOrCreate(
                        [
                            'application_id' => $application->id,
                            'doc_type'       => $docType,
                        ],
                        $docData
                    );
                }
            }
        }
    }

    /**
     * Submit Application (FILES + JSON)
     * Enforces Local vs Foreign requirements and Employed vs Freelancer.
     */
    public function submit(Request $request)
    {
        $this->ensureWindowOpen('accreditation');
        $user = Auth::user();

        // Base request validation for files + top fields
        $validated = $request->validate([
            'journalist_scope'   => 'required|in:local,foreign',
            'collection_region'  => 'nullable|in:harare,bulawayo,mutare,masvingo', // only required for local in our rules
            'form_data'          => 'nullable',

            // Uploads
            'passport_photo'        => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'id_scan'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'employment_letter'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'reference_letter'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'educational_certificate'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'passport_biodata_page' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'clearance_letter'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Decode form_data safely
        $rawFormData = $request->input('form_data');
        $formData = $rawFormData;
        if (is_string($rawFormData)) {
            $decoded = json_decode($rawFormData, true);
            $formData = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($formData)) $formData = [];

        // Identify existing draft EARLY to support file presence check
        $existingDraft = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'accreditation')
            ->where('request_type', 'new')
            ->where('is_draft', true)
            ->first();

        // Helper to check if a file exists in the request OR has already been uploaded for this draft
        $hasDoc = function($field) use ($request, $existingDraft) {
            if ($request->hasFile($field)) return true;
            if ($existingDraft) {
                return ApplicationDocument::where('application_id', $existingDraft->id)
                    ->where('doc_type', $field)
                    ->exists();
            }
            return false;
        };

        $scope = $validated['journalist_scope'] ?? $formData['journalist_scope'] ?? ($existingDraft ? $existingDraft->journalist_scope : 'local');
        $employmentType = $formData['employment_type'] ?? null;

        // ---- REQUIRED FIELDS (SPEC) ----
        $requiredCommon = [
            'title', 'surname', 'first_name', 'dob', 'birth_place',
            'marital_status', 'gender', 'nationality', 'address',
            'phone_country_code', 'phone', 'email',
            'employment_type', 'medium_type', 'designation',
            'declaration_date',
        ];

        foreach ($requiredCommon as $k) {
            if (empty($formData[$k])) {
                return response()->json([
                    'success' => false,
                    'message' => "Missing required field: {$k}",
                ], 422);
            }
        }

        // Referees: 3 required
        for ($i = 1; $i <= 3; $i++) {
            $rk = "referee_name_{$i}";
            $ra = "referee_address_{$i}";
            $rp = "referee_phone_{$i}";
            if (empty($formData[$rk]) || empty($formData[$ra]) || empty($formData[$rp])) {
                return response()->json([
                    'success' => false,
                    'message' => "All 3 referees are required (Name, Address, Phone). Missing referee {$i}.",
                ], 422);
            }
        }

        // Local vs Foreign
        if ($scope === 'local') {
            if (empty($formData['national_reg_no'])) {
                return response()->json(['success'=>false,'message'=>'National Reg. No is required for local applications.'], 422);
            }
            // Collection office required for local
            $collection = $request->input('collection_region') ?: ($formData['collection_region'] ?? null);
            if (!$collection) {
                return response()->json(['success'=>false,'message'=>'Collection Office is required for local applications.'], 422);
            }

            // Strict format validation for national_reg_no
            if (!preg_match('/^\d{2}-\d{6,7}-[A-Z]-\d{2}$/i', $formData['national_reg_no'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid National Reg. No format. Please use the format: 63-1234567-X-89',
                ], 422);
            }
        }

        if ($scope === 'foreign') {
            $requiredForeign = [
                'passport_no', 'passport_expiry', 'passport_issued_at',
                'first_time_in_zim', 'address_in_zimbabwe',
            ];
            foreach ($requiredForeign as $k) {
                if (empty($formData[$k])) {
                    return response()->json(['success'=>false,'message'=>"Missing required field for foreign application: {$k}"], 422);
                }
            }

            // Foreign travel fields (Required for ALL foreign applicants)
            $requiredForeignTravel = ['arrived_on', 'arrival_mode', 'port_of_entry', 'departing_on', 'special_assignment'];
            foreach ($requiredForeignTravel as $k) {
                if (empty($formData[$k])) {
                    return response()->json(['success'=>false,'message'=>"Missing required foreign travel field: {$k}"], 422);
                }
            }
        }

        // Employed vs Freelancer upload requirements (Local only per spec)
        if ($scope === 'local') {
            if ($employmentType === 'employed' && !$hasDoc('employment_letter')) {
                return response()->json(['success'=>false,'message'=>'Employment Letter is required for Employed applicants.'], 422);
            }
            if ($employmentType === 'freelancer' && !$hasDoc('reference_letter')) {
                return response()->json(['success'=>false,'message'=>'Reference/Testimonial/Affidavit is required for Freelancers.'], 422);
            }
        }

        // Required photo always
        if (!$hasDoc('passport_photo')) {
            return response()->json(['success'=>false,'message'=>'Photo is required. Please upload or take a passport photo.'], 422);
        }

        // Required ID docs
        if ($scope === 'local' && !$hasDoc('id_scan')) {
            return response()->json(['success'=>false,'message'=>'National ID Scan is required for Local applications.'], 422);
        }
        if ($scope === 'foreign') {
            if (!$hasDoc('passport_biodata_page')) {
                return response()->json(['success'=>false,'message'=>'Passport Bio Data Page is required for Foreign applications.'], 422);
            }
            if (!$hasDoc('clearance_letter')) {
                return response()->json(['success'=>false,'message'=>'Clearance Letter is required for Foreign applications.'], 422);
            }
        }

        // Merge into profile (based on formData)
        $this->mergeIntoProfile([
            'title' => $formData['title'] ?? null,
            'surname' => $formData['surname'] ?? null,
            'first_name' => $formData['first_name'] ?? null,
            'other_names' => $formData['other_names'] ?? null,
            'date_of_birth' => $formData['dob'] ?? null,
            'place_birth' => $formData['birth_place'] ?? null,
            'marital_status' => $formData['marital_status'] ?? null,
            'sex' => $formData['gender'] ?? null,
            'national_reg_no' => $formData['national_reg_no'] ?? null,
            'passport_no' => $formData['passport_no'] ?? null,
            'passport_expiry' => $formData['passport_expiry'] ?? null,
            'passport_issued_at' => $formData['passport_issued_at'] ?? null,
            'nationality' => $formData['nationality'] ?? null,
            'drivers_licence_no' => $formData['drivers_licence_no'] ?? null,
            'residential_address' => $formData['address'] ?? null,
            'phone' => ($formData['phone_country_code'] ?? '') . ($formData['phone'] ?? ''),
            'email' => $formData['email'] ?? null,
            'employment_type' => $formData['employment_type'] ?? null,
        ]);

        // Generate reference (ZMC-AP3-YYYY-0001)
        $year = now()->format('Y');
        $prefix = "ZMC-AP3-{$year}-";

        $lastRef = Application::where('reference', 'like', $prefix . '%')
            ->where('reference', 'not like', 'DRAFT%')
            ->orderByRaw("reference DESC")
            ->value('reference');

        $nextNum = 1;
        if ($lastRef) {
            $lastNum = (int) substr($lastRef, -4);
            $nextNum = $lastNum + 1;
        }

        $reference = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $collectionRegion = $request->input('collection_region') ?: ($formData['collection_region'] ?? 'harare');

        $paymentMethod = $request->input('payment_payment_method') ?: $request->input('payment_method');
        $hasPayment = !empty($paymentMethod) || $request->hasFile('payment_proof_file') || $request->hasFile('payment_waiver_file');
        $initialStatus = $hasPayment ? Application::AWAITING_ACCOUNTS_VERIFICATION : Application::SUBMITTED;

        $paymentFields = [];
        if ($hasPayment) {
            $paymentFields = [
                'payment_submission_method' => $paymentMethod ?? 'proof_upload',
                'payment_submitted_at' => now(),
                'proof_status' => 'submitted',
                'payment_status' => 'pending',
            ];
            if ($request->input('payment_paynow_reference')) {
                $paymentFields['paynow_ref_submitted'] = $request->input('payment_paynow_reference');
                $paymentFields['payment_submission_method'] = 'paynow_reference';
            }
            if ($request->input('payment_proof_amount_paid')) {
                $paymentFields['proof_amount_paid'] = $request->input('payment_proof_amount_paid');
            }
            if ($request->input('payment_proof_bank_name')) {
                $paymentFields['proof_bank_name'] = $request->input('payment_proof_bank_name');
            }
            if ($request->input('payment_proof_payment_date')) {
                $paymentFields['proof_payment_date'] = $request->input('payment_proof_payment_date');
            }
            if ($request->input('payment_proof_first_name')) {
                $paymentFields['proof_payer_first_name'] = $request->input('payment_proof_first_name');
            }
            if ($request->input('payment_proof_last_name')) {
                $paymentFields['proof_payer_last_name'] = $request->input('payment_proof_last_name');
            }
        }

        if ($existingDraft) {
            $existingDraft->update(array_merge([
                'reference'         => $reference,
                'journalist_scope'  => $scope,
                'collection_region' => $collectionRegion,
                'form_data'         => $formData,
                'is_draft'          => false,
                'status'            => $initialStatus,
                'submitted_at'      => now(),
            ], $paymentFields));
            $application = $existingDraft;
        } else {
            $application = Application::create(array_merge([
                'reference'         => $reference,
                'applicant_user_id' => $user->id,
                'application_type'  => 'accreditation',
                'request_type'      => 'new',
                'journalist_scope'  => $scope,
                'collection_region' => $collectionRegion,
                'form_data'         => $formData,
                'is_draft'          => false,
                'status'            => $initialStatus,
                'submitted_at'      => now(),
            ], $paymentFields));
        }

        if ($hasPayment && $request->hasFile('payment_proof_file')) {
            $request->validate(['payment_proof_file' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240']]);
            $proofFile = $request->file('payment_proof_file');
            $proofPath = $proofFile->store('payment_proofs', 'public');
            $application->update([
                'payment_proof_path' => $proofPath,
                'payment_proof_uploaded_at' => now(),
                'proof_original_name' => $proofFile->getClientOriginalName(),
                'proof_mime' => $proofFile->getMimeType(),
            ]);
        }

        if ($hasPayment && $request->hasFile('payment_waiver_file')) {
            $request->validate(['payment_waiver_file' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240']]);
            $waiverFile = $request->file('payment_waiver_file');
            $waiverPath = $waiverFile->store('waivers', 'public');
            $application->update([
                'waiver_path' => $waiverPath,
                'waiver_status' => 'submitted',
                'waiver_original_name' => $waiverFile->getClientOriginalName(),
                'waiver_mime' => $waiverFile->getMimeType(),
                'payment_submission_method' => 'waiver',
            ]);
        }

        // Upload documents to ApplicationDocument
        $this->saveDraftDocuments($request, $application, [
            'passport_photo',
            'id_scan',
            'employment_letter',
            'reference_letter',
            'educational_certificate',
            'passport_biodata_page',
            'clearance_letter',
        ], 'pending');

        return response()->json([
            'success'   => true,
            'message'   => 'Application submitted successfully',
            'reference' => $application->reference,
        ]);
    }

    public function renewalForm(Request $request)
    {
        $user = Auth::user();
        $ap5Type = 'renewal';

        $drafts = Application::where('applicant_user_id', $user->id)
            ->where('is_draft', true)
            ->where('application_type', 'accreditation')
            ->where('request_type', 'renewal')
            ->orderByDesc('created_at')
            ->get();

        $draft = null;
        if ($request->filled('draft')) {
            $draft = $drafts->firstWhere('reference', $request->input('draft'));
        }

        return view('portal.accreditation.renewals', compact('drafts', 'draft', 'ap5Type'));
    }

    public function replacementForm(Request $request)
    {
        $user = Auth::user();
        $ap5Type = 'replacement';

        $drafts = Application::where('applicant_user_id', $user->id)
            ->where('is_draft', true)
            ->where('application_type', 'accreditation')
            ->where('request_type', 'replacement')
            ->orderByDesc('created_at')
            ->get();

        $draft = null;
        if ($request->filled('draft')) {
            $draft = $drafts->firstWhere('reference', $request->input('draft'));
        }

        return view('portal.accreditation.renewals', compact('drafts', 'draft', 'ap5Type'));
    }

    public function renewals(Request $request)
    {
        $user = Auth::user();

        $drafts = Application::where('applicant_user_id', $user->id)
            ->where('is_draft', true)
            ->where('application_type', 'accreditation')
            ->whereIn('request_type', ['renewal', 'replacement'])
            ->orderByDesc('created_at')
            ->get();

        $draft = null;
        if ($request->filled('draft')) {
            $draft = $drafts->firstWhere('reference', $request->input('draft'));
        }

        $ap5Type = null;
        return view('portal.accreditation.renewals', compact('drafts', 'draft', 'ap5Type'));
    }

    /**
     * AP5 Draft: allow multiple drafts per user.
     * Every click creates a new draft record (no overwrite).
     */
    public function saveDraftAp5(Request $request)
    {
        $this->ensureWindowOpen('accreditation');
        $user = Auth::user();

        $validated = $request->validate([
            'request_type' => 'required|in:renewal,replacement',
            'practitioner_type' => 'nullable|in:employed,freelancer',
            'draft_reference' => 'nullable|string|max:64',
            'current_step' => 'nullable|integer|min:1|max:4',
            'declaration_confirmed' => 'nullable|in:1',
 
             'renewal_employer_letter'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
             'replacement_affidavit'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
             'replacement_employer_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
             'replacement_police_report'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Store all posted inputs (excluding files + _token) into form_data
        $formData = $request->except([
            '_token',
            'renewal_employer_letter',
            'replacement_affidavit',
            'replacement_employer_letter',
            'replacement_police_report',
        ]);

        $reference = 'DRAFT-AP5-' . now()->format('Y') . '-' . Str::upper(Str::random(6));

        $draft = Application::create([
            'reference'         => $reference,
            'applicant_user_id' => $user->id,
            'application_type'  => 'accreditation',
            'request_type'      => $validated['request_type'],
            'journalist_scope'  => $user->profile_data['journalist_scope'] ?? ($formData['journalist_scope'] ?? 'local'),
            'collection_region' => $formData['collection_region'] ?? 'harare',
            'form_data'         => $formData,
            'is_draft'          => true,
            'status'            => Application::DRAFT,
        ]);

        $this->saveDraftDocuments($request, $draft, [
            'renewal_employer_letter',
            'replacement_affidavit',
            'replacement_employer_letter',
            'replacement_police_report',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully.',
            'reference' => $draft->reference,
        ]);
    }

    /**
     * Submit AP5 (Renewal/Replacement) for review.
     */
    public function submitAp5(Request $request)
    {
        $this->ensureWindowOpen('accreditation');
        $user = Auth::user();

        $validated = $request->validate([
            'request_type' => 'required|in:renewal,replacement',
            'practitioner_type' => 'required_if:request_type,renewal|in:employed,freelancer',
            'current_step' => 'nullable|integer|min:1|max:4',
            'surname' => 'required|string|max:120',
            'first_name' => 'required|string|max:120',
            'gender' => 'required|in:male,female',
            'dob' => 'required|date',
            'nationality' => 'required|string|max:120',
            'national_id' => 'nullable|string|max:120',
            'passport_number' => 'nullable|string|max:120',
            'id_or_passport' => 'nullable|string|max:120',
            'accreditation_number' => 'required|string|max:120',

            // docs required based on type
            'renewal_employer_letter'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_affidavit'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_employer_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_police_report'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_reason'          => 'nullable|in:lost,damaged,stolen',
        ]);

        // enforce doc rules
        if ($validated['request_type'] === 'renewal') {
            $isEmployed = ($validated['practitioner_type'] ?? 'employed') === 'employed';
            if ($isEmployed && !$request->hasFile('renewal_employer_letter')) {
                return response()->json(['success' => false, 'message' => 'Employer Letter is required for employed renewal.'], 422);
            }
        }
        if ($validated['request_type'] === 'replacement') {
            if (!$request->hasFile('replacement_affidavit') || !$request->hasFile('replacement_employer_letter')) {
                return response()->json(['success' => false, 'message' => 'Affidavit and Employer Letter are required for replacement.'], 422);
            }
            if (($validated['replacement_reason'] ?? null) === 'stolen' && !$request->hasFile('replacement_police_report')) {
                return response()->json(['success' => false, 'message' => 'Police Report is required for stolen replacement.'], 422);
            }
        }

        $formData = $request->except([
            '_token',
            'renewal_employer_letter',
            'replacement_affidavit',
            'replacement_employer_letter',
            'replacement_police_report',
        ]);

        $draftRef = $request->input('draft_reference');
        $draft = null;

        if ($draftRef) {
            $draft = Application::where('reference', $draftRef)
                ->where('applicant_user_id', $user->id)
                ->where('is_draft', true)
                ->where('application_type', 'accreditation')
                ->first();
        }

        $reference = 'AP5-' . now()->format('Y') . '-' . Str::upper(Str::random(6));

        if ($draft) {
            // Convert draft into a submitted application (removes it from draft lists)
            $draft->reference = $reference;
            $draft->request_type = $validated['request_type'];
            $draft->journalist_scope = $user->profile_data['journalist_scope'] ?? ($draft->journalist_scope ?? 'local');
            $draft->collection_region = $formData['collection_region'] ?? ($draft->collection_region ?? 'harare');
            $draft->form_data = $formData;
            $draft->is_draft = false;
            $draft->status = Application::SUBMITTED;
            $draft->current_stage = Application::OFFICER_REVIEW;
            $draft->submitted_at = now();
            $draft->save();

            $app = $draft;
        } else {
            $app = Application::create([
                'reference'         => $reference,
                'applicant_user_id' => $user->id,
                'application_type'  => 'accreditation',
                'request_type'      => $validated['request_type'],
                'journalist_scope'  => $user->profile_data['journalist_scope'] ?? 'local',
                'collection_region' => $formData['collection_region'] ?? 'harare',
                'form_data'         => $formData,
                'is_draft'          => false,
                'status'            => Application::SUBMITTED,
                'current_stage'     => Application::OFFICER_REVIEW,
                'submitted_at'      => now(),
            ]);
        }

        $this->saveDraftDocuments($request, $app, [
            'renewal_employer_letter',
            'replacement_affidavit',
            'replacement_employer_letter',
            'replacement_police_report',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'reference' => $app->reference,
        ]);
    }

    public function payments()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $payments = \App\Models\Payment::where('payer_user_id', $user->id)
            ->with('application')
            ->orderByDesc('created_at')
            ->get();

        return view('portal.accreditation.payments', compact('payments'));
    }

    public function notices()
    {
        $notices = \App\Models\Notice::where('is_published', true)
            ->whereIn('target_portal', ['journalist', 'both'])
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $events = \App\Models\Event::where('is_published', true)
            ->whereIn('target_portal', ['journalist', 'both'])
            ->orderBy('starts_at')
            ->limit(20)
            ->get();

        return view('portal.accreditation.notices', compact('notices', 'events'));
    }

    public function lookupAccreditation(string $accreditationNumber)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $record = null;
        if (class_exists(\App\Models\AccreditationRecord::class) && \Illuminate\Support\Facades\Schema::hasTable('accreditation_records')) {
            $record = \App\Models\AccreditationRecord::where('accreditation_no', $accreditationNumber)
                ->orWhere('certificate_no', $accreditationNumber)
                ->first();
        }

        if (!$record) {
            return response()->json(['found' => false, 'message' => 'No record found for this accreditation number.']);
        }

        return response()->json([
            'found' => true,
            'record' => [
                'accreditation_no' => $record->accreditation_no ?? $record->certificate_no,
                'holder_name' => $record->holder?->name ?? $record->holder_name ?? '',
                'category' => $record->category ?? '',
                'issued_at' => optional($record->issued_at)->format('d M Y'),
                'expires_at' => optional($record->expires_at)->format('d M Y'),
                'status' => $record->status ?? '',
                'media_house' => $record->media_house_name ?? '',
            ],
        ]);
    }

    public function acknowledgeReminder(Request $request, $reminderId)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $reminder = \App\Models\Reminder::where('id', $reminderId)
            ->where(function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('target_type', 'media_practitioner')
                        ->where('target_id', $user->id);
                })->orWhere('target_type', 'bulk');
            })
            ->firstOrFail();

        \App\Models\ReminderRead::updateOrCreate(
            ['reminder_id' => $reminder->id, 'user_id' => $user->id],
            ['read_at' => now(), 'acknowledged_at' => now()]
        );

        \App\Services\ActivityLogger::log('reminder_acknowledged', null, null, null, [
            'reminder_id' => $reminder->id,
            'actor_user_id' => $user->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Reminder acknowledged.');
    }

    public function howto()
    {
        return view('portal.accreditation.howto');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('portal.accreditation.profile', compact('user'));
    }

    public function communication()
    {
        return view('portal.accreditation.communication');
    }

    public function settings()
    {
        return view('portal.accreditation.settings');
    }

    public function deleteDraft(Application $application)
    {
        $userId = Auth::id();
        abort_unless($userId && $application->applicant_user_id === $userId, 403);
        abort_unless($application->is_draft, 403);

        $application->documents()->each(function ($doc) {
            Storage::disk('public')->delete($doc->file_path);
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
        
        // Only allow withdrawal before approval/rejection logic
        $allowed = [
            Application::SUBMITTED, 
            Application::OFFICER_REVIEW, 
            Application::REGISTRAR_REVIEW, 
            Application::ACCOUNTS_REVIEW
        ];
        abort_unless(in_array($application->status, $allowed), 403);

        $application->update([
            'status' => Application::WITHDRAWN,
            'is_draft' => true, // Move back to draft so they can edit/resubmit or delete
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
            return; // not configured = always open
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
