<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Support\Facades\Log;
use App\Notifications\ApplicationSubmittedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
                    Application::REGISTRAR_REVIEW,
                    Application::ACCOUNTS_REVIEW,
                    Application::APPROVED_AWAITING_PAYMENT,
                    Application::AWAITING_ACCOUNTS_VERIFICATION,
                ])
                ->count(),
            'renewals_due' => 0,
        ];

        $recentApplications = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->take(10)
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

        return view('portal.accreditation.dashboard', compact('stats', 'recentApplications', 'notices', 'events'));
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

        $application->update([
            'journalist_scope'  => $request->input('journalist_scope', $formData['journalist_scope'] ?? $application->journalist_scope),
            'collection_region' => $request->input('collection_region', $formData['collection_region'] ?? $application->collection_region),
            'form_data'         => $formData,
            'is_draft'          => false,
            'status'            => Application::SUBMITTED,
            'submitted_at'      => now(),
        ]);

        try {
            $application->applicant->notify(new ApplicationSubmittedNotification($application));
        } catch (\Throwable $e) {
            Log::error('Notification error in resubmitCorrection', ['msg' => $e->getMessage()]);
        }

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

        $payload = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'phone2' => ['required', 'string', 'max:20'],
            'id_number' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:50'],
        ]);

        if (empty($payload['id_number']) && empty($payload['passport_number'])) {
            return back()->withErrors(['id_number' => 'Either ID Number or Passport Number is required.'])->withInput();
        }

        $updateData = [
            'phone_number' => $payload['phone_number'],
            'phone2' => $payload['phone2'],
            'id_number' => $payload['id_number'],
            'passport_number' => $payload['passport_number'],
        ];

        if (!empty($payload['name'])) {
            $updateData['name'] = $payload['name'];
        }
        if (!empty($payload['email']) && $payload['email'] !== $user->email) {
            $request->validate(['email' => 'unique:users,email']);
            $updateData['email'] = $payload['email'];
        }

        $user->update($updateData);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Save Draft (FILES + JSON)
     * NOTE: This endpoint MUST be called with multipart/form-data (FormData),
     * not JSON, otherwise files will never arrive here.
     */
    public function saveDraft(Request $request)
    {
        try {
            $user = Auth::user();

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
                    'collection_region' => $request->input('collection_region', $formData['collection_region'] ?? null),
                    'form_data'         => array_merge($formData, ['current_step' => $request->input('current_step', 1)]),
                    'status'            => Application::DRAFT,
                ]
            );

            $this->saveDraftDocuments($request, $draft, [
                'passport_photo', 'id_scan', 'employment_letter',
                'reference_letter', 'educational_certificate',
                'passport_biodata_page', 'clearance_letter',
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
                'success'  => true,
                'message'  => 'Draft saved successfully',
                'draft_id' => $draft->id,
                'reference' => $draft->reference,
            ]);
        } catch (\Throwable $e) {
            Log::error('SaveDraft error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving draft: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function saveDraftDocuments(Request $request, Application $application, array $fields): void
    {
        foreach ($fields as $field) {
            if (!$request->hasFile($field)) continue;

            $file = $request->file($field);
            if (!$file) continue;

            $sha256 = null;
            $fileContent = null;
            try {
                $realPath = $file->getRealPath();
                $sha256 = hash_file('sha256', $realPath);
                $rawContent = file_get_contents($realPath);
                $fileContent = $rawContent !== false ? base64_encode($rawContent) : null;
            } catch (\Throwable $e) {}

            if ($sha256) {
                $exists = ApplicationDocument::where('application_id', $application->id)
                    ->where('sha256', $sha256)
                    ->exists();
                if ($exists) continue;
            }

            $path = $file->store('documents/' . $application->id, 'public');

            ApplicationDocument::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'doc_type'       => $field,
                ],
                [
                    'file_path'      => $path,
                    'original_name'  => $file->getClientOriginalName(),
                    'owner_id'       => auth()->id(),
                    'mime'           => $file->getMimeType(),
                    'size'           => $file->getSize(),
                    'sha256'         => $sha256,
                    'file_data'      => $fileContent,
                    'status'         => 'draft',
                ]
            );

        }
    }

    /**
     * Submit Application (FILES + JSON)
     * Enforces Local vs Foreign requirements and Employed vs Freelancer.
     */
    public function submit(Request $request)
    {
        try {
            $this->ensureWindowOpen('accreditation');
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $user = Auth::user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'journalist_scope'   => 'required|in:local,foreign',
            'collection_region'  => 'nullable|in:harare,bulawayo,mutare,masvingo,gweru,chinhoyi',
            'form_data'          => 'nullable',

            'passport_photo'        => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'id_scan'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'employment_letter'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'reference_letter'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'educational_certificate'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'passport_biodata_page' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'clearance_letter'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'work_samples'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,mp4,mov,avi,doc,docx|max:20480',
        ]);

        if ($validator->fails()) {
            Log::error('Submit validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $validated = $validator->validated();

        // Decode form_data safely
        $rawFormData = $request->input('form_data');
        $formData = $rawFormData;

        if (is_string($rawFormData)) {
            $decoded = json_decode($rawFormData, true);
            $formData = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($formData)) $formData = [];

        $scope = $validated['journalist_scope'];
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

            $requiredForeignTravel = ['journalist_based_country', 'arrived_on', 'arrival_mode', 'port_of_entry', 'departing_on', 'special_assignment'];
            foreach ($requiredForeignTravel as $k) {
                if (empty($formData[$k])) {
                    return response()->json(['success'=>false,'message'=>"Missing required foreign field: {$k}"], 422);
                }
            }
        }

        // Employed vs Freelancer upload requirements
        if ($scope === 'local') {
            if ($employmentType === 'employed' && !$request->hasFile('employment_letter')) {
                return response()->json(['success'=>false,'message'=>'Employment Letter is required for Employed applicants.'], 422);
            }
            if ($employmentType === 'freelancer' && !$request->hasFile('reference_letter')) {
                return response()->json(['success'=>false,'message'=>'Reference/Testimonial/Affidavit is required for Freelancers.'], 422);
            }
        }

        // Required photo always
        if (!$request->hasFile('passport_photo')) {
            return response()->json(['success'=>false,'message'=>'Photo is required. Please upload or take a passport photo.'], 422);
        }

        // Required ID docs
        if ($scope === 'local' && !$request->hasFile('id_scan')) {
            return response()->json(['success'=>false,'message'=>'National ID Scan is required for Local applications.'], 422);
        }
        if ($scope === 'foreign') {
            if (!$request->hasFile('passport_biodata_page')) {
                return response()->json(['success'=>false,'message'=>'Passport Bio Data Page is required for Foreign applications.'], 422);
            }
            if (!$request->hasFile('clearance_letter')) {
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

        try {
            // Generate reference (ZMC-AP3-YYYY-0001)
            $year = now()->format('Y');
            $prefix = "ZMC-AP3-{$year}-";

            $lastRef = Application::where('reference', 'like', $prefix . '%')
                ->where('reference', 'not like', 'DRAFT%')
                ->orderBy('reference', 'desc')
                ->value('reference');

            $nextNum = 1;
            if ($lastRef) {
                $lastNum = (int) substr($lastRef, -4);
                $nextNum = $lastNum + 1;
            }

            $reference = $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            $existingDraft = Application::where('applicant_user_id', $user->id)
                ->where('application_type', 'accreditation')
                ->where('request_type', 'new')
                ->where('is_draft', true)
                ->first();

            $collectionRegion = $request->input('collection_region') ?: ($formData['collection_region'] ?? 'harare');

            $application = null;

            \DB::transaction(function () use (
                $user, $reference, $scope, $collectionRegion, $formData,
                $existingDraft, $request, &$application
            ) {
                if ($existingDraft) {
                    $existingDraft->update([
                        'reference'         => $reference,
                        'journalist_scope'  => $scope,
                        'collection_region' => $collectionRegion,
                        'form_data'         => $formData,
                        'is_draft'          => false,
                        'status'            => Application::SUBMITTED,
                        'submitted_at'      => now(),
                    ]);
                    $application = $existingDraft;
                } else {
                    $application = Application::create([
                        'reference'         => $reference,
                        'applicant_user_id' => $user->id,
                        'application_type'  => 'accreditation',
                        'request_type'      => 'new',
                        'journalist_scope'  => $scope,
                        'collection_region' => $collectionRegion,
                        'form_data'         => $formData,
                        'is_draft'          => false,
                        'status'            => Application::SUBMITTED,
                        'submitted_at'      => now(),
                    ]);
                }

                $fileFields = [
                    'passport_photo',
                    'id_scan',
                    'employment_letter',
                    'reference_letter',
                    'educational_certificate',
                    'passport_biodata_page',
                    'clearance_letter',
                    'work_samples',
                ];

                foreach ($fileFields as $field) {
                    if (!$request->hasFile($field)) continue;

                    $file = $request->file($field);
                    $sha256 = null;
                    try {
                        $sha256 = hash_file('sha256', $file->getRealPath());
                    } catch (\Throwable $e) {}

                    if ($sha256) {
                        $exists = ApplicationDocument::where('application_id', $application->id)
                            ->where('sha256', $sha256)
                            ->exists();
                        if ($exists) continue;
                    }

                    $path = $file->store('documents/' . $application->id, 'public');

                    ApplicationDocument::updateOrCreate(
                        [
                            'application_id' => $application->id,
                            'doc_type'       => $field,
                        ],
                        [
                            'file_path'      => $path,
                            'original_name'  => $file->getClientOriginalName(),
                            'owner_id'       => $user->id,
                            'mime'           => $file->getMimeType(),
                            'size'           => $file->getSize(),
                            'sha256'         => $sha256,
                            'file_data'      => null,
                            'status'         => 'pending',
                        ]
                    );
                }
            });

            try {
                $user->notify(new ApplicationSubmittedNotification($application));
            } catch (\Throwable $e) {
                Log::error('Notification error in submit', ['msg' => $e->getMessage()]);
            }

            return response()->json([
                'success'        => true,
                'message'        => 'Application submitted successfully',
                'reference'      => $application->reference,
                'application_id' => $application->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Submit DB error', ['message' => mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8'), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Submission failed. Please try again or contact support.',
            ], 500);
        }
    }

    public function renewals(Request $request)
    {
        $user = Auth::user();

        $drafts = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'accreditation')
            ->where('request_type', 'renewal')
            ->where(function($q) {
                $q->where('is_draft', true)
                  ->orWhere(function($qq) {
                      $qq->where('is_draft', false)
                        ->where('status', Application::PAID_CONFIRMED)
                        ->whereNotNull('batch_id');
                  });
            })
            ->orderByDesc('created_at')
            ->get();

        $draft = null;
        if ($request->filled('draft')) {
            $draft = $drafts->firstWhere('reference', $request->input('draft'));
        }

        return view('portal.accreditation.renewals', compact('drafts', 'draft'));
    }

    public function replacement(Request $request)
    {
        $user = Auth::user();

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

        return view('portal.accreditation.replacement', compact('drafts', 'draft'));
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


    public function lookupAccreditationNumber(string $number)
    {
        $record = \App\Models\AccreditationRecord::where('record_number', $number)
            ->orWhere('certificate_no', $number)
            ->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'No record found for this accreditation number.'], 404);
        }

        $application = $record->application;
        $formData = $application ? ($application->form_data ?? []) : [];
        $holder = $record->holder;

        return response()->json([
            'success' => true,
            'record' => [
                'record_number' => $record->record_number,
                'certificate_no' => $record->certificate_no,
                'status' => $record->status,
                'issued_at' => $record->issued_at?->format('Y-m-d'),
                'expires_at' => $record->expires_at?->format('Y-m-d'),
                'holder_name' => $holder ? trim(($holder->profile_data['first_name'] ?? '') . ' ' . ($holder->profile_data['surname'] ?? '')) : ($formData['first_name'] ?? '') . ' ' . ($formData['surname'] ?? ''),
                'surname' => $holder->profile_data['surname'] ?? $formData['surname'] ?? '',
                'first_name' => $holder->profile_data['first_name'] ?? $formData['first_name'] ?? '',
                'other_names' => $holder->profile_data['other_names'] ?? $formData['other_names'] ?? '',
                'gender' => $holder->profile_data['sex'] ?? $formData['gender'] ?? '',
                'dob' => $holder->profile_data['date_of_birth'] ?? $formData['dob'] ?? '',
                'nationality' => $holder->profile_data['nationality'] ?? $formData['nationality'] ?? '',
                'id_or_passport' => $holder->profile_data['national_reg_no'] ?? $holder->profile_data['passport_no'] ?? $formData['id_or_passport'] ?? '',
                'employment_type' => $holder->profile_data['employment_type'] ?? $formData['employment_type'] ?? '',
                'medium_type' => $formData['medium_type'] ?? '',
                'designation' => $formData['designation'] ?? '',
                'application_type' => $application->application_type ?? '',
                'journalist_scope' => $application->journalist_scope ?? '',
            ],
        ]);
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
            'draft_reference' => 'nullable|string|max:64',
            'current_step' => 'nullable|integer|min:1|max:4',
            'declaration_confirmed' => 'nullable|in:1',
            'journalist_scope' => 'nullable|in:local,foreign',
            'national_id_number' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',

            // docs (optional for draft)
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

        $draft = Application::updateOrCreate(
            [
                'applicant_user_id' => $user->id,
                'application_type'  => 'accreditation',
                'request_type'      => $validated['request_type'],
                'reference'         => $request->input('draft_reference') ?: ('DRAFT-AP5-' . now()->format('Y') . '-' . Str::upper(Str::random(6))),
                'is_draft'          => true,
            ],
            [
                'journalist_scope'  => $user->profile_data['journalist_scope'] ?? ($formData['journalist_scope'] ?? 'local'),
                'collection_region' => $formData['collection_region'] ?? 'harare',
                'form_data'         => array_merge($formData, ['current_step' => $request->input('current_step', 1)]),
                'status'            => Application::DRAFT,
            ]
        );

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
     * Renewals route directly to AWAITING_ACCOUNTS_VERIFICATION (skip Officer/Registrar).
     */
    public function submitAp5(Request $request)
    {
        $this->ensureWindowOpen('accreditation');
        $user = Auth::user();

        $validated = $request->validate([
            'request_type' => 'required|in:renewal,replacement',
            'current_step' => 'nullable|integer|min:1|max:4',
            'accreditation_number' => 'required|string|max:120',
            'journalist_scope' => 'nullable|in:local,foreign',
            'national_id_number' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'has_changes' => 'nullable|in:yes,no',
            'changes_data' => 'nullable',
            'employment_status' => 'nullable|in:freelancer,employed',

            'renewal_employer_letter'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_affidavit'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_employer_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_police_report'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'replacement_reason'          => 'nullable|in:lost,damaged,stolen',

            'payment_method' => 'nullable|in:paynow,proof_upload',
            'paynow_reference' => 'nullable|string|max:120',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $employmentStatus = $validated['employment_status'] ?? 'employed';

        if ($validated['request_type'] === 'renewal') {
            if ($employmentStatus === 'employed' && !$request->hasFile('renewal_employer_letter')) {
                return response()->json(['success' => false, 'message' => 'Employment Letter is required for employed applicants.'], 422);
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

        $changesData = $request->input('changes_data');
        if (is_string($changesData)) {
            $changesData = json_decode($changesData, true);
        }

        $formData = $request->except([
            '_token',
            'renewal_employer_letter',
            'replacement_affidavit',
            'replacement_employer_letter',
            'replacement_police_report',
            'payment_proof',
        ]);
        $formData['changes_data'] = $changesData;

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

        $status = ($validated['request_type'] === 'renewal')
            ? Application::AWAITING_ACCOUNTS_VERIFICATION
            : Application::SUBMITTED;

        if ($draft && $draft->status === Application::PAID_CONFIRMED && $draft->batch_id) {
            $status = Application::PRODUCTION_QUEUE;
        }

        if ($draft) {
            $draft->reference = $reference;
            $draft->request_type = $validated['request_type'];
            $draft->journalist_scope = $user->profile_data['journalist_scope'] ?? ($draft->journalist_scope ?? 'local');
            $draft->collection_region = $formData['collection_region'] ?? ($draft->collection_region ?? 'harare');
            $draft->form_data = $formData;
            $draft->is_draft = false;
            $draft->status = $status;
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
                'status'            => $status,
                'submitted_at'      => now(),
            ]);
        }

        $this->saveDraftDocuments($request, $app, [
            'renewal_employer_letter',
            'replacement_affidavit',
            'replacement_employer_letter',
            'replacement_police_report',
        ]);

        if ($request->hasFile('payment_proof')) {
            $this->saveDraftDocuments($request, $app, ['payment_proof']);
        }

        if (!empty($validated['paynow_reference'])) {
            $app->update(['paynow_ref_submitted' => $validated['paynow_reference']]);
        }

        try {
            $user->notify(new ApplicationSubmittedNotification($app));
        } catch (\Throwable $e) {
            Log::error('Notification error in submitAp5', ['msg' => $e->getMessage()]);
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Application submitted successfully.',
            'reference'      => $app->reference,
            'application_id' => $app->id,
        ]);
    }

    public function payments()
    {
        $user = Auth::user();
        $applications = Application::where('applicant_user_id', $user->id)
            ->where('application_type', 'accreditation')
            ->whereNotNull('payment_status')
            ->orWhere(function($q) use ($user) {
                $q->where('applicant_user_id', $user->id)
                  ->where('application_type', 'accreditation')
                  ->whereIn('status', [
                      Application::AWAITING_ACCOUNTS_VERIFICATION,
                      Application::PAYMENT_VERIFIED,
                      Application::PAID_CONFIRMED,
                      Application::ACCOUNTS_REVIEW,
                      Application::APPROVED_AWAITING_PAYMENT,
                  ]);
            })
            ->orderByDesc('submitted_at')
            ->get();

        return view('portal.accreditation.payments', compact('applications'));
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

        $news = \App\Models\News::where('is_published', true)
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();

        return view('portal.accreditation.notices', compact('notices', 'events', 'news'));
    }

    public function howto()
    {
        return view('portal.accreditation.howto');
    }

    public function requirements()
    {
        return view('portal.accreditation.requirements');
    }

    public function profile()
    {
        return view('portal.accreditation.profile');
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
