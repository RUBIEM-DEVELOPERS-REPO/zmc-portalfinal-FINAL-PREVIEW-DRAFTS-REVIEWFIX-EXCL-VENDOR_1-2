<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\AccreditationRecord;
use App\Models\RegistrationRecord;
use App\Services\ApplicationWorkflow;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccreditationOfficerController extends Controller
{
    /**
     * Officer dashboard statuses (your list + submitted).
     */
    public function dashboard()
    {
        $user = Auth::user();

        $pendingStatuses = [
            Application::SUBMITTED,
            Application::SUBMITTED_WITH_APP_FEE,
            Application::OFFICER_REVIEW,
            Application::CORRECTION_REQUESTED,
            Application::RETURNED_TO_OFFICER,
            Application::REGISTRAR_FIX_REQUEST,
            Application::AWAITING_BATCH_PAYMENT,
            Application::PAID_CONFIRMED,
        ];

        $kpis = [
            'total_applications' => Application::count(),
            'recent_applications' => Application::whereIn('status', [
                Application::SUBMITTED,
                Application::SUBMITTED_WITH_APP_FEE,
                Application::OFFICER_REVIEW,
            ])->where(function($q) use ($user) {
                $q->whereNull('assigned_officer_id')
                  ->orWhere('assigned_officer_id', $user->id);
            })->count(),
            'approved_applications' => Application::whereIn('status', [Application::OFFICER_APPROVED, Application::APPROVED_AWAITING_PAYMENT, Application::VERIFIED_BY_OFFICER, Application::REGISTRAR_APPROVED, Application::ISSUED])->count(),
            'returned_applications' => Application::whereIn('status', [Application::CORRECTION_REQUESTED, Application::RETURNED_TO_APPLICANT])->count(),
            'new_this_week' => Application::where('created_at', '>=', now()->startOfWeek())->count(),
        ];

        $statuses = [
            Application::SUBMITTED,
            Application::SUBMITTED_WITH_APP_FEE,
            Application::OFFICER_REVIEW,
            Application::CORRECTION_REQUESTED,
            Application::RETURNED_TO_APPLICANT,
            Application::RETURNED_TO_OFFICER,
            Application::REGISTRAR_FIX_REQUEST,
            Application::APPROVED_AWAITING_PAYMENT,
            Application::FORWARDED_TO_REGISTRAR,
            Application::VERIFIED_BY_OFFICER,
            Application::OFFICER_APPROVED,
            Application::AWAITING_BATCH_PAYMENT,
            Application::PAID_CONFIRMED,
        ];

        $applications = Application::query()
            ->with(['applicant', 'documents'])
            ->withCount('documents')
            ->whereIn('status', $statuses)
            ->where(function($q) use ($user) {
                // Show applications that are NOT assigned to anyone else
                // AND either unassigned OR assigned to current user
                $q->whereNull('assigned_officer_id')
                  ->orWhere('assigned_officer_id', $user->id);
            })
            // Pool visibility logic (respecting lock timeout)
            ->where(function($q) use ($user) {
                $q->whereNull('locked_at')
                  ->orWhere('locked_at', '<=', now()->subHours(2))
                  ->orWhere('locked_by', $user->id);
            })
            ->latest()
            ->paginate(20);

        // Renewals due (expiring within 90 days)
        $cutoff = now()->addDays(90);
        $expiringJournalists = collect();
        $expiringMediaHouses = collect();

        if (class_exists(\App\Models\AccreditationRecord::class) && Schema::hasTable('accreditation_records')) {
            $expiringJournalists = \App\Models\AccreditationRecord::query()
                ->with('holder')
                ->whereNotNull('expires_at')
                ->where('expires_at', '>=', now())
                ->where('expires_at', '<=', $cutoff)
                ->orderBy('expires_at')
                ->limit(10)
                ->get();
        }

        if (class_exists(\App\Models\RegistrationRecord::class) && Schema::hasTable('registration_records')) {
            $expiringMediaHouses = \App\Models\RegistrationRecord::query()
                ->with('contact')
                ->whereNotNull('expires_at')
                ->where('expires_at', '>=', now())
                ->where('expires_at', '<=', $cutoff)
                ->orderBy('expires_at')
                ->limit(10)
                ->get();
        }

        // Inactive media practitioners (2-3 years without login)
        $inactiveJournalists = collect();
        $twoYearsAgo = now()->subYears(2);
        $threeYearsAgo = now()->subYears(3);

        if (class_exists(\App\Models\AccreditationRecord::class) && Schema::hasTable('accreditation_records')) {
            $inactiveJournalists = \App\Models\AccreditationRecord::query()
                ->join('users', 'accreditation_records.holder_user_id', '=', 'users.id')
                ->select('accreditation_records.*', 'users.updated_at as last_activity')
                ->with('holder')
                ->whereNotNull('accreditation_records.holder_user_id')
                ->where('users.updated_at', '<=', $twoYearsAgo)
                ->where('users.updated_at', '>=', $threeYearsAgo)
                ->orderBy('users.updated_at')
                ->limit(10)
                ->get();
        }

        // Trend Analytics
        $trendRange = request()->get('trend_range', '12_months');
        $trendCutoff = now()->subMonths(12);
        $currentRangeLabel = 'Last 12 Months';

        switch ($trendRange) {
            case '30_days': $trendCutoff = now()->subDays(30); $currentRangeLabel = 'Last 30 Days'; break;
            case '90_days': $trendCutoff = now()->subDays(90); $currentRangeLabel = 'Last 90 Days'; break;
            case '6_months': $trendCutoff = now()->subMonths(6); $currentRangeLabel = 'Last 6 Months'; break;
            case 'this_year': $trendCutoff = now()->startOfYear(); $currentRangeLabel = 'This Year (' . date('Y') . ')'; break;
            case 'all_time': $trendCutoff = now()->subYears(10); $currentRangeLabel = 'All Time'; break;
        }

        $accreditationTrends = [];
        $registrationTrends = [];
        $trendLabels = [];

        try {
            $trends = Application::selectRaw("strftime('%Y-%m', created_at) as month, 
                SUM(CASE WHEN application_type = 'accreditation' THEN 1 ELSE 0 END) as acc_count,
                SUM(CASE WHEN application_type = 'registration' THEN 1 ELSE 0 END) as reg_count")
                ->where('created_at', '>=', $trendCutoff)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($trends as $t) {
                $trendLabels[] = date('M Y', strtotime($t->month . '-01'));
                $accreditationTrends[] = (int) $t->acc_count;
                $registrationTrends[] = (int) $t->reg_count;
            }
        } catch (\Exception $e) {}

        return view('staff.officer.dashboard', compact(
            'applications', 'kpis', 'expiringJournalists', 'expiringMediaHouses', 'inactiveJournalists',
            'trendLabels', 'accreditationTrends', 'registrationTrends', 'currentRangeLabel'
        ));
    }

    public function show(Application $application)
{
    // Try to claim the application (concurrency lock)
    if (!$application->claim(auth()->user())) {
        $lockerName = $application->lockedBy ? $application->lockedBy->name : 'another officer';
        return redirect()->back()->with('error', "This application is currently being worked on by {$lockerName}.");
    }

    $application->load([
        'applicant',
        'documents',
        'messages',
        'workflowLogs',
        'lockedBy',
    ]);

    $previousApplications = collect();
    if ($application->applicant_user_id) {
        $previousApplications = Application::where('applicant_user_id', $application->applicant_user_id)
            ->where('id', '!=', $application->id)
            ->latest()
            ->get();
    }

    return view('staff.officer.show', compact('application', 'previousApplications'));
}

public function unlock(Application $application)
{
    if ($application->locked_by === auth()->id()) {
        $application->unlock();
        return redirect()->route('staff.officer.dashboard')->with('success', 'Application released back to pool.');
    }
    return back();
}

    public function openReview(Request $request, Application $application)
    {
        $from = $application->status;

        if ($application->status !== Application::OFFICER_REVIEW) {
            ApplicationWorkflow::transition($application, Application::OFFICER_REVIEW, 'officer_open_review');
        }

        $application->refresh();
        $this->audit('officer_open_review', $application, $from, $application->status);

        return back()->with('success', 'Application moved to Officer Review.');
    }

    /**
     * ✅ Officer Approve -> PUSH TO REGISTRAR DASHBOARD (registrar_review)
     */
public function approve(Request $request, Application $application)
{
    $data = $request->validate([
        'decision_notes' => ['nullable', 'string', 'max:5000'],
        'category_code'  => ['required','string','max:10'],
    ]);

    $from = $application->status;

    if (in_array($application->status, [Application::SUBMITTED, Application::SUBMITTED_WITH_APP_FEE], true)) {
        ApplicationWorkflow::transition($application, Application::OFFICER_REVIEW, 'officer_open_review');
    }

    if ($application->application_type === 'registration') {
        $allowed = array_keys(Application::massMediaCategories());
        abort_unless(in_array($data['category_code'], $allowed, true), 422, 'Invalid media house category.');
        if (Schema::hasColumn('applications','media_house_category_code')) {
            $application->media_house_category_code = $data['category_code'];
        }
    } else {
        $allowed = array_keys(Application::accreditationCategories());
        abort_unless(in_array($data['category_code'], $allowed, true), 422, 'Invalid accreditation category.');
        if (Schema::hasColumn('applications','accreditation_category_code')) {
            $application->accreditation_category_code = $data['category_code'];
        }
    }
    $application->save();

    if ($application->application_type === 'registration') {
        ApplicationWorkflow::transition($application, Application::VERIFIED_BY_OFFICER, 'officer_verify_media_house', [
            'decision_notes' => $data['decision_notes'] ?? null,
            'category_code' => $data['category_code'],
        ]);

        ApplicationWorkflow::transition($application, Application::REGISTRAR_REVIEW, 'system_send_to_registrar', [
            'category_code' => $data['category_code'],
        ]);

        $successMessage = 'Media house verified and forwarded to Registrar for review.';
    } else {
        ApplicationWorkflow::transition($application, Application::APPROVED_AWAITING_PAYMENT, 'officer_approve', [
            'decision_notes' => $data['decision_notes'] ?? null,
            'category_code' => $data['category_code'],
        ]);

        $successMessage = 'Approved — applicant will be prompted for payment.';
    }

    if (!empty($data['decision_notes']) && Schema::hasColumn('applications','decision_notes')) {
        $application->decision_notes = $data['decision_notes'];
        $application->save();
    }

    $this->audit('officer_approve', $application, $from, $application->status, [
        'notes' => $data['decision_notes'] ?? null,
        'category_code' => $data['category_code'],
    ]);

    return back()->with('success', $successMessage);
}


    public function reject(Request $request, Application $application)
    {
        abort(403, 'Accreditation Officers cannot reject applications. Use Request Correction or forward to Registrar.');
    }

    public function requestCorrection(Request $request, Application $application)
    {
        $data = $request->validate([
            'notes' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        if (in_array($application->status, [Application::SUBMITTED, Application::SUBMITTED_WITH_APP_FEE], true)) {
            ApplicationWorkflow::transition($application, Application::OFFICER_REVIEW, 'officer_open_review');
        }

        $this->safeSet($application, [
            'correction_notes' => $data['notes'],
            'decision_notes' => $data['notes'],
        ]);

        ApplicationWorkflow::transition($application, Application::RETURNED_TO_APPLICANT, 'officer_return_to_applicant', [
            'notes' => $data['notes'],
        ]);

        $application->refresh();

        $this->audit('officer_return_to_applicant', $application, $from, $application->status, [
            'notes' => $data['notes'],
        ]);

        $this->persistMessageIfAvailable($application, $data['notes']);

        return back()->with('success', 'Application returned to applicant with correction notes.');
    }

    public function forwardToRegistrar(Request $request, Application $application)
    {
        $data = $request->validate([
            'forward_reason' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        if (in_array($application->status, [Application::SUBMITTED, Application::SUBMITTED_WITH_APP_FEE], true)) {
            ApplicationWorkflow::transition($application, Application::OFFICER_REVIEW, 'officer_open_review');
        }

        $this->safeSet($application, [
            'forward_reason' => $data['forward_reason'],
        ]);

        ApplicationWorkflow::transition($application, Application::FORWARDED_TO_REGISTRAR, 'officer_forward_to_registrar', [
            'forward_reason' => $data['forward_reason'],
        ]);

        $application->refresh();

        $this->audit('officer_forward_to_registrar', $application, $from, $application->status, [
            'forward_reason' => $data['forward_reason'],
        ]);

        return back()->with('success', 'Application forwarded to Registrar (without approval).');
    }

    public function physicalIntake()
    {
        return view('staff.officer.physical_intake');
    }

    public function processPhysicalIntake(Request $request)
    {
        $data = $request->validate([
            'application_type' => ['required', 'in:accreditation,registration'],
            'request_type' => ['required', 'in:new,renewal,replacement'],
            'lookup_number' => ['required_unless:request_type,new', 'nullable', 'string', 'max:100'],
            'receipt_number' => ['required', 'string', 'max:100'],
            'first_name' => ['required_if:request_type,==,new', 'nullable', 'string', 'max:100'],
            'surname' => ['required_if:request_type,==,new', 'nullable', 'string', 'max:100'],
            'id_number' => ['required_if:request_type,==,new', 'nullable', 'string', 'max:50'],
            'category' => ['required_if:application_type,==,accreditation', 'nullable', 'string', 'max:50'],
            'email' => ['required_if:request_type,==,new', 'nullable', 'email', 'max:255'],
            'phone' => ['required_if:request_type,==,new', 'nullable', 'string', 'max:20'],
            'physical_address' => ['required_if:request_type,==,new', 'nullable', 'string', 'max:500'],
            'city' => ['required_if:request_type,==,new', 'nullable', 'string', 'max:100'],
            'province' => ['required_if:request_type,==,new', 'nullable', 'string', 'max:50'],
            'applicant_photo' => ['required_if:application_type,==,accreditation', 'nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            
            // Media House Specific Fields
            'entity_name' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:255'],
            'trading_name' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:255'],
            'business_registration' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:100'],
            'tax_number' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:100'],
            'business_type' => ['required_if:application_type,==,registration', 'nullable', 'string'],
            'ownership_type' => ['required_if:application_type,==,registration', 'nullable', 'string'],
            'local_ownership' => ['required_if:application_type,==,registration', 'nullable', 'numeric', 'min:0', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'postal_address' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:255'],
            'media_category' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:50'],
            'publication_name' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:255'],
            'publication_frequency' => ['required_if:application_type,==,registration', 'nullable', 'string'],
            'editor_name' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:255'],
            'editor_contact' => ['required_if:application_type,==,registration', 'nullable', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $application = null;
            $applicantId = null;
            
            // Normalize receipt number if it came from the renewal field
            $receiptNumber = $data['receipt_number'] ?? ($request->input('renewal_receipt_number') ?? 'UNKNOWN');

            if ($data['request_type'] === 'new') {
                $applicantId = $this->getOrCreateWalkinApplicant($data);
                
                // Generate accreditation/registration number
                $accreditationNumber = $this->generateAccreditationNumber($data['application_type']);
                
                // Handle photo upload
                $photoPath = null;
                if ($request->hasFile('applicant_photo')) {
                    $photo = $request->file('applicant_photo');
                    $photoPath = $photo->store('applicant_photos', 'public');
                }
                
                // Construct form_data based on application type
                $formData = [
                    'first_name' => $data['first_name'],
                    'surname' => $data['surname'],
                    'id_number' => $data['id_number'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'physical_address' => $data['physical_address'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'receipt_number' => $receiptNumber,
                ];

                if ($data['application_type'] === 'accreditation') {
                    $formData['category'] = $data['category'];
                    $formData['accreditation_number'] = $accreditationNumber;
                    $formData['applicant_photo'] = $photoPath;
                } else {
                    $formData['registration_number'] = $accreditationNumber; // It's just a generated number
                    $formData['business_details'] = [
                        'entity_name' => $data['entity_name'],
                        'trading_name' => $data['trading_name'],
                        'business_registration' => $data['business_registration'],
                        'tax_number' => $data['tax_number'],
                        'business_type' => $data['business_type'],
                        'ownership_type' => $data['ownership_type'],
                        'local_ownership' => $data['local_ownership'],
                        'website' => $data['website'],
                        'postal_address' => $data['postal_address'],
                        'publication_name' => $data['publication_name'],
                        'publication_frequency' => $data['publication_frequency'],
                        'media_category' => $data['media_category'],
                        'editor_name' => $data['editor_name'],
                        'editor_contact' => $data['editor_contact'],
                    ];
                }

                $application = Application::create([
                    'reference' => $this->generateReference(),
                    'application_type' => $data['application_type'],
                    'request_type' => $data['request_type'],
                    'applicant_user_id' => $applicantId,
                    'status' => Application::PAID_CONFIRMED,
                    'submitted_at' => now(),
                    'assigned_officer_id' => Auth::id(),
                    'assigned_at' => now(),
                    'collection_region' => Auth::user()->region,
                    'form_data' => $formData,
                ]);

                // Store category codes
                if ($data['application_type'] === 'accreditation') {
                    $application->accreditation_category_code = $data['category'];
                } else {
                    $application->media_house_category_code = $data['media_category'] ?? null;
                }
                $application->save();

            } else {
                $application = $this->findExistingApplication($data['lookup_number'], $data['application_type']);
                
                if (!$application) {
                    $record = $this->findExistingRecord($data['lookup_number'], $data['application_type']);
                    if (!$record) {
                        throw new \Exception("Could not find existing record for " . $data['lookup_number']);
                    }
                    
                    $application = Application::create([
                        'reference' => $this->generateReference(),
                        'application_type' => $data['application_type'],
                        'request_type' => $data['request_type'],
                        'applicant_user_id' => $record->holder_user_id ?? $record->media_house_id ?? $this->getOrCreateWalkinApplicant($data),
                        'status' => Application::PAID_CONFIRMED,
                        'submitted_at' => now(),
                        'assigned_officer_id' => Auth::id(),
                        'assigned_at' => now(),
                        'collection_region' => Auth::user()->region,
                    ]);
                } else {
                    $application->update([
                        'status' => Application::PAID_CONFIRMED,
                        'request_type' => $data['request_type'],
                        'assigned_officer_id' => Auth::id(),
                        'assigned_at' => now(),
                    ]);
                }

                // Handle additional renewal data (only for renewals)
                $formData = $application->form_data ?? [];
                if ($data['application_type'] === 'accreditation') {
                    $formData['personal_details'] = array_merge($formData['personal_details'] ?? [], [
                        'full_name' => $request->input('renewal_first_name') . ' ' . $request->input('renewal_surname'),
                        'id_number' => $request->input('renewal_id_number'),
                        'province' => $request->input('renewal_province'),
                        'receipt_number' => $receiptNumber,
                    ]);
                    $application->accreditation_category_code = $request->input('renewal_category') ?? $application->accreditation_category_code;
                } else {
                    $formData['business_details'] = array_merge($formData['business_details'] ?? [], [
                        'trading_name' => $request->input('renewal_trading_name') ?? $request->input('renewal_first_name'),
                        'business_address' => $request->input('renewal_physical_address'),
                        'receipt_number' => $receiptNumber,
                    ]);
                    $application->media_house_category_code = $request->input('renewal_category') ?? $application->media_house_category_code;
                }
                $application->update(['form_data' => $formData, 'notes' => $request->input('notes')]);
            }

            $application->refresh();

            // Create Payment & Receipt
            Payment::create([
                'application_id' => $application->id,
                'amount' => 0,
                'method' => 'cash',
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'recorded_by' => Auth::id(),
                'receipt_number' => $receiptNumber,
            ]);

            Receipt::create([
                'application_id' => $application->id,
                'applicant_id' => $application->applicant_id,
                'receipt_number' => $receiptNumber,
                'payment_reference' => $application->reference . '-WI',
                'amount' => 0,
                'payment_date' => now(),
                'status' => Receipt::STATUS_VERIFIED,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'generated_by' => Auth::id(),
                'notes' => 'Walk-in intake: ' . ($data['notes'] ?? 'No notes provided'),
            ]);

            ActivityLogger::log('physical_intake_recorded', $application, null, Application::PAID_CONFIRMED, [
                'receipt_number' => $data['receipt_number'],
                'officer_id' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->route('staff.production.dashboard')->with('success', 'Walk-in application recorded and added to Production Queue.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    private function generateReference()
    {
        return 'ZMC-WI-' . strtoupper(Str::random(6)) . '-' . date('Y');
    }

    private function getOrCreateWalkinApplicant($data)
    {
        $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['surname'] ?? ''));
        $idNumber = $data['id_number'] ?? 'NONE';
        
        $user = User::where('id_number', $idNumber)
            ->orWhere(function($query) use ($fullName, $idNumber) {
                $query->where('name', $fullName)
                      ->orWhere('id_number', $idNumber);
            })
            ->first();

        if (!$user) {
            $email = 'walkin_' . Str::random(8) . '@zmc-portal.org.zw';
            $user = User::create([
                'name' => $fullName,
                'email' => $data['email'] ?? $email,
                'password' => bcrypt(Str::random(16)),
                'id_number' => $idNumber,
                'province' => $data['province'] ?? null,
                'category' => $data['category'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['physical_address'] ?? null,
                'city' => $data['city'] ?? null,
            ]);
        }
        
        return $user->id;
    }

    private function generateAccreditationNumber($applicationType)
    {
        if ($applicationType === 'accreditation') {
            // Generate accreditation number for media practitioners
            $year = date('Y');
            $sequence = \App\Models\Application::where('application_type', 'accreditation')
                ->whereYear('created_at', $year)
                ->count() + 1;
            return 'J' . str_pad($sequence, 6, '0', STR_PAD_LEFT) . $year;
        } else {
            // Generate registration number for media houses
            $year = date('Y');
            $sequence = \App\Models\Application::where('application_type', 'registration')
                ->whereYear('created_at', $year)
                ->count() + 1;
            return 'MC' . str_pad($sequence, 6, '0', STR_PAD_LEFT) . $year;
        }
    }

    private function findExistingRecord($number, $type)
    {
        if ($type === 'accreditation') {
            return AccreditationRecord::where('record_number', $number)
                ->orWhere('certificate_no', $number)
                ->first();
        } else {
            return RegistrationRecord::where('record_number', $number)
                ->orWhere('certificate_no', $number)
                ->first();
        }
    }

    private function findExistingApplication($number, $type)
    {
        return Application::where('reference', $number)
            ->where('application_type', $type)
            ->first();
    }

    public function productionQueue(Request $request)
    {
        $applications = $this->applicationsBaseQuery('production', $request)
            ->paginate(20)
            ->withQueryString();

        return view('staff.officer.production_queue', compact('applications'));
    }

    /**
     * Display paginated list of accredited media practitioners with filters
     */
    public function accreditedJournalists(Request $request)
    {
        $query = \App\Models\AccreditationRecord::query()
            ->with('holder')
            ->orderBy('issued_at', 'desc');

        // Filter by search term (name or certificate number)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_no', 'like', "%{$search}%")
                  ->orWhereHas('holder', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by collection status
        if ($request->filled('collection_status')) {
            $status = $request->input('collection_status');
            if ($status === 'collected') {
                $query->whereNotNull('collected_at');
            } elseif ($status === 'uncollected') {
                $query->whereNull('collected_at');
            }
        }

        // Filter by expiry status
        if ($request->filled('expiry_status')) {
            $status = $request->input('expiry_status');
            if ($status === 'expiring_soon') {
                $query->whereNotNull('expires_at')
                      ->where('expires_at', '>=', now())
                      ->where('expires_at', '<=', now()->addDays(90));
            } elseif ($status === 'expired') {
                $query->whereNotNull('expires_at')
                      ->where('expires_at', '<', now());
            }
        }

        $journalists = $query->paginate(20)->appends($request->query());

        // --- Analytics Data ---
        $currentYear = now()->year;
        $recordsThisYear = \App\Models\AccreditationRecord::whereYear('issued_at', $currentYear)->with('application')->get();
        
        $monthlyCounts = array_fill(1, 12, 0);
        $categoryCounts = [];
        
        foreach($recordsThisYear as $rec) {
            $m = $rec->issued_at ? (int)$rec->issued_at->format('n') : null;
            if ($m) $monthlyCounts[$m]++;
            
            $cat = $rec->application ? $rec->application->categoryLabel() : 'Unknown';
            $cat = $cat ?: 'Other';
            $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
        }

        $chartData = [
            'months' => array_values($monthlyCounts),
            'categories' => [
                'labels' => array_keys($categoryCounts),
                'data' => array_values($categoryCounts)
            ]
        ];

        return view('staff.officer.accredited_journalists', compact('journalists', 'chartData', 'currentYear'));
    }

    /**
     * Display paginated list of registered media houses with filters
     */
    public function registeredMediaHouses(Request $request)
    {
        $query = \App\Models\RegistrationRecord::query()
            ->with('contact')
            ->orderBy('issued_at', 'desc');

        // Filter by search term (entity name or registration number)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                  ->orWhere('entity_name', 'like', "%{$search}%");
            });
        }

        // Filter by collection status
        if ($request->filled('collection_status')) {
            $status = $request->input('collection_status');
            if ($status === 'collected') {
                $query->whereNotNull('collected_at');
            } elseif ($status === 'uncollected') {
                $query->whereNull('collected_at');
            }
        }

        // Filter by expiry status
        if ($request->filled('expiry_status')) {
            $status = $request->input('expiry_status');
            if ($status === 'expiring_soon') {
                $query->whereNotNull('expires_at')
                      ->where('expires_at', '>=', now())
                      ->where('expires_at', '<=', now()->addDays(90));
            } elseif ($status === 'expired') {
                $query->whereNotNull('expires_at')
                      ->where('expires_at', '<', now());
            }
        }

        $mediaHouses = $query->paginate(20)->appends($request->query());

        // --- Analytics Data ---
        $currentYear = now()->year;
        $recordsThisYear = \App\Models\RegistrationRecord::whereYear('issued_at', $currentYear)->with('application')->get();
        
        $monthlyCounts = array_fill(1, 12, 0);
        $categoryCounts = [];
        
        foreach($recordsThisYear as $rec) {
            $m = $rec->issued_at ? (int)$rec->issued_at->format('n') : null;
            if ($m) $monthlyCounts[$m]++;
            
            $cat = $rec->application ? $rec->application->categoryLabel() : 'Unknown';
            $cat = $cat ?: 'Other';
            $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
        }

        $chartData = [
            'months' => array_values($monthlyCounts),
            'categories' => [
                'labels' => array_keys($categoryCounts),
                'data' => array_values($categoryCounts)
            ]
        ];

        return view('staff.officer.registered_mediahouses', compact('mediaHouses', 'chartData', 'currentYear'));
    }

    /**
     * AJAX: Get record data for editing
     */
    public function getRecordData(Request $request, $id)
    {
        $type = $request->query('type');
        
        try {
            if ($type === 'accreditation') {
                $record = \App\Models\AccreditationRecord::with('application')->findOrFail($id);
                $fd = $record->application ? ($record->application->form_data ?? []) : [];
                return response()->json(['success' => true, 'data' => $fd]);
            } elseif ($type === 'registration') {
                $record = \App\Models\RegistrationRecord::with('application')->findOrFail($id);
                $fd = $record->application ? ($record->application->form_data ?? []) : [];
                return response()->json(['success' => true, 'data' => $fd]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Record not found.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid record type.']);
    }

    /**
     * Update Record Data from Modal
     */
    public function updateRecordData(Request $request)
    {
        $request->validate([
            'record_type' => 'required|in:accreditation,registration',
            'record_id' => 'required|integer',
            'form_data' => 'required|array',
        ]);

        $type = $request->input('record_type');
        $id = $request->input('record_id');
        $updates = $request->input('form_data');

        if ($type === 'accreditation') {
            $record = \App\Models\AccreditationRecord::with('application')->findOrFail($id);
            if ($record->application) {
                $fd = $record->application->form_data ?? [];
                foreach ($updates as $k => $v) {
                    $fd[$k] = $v;
                }
                $record->application->form_data = $fd;
                $record->application->save();
            }
        } elseif ($type === 'registration') {
            $record = \App\Models\RegistrationRecord::with('application')->findOrFail($id);
            if ($record->application) {
                $fd = $record->application->form_data ?? [];
                foreach ($updates as $k => $v) {
                    $fd[$k] = $v;
                }
                $record->application->form_data = $fd;
                $record->application->save();
            }
        }

        return back()->with('success', 'Record data updated successfully.');
    }

    /**
     * Send collection notification to journalists/media houses
     */
    public function sendCollectionNotification(Request $request)
    {
        $data = $request->validate([
            'record_type' => ['required', 'in:accreditation,registration'],
            'record_ids' => ['nullable', 'array'],
            'record_ids.*' => ['integer'],
        ]);

        $notificationService = new \App\Services\NotificationService();
        $count = 0;
        $type = $data['record_type'];
        $ids = $data['record_ids'] ?? [];

        if ($type === 'accreditation') {
            $query = \App\Models\AccreditationRecord::query()->with('holder')->whereNull('collected_at');
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
            $records = $query->get();

            foreach ($records as $record) {
                if ($notificationService->sendCollectionReminder($record, 'accreditation')) {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($record)
                        ->log('collection_reminder_sent');
                    $count++;
                }
            }
        } else {
            $query = \App\Models\RegistrationRecord::query()->with('contact')->whereNull('collected_at');
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
            $records = $query->get();

            foreach ($records as $record) {
                if ($notificationService->sendCollectionReminder($record, 'registration')) {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($record)
                        ->log('collection_reminder_sent');
                    $count++;
                }
            }
        }

        return back()->with('success', "Collection reminders sent to {$count} " . ($type === 'accreditation' ? 'media practitioners' : 'media houses') . ".");
    }

    /**
     * Display all applications with comprehensive filtering
     */
    public function allApplications(Request $request)
    {
        $query = Application::query()
            ->with(['applicant', 'documents'])
            ->latest();

        // Apply filters
        if ($request->filled('name')) {
            $search = $request->input('name');
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('application_type')) {
            $query->where('application_type', $request->input('application_type'));
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->input('request_type'));
        }

        if ($request->filled('local_foreign')) {
            $ownership = $request->input('local_foreign');
            $query->where('application_type', 'registration')
                  ->where(function($q) use ($ownership) {
                      $q->whereRaw("JSON_EXTRACT(form_data, '$.ownership_type') = ?", [$ownership])
                        ->orWhere('form_data', 'like', '%"ownership_type":"' . $ownership . '"%');
                  });
        }

        if ($request->filled('email')) {
            $email = $request->input('email');
            $query->whereHas('applicant', function ($q) use ($email) {
                $q->where('email', 'like', "%{$email}%");
            });
        }

        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->input('reference') . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }

        $applications = $query->paginate(20)->withQueryString();

        return view('staff.officer.applications_list', [
            'applications' => $applications,
            'title' => 'All Applications',
            'list' => 'all'
        ]);
    }

    /**
     * Display recent applications (incoming ones)
     */
    public function recentApplications(Request $request)
    {
        $query = Application::query()
            ->with(['applicant', 'documents'])
            ->whereIn('status', [
                Application::SUBMITTED,
                Application::SUBMITTED_WITH_APP_FEE,
                Application::OFFICER_REVIEW,
            ])
            ->latest();

        // Apply same filters as all applications
        $this->applyFilters($query, $request);

        $applications = $query->paginate(20)->withQueryString();

        return view('staff.officer.applications_list', [
            'applications' => $applications,
            'title' => 'Recent Applications',
            'list' => 'new'
        ]);
    }

    /**
     * Display pending review applications
     */
    public function pendingReview(Request $request)
    {
        $query = Application::query()
            ->with(['applicant', 'documents'])
            ->whereIn('status', [
                Application::SUBMITTED,
                Application::SUBMITTED_WITH_APP_FEE,
                Application::OFFICER_REVIEW,
            ])
            ->where(function($q) {
                $q->whereNull('assigned_officer_id')
                  ->orWhere('assigned_officer_id', auth()->id());
            })
            ->latest();

        // Apply same filters as all applications
        $this->applyFilters($query, $request);

        $applications = $query->paginate(20)->withQueryString();

        return view('staff.officer.applications_list', [
            'applications' => $applications,
            'title' => 'Pending Review',
            'list' => 'pending'
        ]);
    }

    /**
     * Display approved applications
     */
    public function approvedApplications(Request $request)
    {
        $query = Application::query()
            ->with(['applicant', 'documents'])
            ->whereIn('status', [
                Application::OFFICER_APPROVED,
                Application::APPROVED_AWAITING_PAYMENT,
                Application::VERIFIED_BY_OFFICER,
                Application::REGISTRAR_APPROVED,
                Application::PAID_CONFIRMED,
                Application::PRODUCTION_QUEUE,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ])
            ->latest();

        // Apply same filters as all applications
        $this->applyFilters($query, $request);

        $applications = $query->paginate(20)->withQueryString();

        return view('staff.officer.applications_list', [
            'applications' => $applications,
            'title' => 'Approved Applications',
            'list' => 'approved'
        ]);
    }

    /**
     * Display returned applications
     */
    public function returnedApplications(Request $request)
    {
        $query = Application::query()
            ->with(['applicant', 'documents'])
            ->whereIn('status', [
                Application::CORRECTION_REQUESTED,
                Application::RETURNED_TO_APPLICANT,
                Application::RETURNED_TO_OFFICER,
            ])
            ->latest();

        // Apply same filters as all applications
        $this->applyFilters($query, $request);

        $applications = $query->paginate(20)->withQueryString();

        return view('staff.officer.applications_list', [
            'applications' => $applications,
            'title' => 'Returned Applications',
            'list' => 'returned'
        ]);
    }

    /**
     * Seek guidance from Registrar for complicated applications
     */
    public function seekGuidance(Request $request)
    {
        $data = $request->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'guidance_reason' => ['required', 'string', 'max:5000'],
        ]);

        $application = Application::findOrFail($data['application_id']);
        $from = $application->status;

        // Forward to Registrar with guidance request
        ApplicationWorkflow::transition($application, Application::FORWARDED_TO_REGISTRAR, 'officer_seek_guidance', [
            'guidance_reason' => $data['guidance_reason'],
        ]);

        $this->audit('officer_seek_guidance', $application, $from, $application->status, [
            'guidance_reason' => $data['guidance_reason'],
        ]);

        return back()->with('success', 'Application forwarded to Registrar for guidance and review.');
    }

    /**
     * Export applications to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = Application::query()->with(['applicant']);

        // Apply same filters
        $this->applyFilters($query, $request);

        $applications = $query->get();

        $filename = 'applications_export_' . now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Reference Number',
                'Applicant Name',
                'Email Address',
                'Application Type',
                'Request Type',
                'Submission Date',
                'Submission Time',
                'Status'
            ]);

            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->reference ?? ('#' . $app->id),
                    $app->applicant->name ?? '—',
                    $app->applicant->email ?? '—',
                    $app->applicationTypeLabel(),
                    ucfirst($app->request_type ?? '—'),
                    optional($app->submitted_at)->format('Y-m-d') ?? optional($app->created_at)->format('Y-m-d'),
                    optional($app->submitted_at)->format('H:i') ?? optional($app->created_at)->format('H:i'),
                    $app->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export applications to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Application::query()->with(['applicant']);

        // Apply same filters
        $this->applyFilters($query, $request);

        $applications = $query->get();

        $data = [
            'applications' => $applications,
            'title' => 'Applications Export',
            'exportDate' => now()->format('Y-m-d H:i'),
        ];

        $pdf = Pdf::loadView('staff.officer.exports.applications_pdf', $data);
        
        $filename = 'applications_export_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Apply common filters to application queries
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('name')) {
            $search = $request->input('name');
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('application_type')) {
            $query->where('application_type', $request->input('application_type'));
        }

        if ($request->filled('request_type')) {
            $query->where('request_type', $request->input('request_type'));
        }

        if ($request->filled('local_foreign')) {
            $ownership = $request->input('local_foreign');
            $query->where('application_type', 'registration')
                  ->where(function($q) use ($ownership) {
                      $q->whereRaw("JSON_EXTRACT(form_data, '$.ownership_type') = ?", [$ownership])
                        ->orWhere('form_data', 'like', '%"ownership_type":"' . $ownership . '"%');
                  });
        }

        if ($request->filled('email')) {
            $email = $request->input('email');
            $query->whereHas('applicant', function ($q) use ($email) {
                $q->where('email', 'like', "%{$email}%");
            });
        }

        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->input('reference') . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->input('month'));
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->input('year'));
        }

        return $query;
    }

    /**
     * View full accredited journalist record
     */
    public function viewFullAccreditedJournalist($id)
    {
        $journalist = AccreditationRecord::with(['application', 'application.applicant', 'application.documents', 'holder'])
            ->findOrFail($id);

        $formData = $journalist->application?->form_data ?? [];
        
        return view('staff.officer.records.accredited_journalist_full', compact('journalist', 'formData'));
    }

    /**
     * View full registered media house record
     */
    public function viewFullRegisteredMediaHouse($id)
    {
        $mediahouse = RegistrationRecord::with(['application', 'application.applicant', 'application.documents', 'contact'])
            ->findOrFail($id);

        $formData = $mediahouse->application?->form_data ?? [];
        
        return view('staff.officer.records.registered_mediahouse_full', compact('mediahouse', 'formData'));
    }

    /**
     * Edit accredited journalist record
     */
    public function editAccreditedJournalist($id)
    {
        $journalist = AccreditationRecord::with(['application', 'application.applicant', 'holder'])
            ->findOrFail($id);

        $formData = $journalist->application?->form_data ?? [];
        
        return view('staff.officer.records.edit_accredited_journalist', compact('journalist', 'formData'));
    }

    /**
     * Edit registered media house record
     */
    public function editRegisteredMediaHouse($id)
    {
        $mediahouse = RegistrationRecord::with(['application', 'application.applicant', 'contact'])
            ->findOrFail($id);

        $formData = $mediahouse->application?->form_data ?? [];
        
        return view('staff.officer.records.edit_registered_mediahouse', compact('mediahouse', 'formData'));
    }

    /**
     * Update accredited journalist record
     */
    public function updateAccreditedJournalist(Request $request, $id)
    {
        $journalist = AccreditationRecord::with(['application'])->findOrFail($id);
        
        $data = $request->validate([
            'edit_reason' => ['required', 'string', 'max:1000'],
            // Add validation for editable fields
            'organization' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'home_address' => ['nullable', 'string', 'max:500'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email_address' => ['nullable', 'email', 'max:255'],
        ]);

        // Create edit request for registrar approval
        $editRequest = \App\Models\EditRequest::create([
            'record_type' => 'accreditation',
            'record_id' => $journalist->id,
            'requested_by' => auth()->id(),
            'edit_reason' => $data['edit_reason'],
            'old_data' => $journalist->application->form_data ?? [],
            'new_data' => $request->except(['_token', 'edit_reason']),
            'status' => 'pending',
        ]);

        ActivityLogger::log('edit_request_submitted', $journalist->application, null, null, [
            'edit_request_id' => $editRequest->id,
            'edit_reason' => $data['edit_reason'],
        ]);

        return back()->with('success', 'Edit request submitted for registrar approval.');
    }

    /**
     * Update registered media house record
     */
    public function updateRegisteredMediaHouse(Request $request, $id)
    {
        $mediahouse = RegistrationRecord::with(['application'])->findOrFail($id);
        
        $data = $request->validate([
            'edit_reason' => ['required', 'string', 'max:1000'],
            // Add validation for editable fields
            'trading_name' => ['nullable', 'string', 'max:255'],
            'physical_address' => ['nullable', 'string', 'max:500'],
            'postal_address' => ['nullable', 'string', 'max:500'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_title' => ['nullable', 'string', 'max:50'],
        ]);

        // Create edit request for registrar approval
        $editRequest = \App\Models\EditRequest::create([
            'record_type' => 'registration',
            'record_id' => $mediahouse->id,
            'requested_by' => auth()->id(),
            'edit_reason' => $data['edit_reason'],
            'old_data' => $mediahouse->application->form_data ?? [],
            'new_data' => $request->except(['_token', 'edit_reason']),
            'status' => 'pending',
        ]);

        ActivityLogger::log('edit_request_submitted', $mediahouse->application, null, null, [
            'edit_request_id' => $editRequest->id,
            'edit_reason' => $data['edit_reason'],
        ]);

        return back()->with('success', 'Edit request submitted for registrar approval.');
    }

    /**
     * Download documents for accredited journalist
     */
    public function downloadAccreditedJournalistDocuments($id)
    {
        $journalist = AccreditationRecord::with(['application', 'application.documents'])
            ->findOrFail($id);

        $documents = $journalist->application->documents;
        
        if ($documents->isEmpty()) {
            return back()->with('error', 'No documents found for this record.');
        }

        // Create ZIP file with all documents
        $zipFileName = 'accreditation_documents_' . $journalist->certificate_no . '_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($documents as $document) {
                $filePath = storage_path('app/' . $document->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $document->doc_type . '_' . $document->original_filename);
                }
            }
            $zip->close();
        }

        ActivityLogger::log('documents_downloaded', $journalist->application, null, null, [
            'document_count' => $documents->count(),
            'zip_file' => $zipFileName,
        ]);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Download documents for registered media house
     */
    public function downloadRegisteredMediaHouseDocuments($id)
    {
        $mediahouse = RegistrationRecord::with(['application', 'application.documents'])
            ->findOrFail($id);

        $documents = $mediahouse->application->documents;
        
        if ($documents->isEmpty()) {
            return back()->with('error', 'No documents found for this record.');
        }

        // Create ZIP file with all documents
        $zipFileName = 'registration_documents_' . $mediahouse->registration_no . '_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($documents as $document) {
                $filePath = storage_path('app/' . $document->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $document->doc_type . '_' . $document->original_filename);
                }
            }
            $zip->close();
        }

        ActivityLogger::log('documents_downloaded', $mediahouse->application, null, null, [
            'document_count' => $documents->count(),
            'zip_file' => $zipFileName,
        ]);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Export accredited journalists to CSV
     */
    public function exportAccreditedJournalists(Request $request)
    {
        $query = AccreditationRecord::with(['application', 'application.applicant', 'holder']);

        // Apply filters
        $this->applyAccreditationFilters($query, $request);

        $journalists = $query->get();

        $filename = 'accredited_journalists_export_' . now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($journalists) {
            $file = fopen('php://output', 'w');
            
            // CSV Header - All 18 fields
            fputcsv($file, [
                'Certificate Number',
                'Applicant Name',
                'Email',
                'Organization',
                'Category',
                'Valid From',
                'Valid To',
                'Year',
                'ID Number',
                'Marital Status',
                'Sex',
                'Date of Birth',
                'Birth Place',
                'Nationality',
                'Home Address',
                'Town',
                'Phone',
                'Cell',
                'Medium',
                'Designation',
                'Status',
                'Collection Status'
            ]);

            foreach ($journalists as $journalist) {
                $app = $journalist->application;
                $formData = $app ? $app->form_data : [];
                $holder = $journalist->holder;

                fputcsv($file, [
                    $journalist->certificate_no ?? '',
                    $holder?->name ?? ($formData['first_name'] ?? '' . ' ' . $formData['surname'] ?? ''),
                    $holder?->email ?? '',
                    $formData['organization'] ?? $formData['employer'] ?? '',
                    $app?->categoryLabel() ?? '',
                    optional($journalist->issued_at)->format('Y-m-d') ?? '',
                    optional($journalist->expires_at)->format('Y-m-d') ?? '',
                    $journalist->year ?? optional($journalist->issued_at)->format('Y') ?? '',
                    $formData['id_number'] ?? $formData['national_id'] ?? '',
                    $formData['marital_status'] ?? '',
                    $formData['sex'] ?? $formData['gender'] ?? '',
                    $formData['date_of_birth'] ?? '',
                    $formData['place_of_birth'] ?? '',
                    $formData['nationality'] ?? '',
                    $formData['home_address'] ?? $formData['address'] ?? '',
                    $formData['town'] ?? $formData['city'] ?? '',
                    $holder?->phone ?? $formData['phone_number'] ?? '',
                    $holder?->phone ?? $formData['cell_number'] ?? '',
                    $formData['medium'] ?? '',
                    $formData['designation'] ?? $formData['job_title'] ?? '',
                    $journalist->status ?? 'active',
                    $journalist->collected_at ? 'Collected' : 'Uncollected'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export registered media houses to CSV with all fields
     */
    public function exportRegisteredMediaHouses(Request $request)
    {
        $query = RegistrationRecord::with(['application', 'application.applicant', 'contact']);

        // Apply filters
        $this->applyRegistrationFilters($query, $request);

        $mediahouses = $query->get();

        $format = $request->input('format', 'csv');
        $includeDetails = $request->input('include_details', true);
        
        if ($format === 'pdf') {
            return $this->exportMediaHousesToPDF($mediahouses, $includeDetails);
        }

        $filename = 'registered_media_houses_export_' . now()->format('Ymd_His') . '.' . $format;
        
        $headers = [
            'Content-Type' => $format === 'excel' ? 'application/vnd.ms-excel' : 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($mediahouses, $includeDetails) {
            $file = fopen('php://output', 'w');
            
            // Comprehensive CSV Header - All requested fields
            $headers = [
                'Registration Number (Primary key)',
                'Organization / Media company',
                'Directors (Indicate their sex)',
                'Shareholding structure',
                'Office address',
                'Telephone(s)',
                'Phone',
                'Email',
                'Website',
                'Category',
                'Registration status',
                'Registration date',
                'Registration year',
                'Publication / Service',
                'Type (Digital platform, Magazine, Newspaper, Newsletter, News Agency, Production House, Advertising)',
                'Print/online (Yes/No, or Both)',
                'Frequency (whether daily, weekly, bi-weekly / fortnight, monthly, bi-monthly, quarterly, annually e.t.c)',
                'Language',
                'Focus (Type of content)',
                'Scope (National, Community, Foreign, Regional, Advertising, Online, Print, In-house, Production)',
                'Reach (The cities or towns)',
                'Provincial reach (All 10 provinces)',
                'Editor/Manager / Head',
                'Web address',
                'Email',
                'Telephone',
                'Phone',
                'Operational status (Yes/No)',
                'Previous renewal year',
                'License status',
                'License expiry date',
                'License expiry year'
            ];

            // Add services/publications headers if detailed export requested
            if ($includeDetails) {
                $headers[] = 'Services/Publications Details';
                $headers[] = 'Service/Publication Type';
                $headers[] = 'Print/Online';
                $headers[] = 'Frequency';
                $headers[] = 'Language';
                $headers[] = 'Focus';
                $headers[] = 'Scope';
                $headers[] = 'Reach';
                $headers[] = 'Contact Person';
                $headers[] = 'Contact Email';
                $headers[] = 'Contact Phone';
            }

            fputcsv($file, $headers);

            foreach ($mediahouses as $mediahouse) {
                $app = $mediahouse->application;
                $formData = $app ? $app->form_data : [];
                $contact = $mediahouse->contact;
                $directors = $formData['directors'] ?? [];
                $services = $formData['services'] ?? [];

                // Build directors info with sex
                $directorsInfo = [];
                foreach ($directors as $director) {
                    $directorsInfo[] = ($director['name'] ?? '') . 
                                       ($director['sex'] ? ' (' . ucfirst($director['sex']) . ')' : '') . 
                                       ($director['telephone'] ? ' - Tel: ' . $director['telephone'] : '');
                }
                $directorsStr = implode('; ', $directorsInfo);

                // Build services/publications info
                $servicesInfo = [];
                foreach ($services as $service) {
                    $servicesInfo[] = ($service['name'] ?? '') . 
                                      ' (' . ($service['type'] ?? '') . ')' . 
                                      ' - ' . ($service['print_online'] ?? '') . 
                                      ' - ' . ($service['frequency'] ?? '') . 
                                      ' - ' . ($service['language'] ?? '') . 
                                      ' - ' . ($service['focus'] ?? '') . 
                                      ' - ' . ($service['scope'] ?? '');
                }
                $servicesStr = implode('; ', $servicesInfo);

                $row = [
                    $mediahouse->registration_no ?? '',
                    $formData['entity_name'] ?? $formData['company_name'] ?? '',
                    $directorsStr,
                    $formData['shareholding_structure'] ?? '',
                    $formData['office_address'] ?? '',
                    $contact?->phone ?? $formData['phone_number'] ?? '',
                    $contact?->phone ?? $formData['mobile_number'] ?? '',
                    $contact?->email ?? $formData['email_address'] ?? '',
                    $formData['website'] ?? '',
                    $formData['media_category'] ?? '',
                    $mediahouse->status ?? '',
                    optional($mediahouse->issued_at)->format('d M Y') ?? '',
                    optional($mediahouse->issued_at)->format('Y') ?? '',
                    $includeDetails ? $servicesStr : '',
                    $includeDetails ? ($services[0]['type'] ?? '') : '',
                    $includeDetails ? ($services[0]['print_online'] ?? '') : '',
                    $includeDetails ? ($services[0]['frequency'] ?? '') : '',
                    $includeDetails ? ($services[0]['language'] ?? '') : '',
                    $includeDetails ? ($services[0]['focus'] ?? '') : '',
                    $includeDetails ? ($services[0]['scope'] ?? '') : '',
                    $includeDetails ? ($services[0]['reach'] ?? '') : '',
                    $includeDetails ? ($services[0]['provincial_reach'] ?? '') : '',
                    $includeDetails ? ($contact?->name ?? $formData['contact_person_name'] ?? '') : '',
                    $includeDetails ? ($contact?->email ?? $formData['contact_person_email'] ?? '') : '',
                    $includeDetails ? ($contact?->phone ?? $formData['contact_person_phone'] ?? '') : '',
                    $formData['operational_status'] ?? 'Active',
                    $formData['previous_renewal_year'] ?? '',
                    $mediahouse->license_status ?? '',
                    optional($mediahouse->expires_at)->format('d M Y') ?? '',
                    optional($mediahouse->expires_at)->format('Y') ?? ''
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export registered media houses to PDF
     */
    private function exportMediaHousesToPDF($mediahouses, $includeDetails = false)
    {
        $pdf = \PDF::loadView('staff.officer.registered_mediahouses_pdf', compact('mediahouses', 'includeDetails'))
            ->setPaper('a4')
            ->setOrientation('landscape')
            ->setOption('margin-bottom', 15);

        $filename = 'registered_media_houses_' . now()->format('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Apply filters for accredited journalists
     */
    private function applyAccreditationFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('certificate_no', 'like', "%{$search}%")
                  ->orWhereHas('holder', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('issued_at', $request->input('year'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('collection_status')) {
            if ($request->input('collection_status') === 'collected') {
                $query->whereNotNull('collected_at');
            } else {
                $query->whereNull('collected_at');
            }
        }

        // Advanced filters
        if ($request->filled('organization')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->whereJsonContains('form_data->organization', $request->input('organization'))
                  ->orWhereJsonContains('form_data->employer', $request->input('organization'));
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->where('category', $request->input('category'));
            });
        }

        if ($request->filled('province')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->whereJsonContains('form_data->province', $request->input('province'));
            });
        }

        if ($request->filled('medium')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->whereJsonContains('form_data->medium', $request->input('medium'));
            });
        }

        if ($request->filled('sex')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereJsonContains('form_data->sex', $request->input('sex'))
                         ->orWhereJsonContains('form_data->gender', $request->input('sex'));
                });
            });
        }

        if ($request->filled('id_number')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereJsonContains('form_data->id_number', $request->input('id_number'))
                         ->orWhereJsonContains('form_data->national_id', $request->input('id_number'));
                });
            });
        }

        return $query;
    }

    /**
     * Apply filters for registered media houses
     */
    private function applyRegistrationFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                  ->orWhereHas('contact', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('year')) {
            $query->whereYear('issued_at', $request->input('year'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('collection_status')) {
            if ($request->input('collection_status') === 'collected') {
                $query->whereNotNull('collected_at');
            } else {
                $query->whereNull('collected_at');
            }
        }

        // Advanced filters
        if ($request->filled('ownership_type')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->whereJsonContains('form_data->ownership_type', $request->input('ownership_type'));
            });
        }

        if ($request->filled('media_type')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->whereJsonContains('form_data->media_type', $request->input('media_type'));
            });
        }

        if ($request->filled('province')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->whereJsonContains('form_data->province', $request->input('province'));
            });
        }

        if ($request->filled('business_type')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->whereJsonContains('form_data->business_type', $request->input('business_type'));
            });
        }

        if ($request->filled('reg_number')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereJsonContains('form_data->business_registration', $request->input('reg_number'))
                         ->orWhereJsonContains('form_data->company_registration', $request->input('reg_number'));
                });
            });
        }

        if ($request->filled('tax_number')) {
            $query->whereHas('application', function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->whereJsonContains('form_data->tax_number', $request->input('tax_number'))
                         ->orWhereJsonContains('form_data->bvr_number', $request->input('tax_number'));
                });
            });
        }

        return $query;
    }

    /**
     * Edit Request Management - Registrar Approval System
     */
    public function editRequests()
    {
        $requests = \App\Models\EditRequest::with(['requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.officer.edit_requests.index', compact('requests'));
    }

    /**
     * View specific edit request details
     */
    public function viewEditRequest($id)
    {
        $request = \App\Models\EditRequest::with(['requester', 'approver'])
            ->findOrFail($id);

        // Load related record
        if ($request->record_type === 'accreditation') {
            $request->load('accreditationRecord.application');
        } else {
            $request->load('registrationRecord.application');
        }

        return view('staff.officer.edit_requests.view', compact('request'));
    }

    /**
     * Approve edit request
     */
    public function approveEditRequest(Request $request, $id)
    {
        $editRequest = \App\Models\EditRequest::findOrFail($id);

        if ($editRequest->status !== \App\Models\EditRequest::STATUS_PENDING) {
            return back()->with('error', 'This request has already been processed.');
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Update request status
        $editRequest->update([
            'status' => \App\Models\EditRequest::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Apply changes to the record
        if ($editRequest->applyChanges()) {
            ActivityLogger::log('edit_request_approved', null, null, null, [
                'edit_request_id' => $editRequest->id,
                'record_type' => $editRequest->record_type,
                'record_id' => $editRequest->record_id,
                'approved_by' => auth()->id(),
            ]);

            return back()->with('success', 'Edit request approved and changes applied successfully.');
        } else {
            return back()->with('error', 'Failed to apply changes. Please contact system administrator.');
        }
    }

    /**
     * Reject edit request
     */
    public function rejectEditRequest(Request $request, $id)
    {
        $editRequest = \App\Models\EditRequest::findOrFail($id);

        if ($editRequest->status !== \App\Models\EditRequest::STATUS_PENDING) {
            return back()->with('error', 'This request has already been processed.');
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Update request status
        $editRequest->update([
            'status' => \App\Models\EditRequest::STATUS_REJECTED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
            'notes' => $data['notes'] ?? null,
        ]);

        ActivityLogger::log('edit_request_rejected', null, null, null, [
            'edit_request_id' => $editRequest->id,
            'record_type' => $editRequest->record_type,
            'record_id' => $editRequest->record_id,
            'rejected_by' => auth()->id(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return back()->with('success', 'Edit request rejected successfully.');
    }

    /**
     * Get pending edit requests count for dashboard
     */
    public function getPendingEditRequestsCount()
    {
        return \App\Models\EditRequest::pending()->count();
    }

    /**
     * Export accredited journalists to PDF
     */
    public function exportAccreditedJournalistsPDF(Request $request)
    {
        $query = AccreditationRecord::with(['application', 'application.applicant', 'holder']);

        // Apply filters
        $this->applyAccreditationFilters($query, $request);

        $journalists = $query->get();

        $filename = 'accredited_journalists_export_' . now()->format('Ymd_His') . '.pdf';
        
        $pdf = \PDF::loadView('exports.accredited_journalists_pdf', [
            'journalists' => $journalists,
            'filters' => $request->query(),
            'exportDate' => now(),
            'title' => 'Accredited Media Practitioners Export'
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export registered media houses to PDF
     */
    public function exportRegisteredMediaHousesPDF(Request $request)
    {
        $query = RegistrationRecord::with(['application', 'application.applicant', 'contact']);

        // Apply filters
        $this->applyRegistrationFilters($query, $request);

        $mediahouses = $query->get();

        $filename = 'registered_media_houses_export_' . now()->format('Ymd_His') . '.pdf';
        
        $pdf = \PDF::loadView('exports.registered_mediahouses_pdf', [
            'mediahouses' => $mediahouses,
            'filters' => $request->query(),
            'exportDate' => now(),
            'title' => 'Registered Media Houses Export'
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export single accredited journalist to PDF
     */
    public function exportSingleAccreditedJournalistPDF($id)
    {
        $journalist = AccreditationRecord::with(['application', 'application.applicant', 'application.documents', 'holder'])
            ->findOrFail($id);

        $formData = $journalist->application?->form_data ?? [];
        
        $filename = 'accreditation_record_' . $journalist->certificate_no . '_' . now()->format('Ymd_His') . '.pdf';
        
        $pdf = \PDF::loadView('exports.accredited_journalist_single_pdf', [
            'journalist' => $journalist,
            'formData' => $formData,
            'exportDate' => now()
        ]);

        return $pdf->download($filename);
    }

    /**
     * Export single registered media house to PDF
     */
    public function exportSingleRegisteredMediaHousePDF($id)
    {
        $mediahouse = RegistrationRecord::with(['application', 'application.applicant', 'application.documents', 'contact'])
            ->findOrFail($id);

        $formData = $mediahouse->application?->form_data ?? [];
        
        $filename = 'registration_record_' . $mediahouse->registration_no . '_' . now()->format('Ymd_His') . '.pdf';
        
        $pdf = \PDF::loadView('exports.registered_mediahouse_single_pdf', [
            'mediahouse' => $mediahouse,
            'formData' => $formData,
            'exportDate' => now()
        ]);

        return $pdf->download($filename);
    }

    public function rejectedApplications(Request $request)
    {
        $query = Application::with(['applicant', 'assignedOfficer'])
            ->whereIn('status', [
                Application::CORRECTION_REQUESTED,
                Application::OFFICER_REJECTED,
                Application::RETURNED_TO_OFFICER
            ]);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('applicant', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('application_type')) {
            $query->where('application_type', $request->application_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $applications = $query->orderBy('submitted_at', 'desc')->paginate(20);

        return view('staff.officer.applications.rejected', compact('applications'));
    }

    public function lookupApplication($number)
    {
        try {
            $application = $this->findExistingApplication($number, 'accreditation');
            
            if (!$application) {
                // Try to find by registration number
                $application = $this->findExistingApplication($number, 'registration');
            }
            
            if ($application) {
                return response()->json([
                    'success' => true,
                    'applicant_name' => $application->applicant->name,
                    'payment_status' => $application->payment?->status ?? 'pending',
                    'receipt_number' => $application->payment?->receipt_number ?? null,
                    'request_type' => $application->request_type,
                    'expiry_date' => $application->expires_at?->format('Y-m-d') ?? null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found with the provided number.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while looking up application.'
            ], 500);
        }
    }

    public function sendRenewalReminders(Request $request)
    {
        $data = $request->validate([
            'record_type' => ['required', 'in:accreditation,registration'],
            'record_ids' => ['nullable', 'array'],
            'record_ids.*' => ['integer'],
        ]);

        $count = 0;
        $type = $data['record_type'];
        $ids = $data['record_ids'] ?? [];

        if ($type === 'accreditation' && class_exists(\App\Models\AccreditationRecord::class)) {
            $query = \App\Models\AccreditationRecord::query()->with('holder');
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            } else {
                $cutoff = now()->addDays(90);
                $query->whereNotNull('expires_at')
                      ->where('expires_at', '>=', now())
                      ->where('expires_at', '<=', $cutoff);
            }
            $records = $query->get();
            foreach ($records as $record) {
                // TODO: Implement actual notification sending (email/SMS/portal notification)
                // For now, just log the action
                if ($record->holder) {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($record)
                        ->withProperties(['holder_email' => $record->holder->email])
                        ->log('renewal_reminder_sent');
                    $count++;
                }
            }
        } elseif ($type === 'registration' && class_exists(\App\Models\RegistrationRecord::class)) {
            $query = \App\Models\RegistrationRecord::query()->with('contact');
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            } else {
                $cutoff = now()->addDays(90);
                $query->whereNotNull('expires_at')
                      ->where('expires_at', '>=', now())
                      ->where('expires_at', '<=', $cutoff);
            }
            $records = $query->get();
            foreach ($records as $record) {
                // TODO: Implement actual notification sending
                if ($record->contact) {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($record)
                        ->withProperties(['contact_email' => $record->contact->email])
                        ->log('renewal_reminder_sent');
                    $count++;
                }
            }
        }

        return back()->with('success', "Renewal reminders sent to {$count} " . ($type === 'accreditation' ? 'media practitioners' : 'media houses') . ".");
    }

    public function idVerify(Request $request, Application $application)
    {
        $data = $request->validate([
            'result' => ['required', 'in:pending,passed,failed'],
            'notes'  => ['nullable', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        if ($this->hasColumn('applications', 'id_verification_status')) {
            $application->id_verification_status = $data['result'];
            $application->save();
        }

        $this->audit('officer_id_verify_stub', $application, $from, $from, [
            'result' => $data['result'],
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'ID verification (stub) updated.');
    }

    /* =========================
     * Records CSV Export
     * ========================= */

    public function exportAccreditedCsv(Request $request)
    {
        $query = \App\Models\AccreditationRecord::query()
            ->with(['holder', 'application.documents'])
            ->orderBy('issued_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_no', 'like', "%{$search}%")
                  ->orWhereHas('holder', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $filename = 'accredited_practitioners_'.now()->format('Ymd_His').'.csv';
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Media / Accreditation Number', 'Applicant’s Name', 'Organization', 'Category',
                'Valid From', 'Valid To', 'Month', 'Year', 'ID Number', 'Photofield URL',
                'Marital Status', 'Sex', 'Date of Birth', 'Birth Place', 'Nationality',
                'Home Address', 'Town', 'Province', 'Phone Number', 'Cell Number', 'Telephone',
                'Medium', 'Designation', 'Email Address'
            ]);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $rec) {
                    $app = $rec->application;
                    $fd = $app ? ($app->form_data ?? []) : [];
                    $name = $app?->applicant?->name ?? trim(($fd['first_name'] ?? '') . ' ' . ($fd['surname'] ?? ''));

                    $photoUrl = '—';
                    if ($app && $app->documents) {
                        $photo = $app->documents->where('doc_type', 'passport_photo')->first();
                        if ($photo) $photoUrl = url('storage/'.$photo->file_path);
                    }

                    fputcsv($out, [
                        $rec->certificate_no,
                        $name ?: '—',
                        $fd['employer_name'] ?? '—',
                        $app ? $app->categoryLabel() : '—',
                        $rec->issued_at ? $rec->issued_at->format('Y-m-d') : '—',
                        $rec->expires_at ? $rec->expires_at->format('Y-m-d') : '—',
                        $rec->issued_at ? $rec->issued_at->format('F') : '—',
                        $rec->issued_at ? $rec->issued_at->format('Y') : '—',
                        $fd['id_passport_number'] ?? '—',
                        $photoUrl,
                        $fd['marital_status'] ?? '—',
                        $fd['sex'] ?? '—',
                        $fd['dob'] ?? '—',
                        $fd['birth_place'] ?? '—',
                        $fd['nationality'] ?? '—',
                        $fd['zim_local_address'] ?? ($fd['zim_address'] ?? '—'),
                        $fd['town'] ?? '—',
                        $app?->collection_region ?? '—',
                        $app?->applicant?->phone ?? ($fd['phone'] ?? '—'),
                        $fd['cell_number'] ?? '—',
                        $fd['telephone'] ?? '—',
                        $fd['medium_type'] ?? '—',
                        $fd['designation'] ?? '—',
                        $app?->applicant?->email ?? ($fd['email'] ?? '—'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportRegisteredCsv(Request $request)
    {
        $query = \App\Models\RegistrationRecord::query()
            ->with(['contact', 'application'])
            ->orderBy('issued_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                  ->orWhere('entity_name', 'like', "%{$search}%");
            });
        }

        $filename = 'registered_mediahouses_'.now()->format('Ymd_His').'.csv';
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Registration Number', 'Organization/Media Company', 'Directors (Sex)', 'Shareholding Structure',
                'Office Address', 'Telephone(s)', 'Phone', 'Email', 'Website', 'Category',
                'Registration Status', 'Registration Date', 'Registration Year',
                'Publication/Service Type', 'Print/Online', 'Frequency', 'Language', 'Focus',
                'Scope', 'Reach', 'Provincial Reach', 'Editor/Manager/Head', 'Web Address',
                'Service Email', 'Service Telephone', 'Service Phone', 'Operational Status',
                'Previous Renewal Year', 'License Status', 'License Expiry Date', 'License Expiry Year'
            ]);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $rec) {
                    $app = $rec->application;
                    $fd = $app ? ($app->form_data ?? []) : [];

                    // Pluck directors array into string
                    $directorsList = [];
                    if (!empty($fd['directors']) && is_array($fd['directors'])) {
                        foreach ($fd['directors'] as $d) {
                            $sex = $d['sex'] ?? 'Unknown';
                            $directorsList[] = trim(($d['name'] ?? '') . ' ' . ($d['surname'] ?? '')) . " ($sex)";
                        }
                    }
                    $directorsStr = implode('; ', $directorsList);

                    // Pluck shareholding percent into string
                    $shareholdersList = [];
                    if (!empty($fd['directors']) && is_array($fd['directors'])) {
                        foreach ($fd['directors'] as $d) {
                            $pct = $d['shareholding_percent'] ?? '0';
                            if ((int)$pct > 0) {
                                $shareholdersList[] = trim(($d['name'] ?? '') . ' ' . ($d['surname'] ?? '')) . " ({$pct}%)";
                            }
                        }
                    }
                    if (!empty($fd['rep_office_shareholders']) && is_array($fd['rep_office_shareholders'])) {
                        foreach ($fd['rep_office_shareholders'] as $rs) {
                            $pct = $rs['percent'] ?? '0';
                            $shareholdersList[] = ($rs['name'] ?? '') . " ({$pct}%)";
                        }
                    }
                    $shareholdersStr = implode('; ', $shareholdersList);

                    $services = $fd['services'] ?? [[]]; // Default to one empty service array to ensure row prints

                    foreach ($services as $srv) {
                        fputcsv($out, [
                            $rec->registration_no,
                            $rec->entity_name ?? '—',
                            $directorsStr ?: '—',
                            $shareholdersStr ?: '—',
                            $fd['org_head_office'] ?? ($fd['head_office'] ?? ($fd['rep_office_address'] ?? '—')),
                            $fd['contact_phone'] ?? '—',
                            $app?->applicant?->phone ?? ($fd['contact_phone'] ?? '—'),
                            $app?->applicant?->email ?? ($fd['contact_email'] ?? '—'),
                            $fd['website'] ?? '—',
                            $app ? $app->categoryLabel() : '—',
                            $rec->status,
                            $rec->issued_at ? $rec->issued_at->format('Y-m-d') : '—',
                            $rec->issued_at ? $rec->issued_at->format('Y') : '—',
                            
                            // Service specific fields
                            $srv['service_type'] ?? ($srv['type'] ?? '—'),
                            $srv['print_online'] ?? '—',
                            $srv['frequency'] ?? '—',
                            $srv['language'] ?? '—',
                            $srv['focus'] ?? '—',
                            $srv['scope'] ?? '—',
                            $srv['reach'] ?? '—',
                            $srv['provincial_reach'] ?? '—',
                            $srv['editor'] ?? ($srv['manager'] ?? '—'),
                            $srv['website'] ?? '—',
                            $srv['email'] ?? '—',
                            $srv['telephone'] ?? '—',
                            $srv['phone'] ?? '—',
                            $srv['operational_status'] ?? '—',
                            $srv['previous_renewal_year'] ?? '—',
                            $srv['license_status'] ?? '—',
                            $srv['license_expiry_date'] ?? '—',
                            $srv['license_expiry_year'] ?? '—',
                        ]);
                    }
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /* =========================
     * Menu pages (v1)
     * ========================= */

    // --- Applications lists ---
    public function applicationsIndex()     { return $this->applicationsList('all'); }
    public function applicationsNew()       { return $this->applicationsList('new'); }
    public function applicationsPending()   { return $this->applicationsList('pending'); }
    public function applicationsApproved()  { return $this->applicationsList('approved'); }
    public function applicationsRejected()  { return $this->applicationsList('rejected'); }
    public function applicationsIncomplete(){ abort(404); } // drafts remain in applicant portal only
    public function applicationsBulk()      { return $this->placeholder('Bulk Review', 'Bulk review workflow can be enabled later (batch approve / request correction).'); }

    public function applicationsExportCsv(Request $request, string $list)
    {
        $q = $this->applicationsBaseQuery($list, $request);

        $filename = 'applications_'.$list.'_'.now()->format('Ymd_His').'.csv';
        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Ref','Applicant','Email','Type','Scope','Request','Status','Submitted','Category']);
            $q->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $app) {
                    fputcsv($out, [
                        $app->reference,
                        $app->applicant?->name,
                        $app->applicant?->email,
                        $app->application_type,
                        $app->journalist_scope,
                        $app->request_type,
                        $app->status,
                        optional($app->created_at)->format('Y-m-d H:i'),
                        $app->categoryLabel(),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // --- Records ---
    public function recordsJournalists() { return $this->recordsListPage('Accredited Media Practitioners', 'accreditation'); }
    public function recordsMediaHouses() { return $this->recordsListPage('Registered Media Houses', 'registration'); }
    public function recordsHistory()     { return $this->placeholder('Accreditation & Registration History', 'Shows historical issuance/renewal activity once records are populated.'); }
    public function recordsSuspended()   { abort(404); }
    public function recordsRenewals()    { return $this->placeholder('Renewals Management', 'Manage renewal requests (approve, reject, request info) once renewal records are enabled.'); }

    // --- Documents ---
    public function documentsUploaded() { return $this->documentsListPage('Uploaded Documents', null); }
    public function documentsPending()  { return $this->documentsListPage('Documents Pending Verification', 'pending'); }
    public function documentsVerified() { return $this->documentsListPage('Approved Documents', 'verified'); }
    public function documentsRejected() { return $this->documentsListPage('Returned Documents', 'rejected'); }

    // --- Renewals & expiry ---
    public function renewalsExpiring() { return $this->expiriesPage('Expiring Soon'); }
    public function renewalsExpired()  { return $this->expiriesPage('Expired', true); }
    public function renewalsRequests() { return $this->placeholder('Renewal Requests', 'Lists renewal submissions once renewal linking is enabled.'); }
    public function renewalsQueue()    { return $this->followUpQueuePage(); }

    // --- Compliance ---
    public function complianceMonitoring() { return $this->placeholder('Compliance Monitoring', 'Compliance monitoring dashboard (flags, risk scores, thresholds).'); }
    public function complianceViolations() { return $this->complianceListPage('Reported Violations'); }
    public function complianceCases()      { return $this->complianceCasesPage(); }
    public function complianceUnaccredited(){ return $this->unaccreditedReportsPage(); }

    // --- Reports ---
    public function reportsStats()      { return $this->placeholder('Accreditation Statistics', 'Charts and KPIs can be added (counts by status, region, type).'); }
    public function reportsMonthly()    { return $this->placeholder('Monthly / Annual Reports', 'Generate PDF/Excel/CSV exports (installations required).'); }
    public function reportsTrends()     { return $this->placeholder('Accreditation Trends', 'Trends over time (submissions, approvals, renewals).'); }
    public function reportsCompliance() { return $this->placeholder('Compliance Reports', 'Compliance summaries and investigation outcomes.'); }

    // --- Communication ---
    public function commNotices()       { return $this->placeholder('Accreditation Notices', 'Officer-facing notices board.'); }
    public function commAnnouncements() { return $this->placeholder('Public Announcements', 'Publish announcements to applicants (subject to permissions).'); }
    public function commMemos()         { return $this->placeholder('Internal Memos', 'Internal comms for staff.'); }
    public function commMessaging()     { return $this->placeholder('Bulk Messaging', 'Send email/SMS/portal notifications (queues + providers required).'); }

    // --- Advanced ---
    public function advancedDuplicates(){ return $this->placeholder('Duplicate Applicant Detection', 'Shows potential duplicates by National ID / Passport / Email / Phone / name similarity.'); }
    public function advancedForgery()   { return $this->placeholder('AI Document Forgery Checks', 'Integrate with AI service/API to produce document risk scores.'); }
    public function advancedQr()        { return $this->placeholder('Accreditation QR Verification', 'Verify QR codes to confirm accreditation status.'); }
    public function advancedAudit()     { return $this->auditTrailPage(); }

    // --- Tools ---
    public function toolsProfile() { return redirect()->route('settings'); }
    public function toolsTasks()   { return $this->placeholder('Task Queue', 'Officer tasks queue (follow-ups, verifications, escalations).'); }
    public function toolsDrafts()  { return $this->placeholder('Saved Drafts', 'Shows saved drafts and partially completed items.'); }
    public function toolsSops()    { return $this->placeholder('System Help / SOPs', 'Upload SOP PDFs and quick guides for staff.'); }

    /* =========================
     * Internal page builders
     * ========================= */

    private function placeholder(string $title, string $subtitle = '')
    {
        return view('staff.officer.placeholder', [
            'title' => $title,
            'subtitle' => $subtitle,
        ]);
    }

    private function applicationsList(string $list)
    {
        $titles = [
            'all' => 'All Applications',
            'new' => 'Recent Applications',
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Returned for Correction',
            'production' => 'Production Queue',
        ];

        $q = $this->applicationsBaseQuery($list, request());
        $applications = $q->paginate(20)->withQueryString();

        return view('staff.officer.applications_list_filtered', [
            'title' => $titles[$list] ?? 'Applications',
            'list' => $list,
            'applications' => $applications,
            'bucketLabels' => Application::bucketLabels(),
        ]);
    }

    private function applicationsBaseQuery(string $list, Request $request)
    {
        $user = Auth::user();
        $q = Application::query()->with('applicant')->latest();

        // Pool visibility: exclude applications being worked on by others
        $q->where(function($qq) use ($user) {
            $qq->whereNull('assigned_officer_id')
              ->orWhere('assigned_officer_id', $user->id);
        });

        // Pool visibility logic (respecting lock timeout)
        $q->where(function($qq) use ($user) {
            $qq->whereNull('locked_at')
              ->orWhere('locked_at', '<=', now()->subHours(2))
              ->orWhere('locked_by', $user->id);
        });

        // exclude drafts (drafts remain in applicant portal)
        $q->where(function ($qq) {
            $qq->whereNull('status')->orWhere('status', '!=', Application::DRAFT);
        });
        if ($this->hasColumn('applications','is_draft')) {
            $q->where(function ($qq) {
                $qq->whereNull('is_draft')->orWhere('is_draft', false);
            });
        }

        // list scopes
        $list = strtolower($list);
        if ($list === 'all') {
            // All applications sent by applicants — base query already filters out drafts
            // so we don't need a specific whereIn filter here.
        } elseif ($list === 'new') {
            // "Recent Application (since these will be the incoming once)"
            $q->whereIn('status', [
                Application::SUBMITTED,
                Application::SUBMITTED_WITH_APP_FEE,
            ]);
        } elseif ($list === 'pending') {
            // "Pending review will show all applications that have not been attended to by the Officer"
            // Including those the officer has started ("OFFICER_REVIEW") but not finalised, as well as incoming ones.
            $q->whereIn('status', [
                Application::SUBMITTED,
                Application::SUBMITTED_WITH_APP_FEE,
                Application::OFFICER_REVIEW,
            ]);
        } elseif ($list === 'approved') {
            // "Approved, will show all application that have been pushed for production"
            $q->whereIn('status', [
                Application::PRODUCTION_QUEUE,
                Application::PRODUCED_READY,
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ]);
        } elseif ($list === 'rejected') {
            // show only returned applications
            $q->where('status', Application::RETURNED_TO_OFFICER);
        } elseif ($list === 'production') {
            $q->where('status', Application::PAYMENT_VERIFIED);
        }

        // filters
        if ($bucket = $request->query('bucket')) {
            $q->applyBucket($bucket);
        } else {
            // Individual filters as fallback
            if ($rt = $request->query('request_type')) {
                if (in_array($rt, ['new','renewal','replacement'], true)) $q->where('request_type', $rt);
            }
            if ($sc = $request->query('scope')) {
                if (in_array($sc, ['local','foreign'], true)) {
                    if ($sc === 'local') {
                        $q->where(fn($w) => $w->where('journalist_scope', 'local')->orWhereNull('journalist_scope'));
                    } else {
                        $q->where('journalist_scope', 'foreign');
                    }
                }
            }
        }

        if ($t = $request->query('application_type')) {
            if (in_array($t, ['accreditation','registration'], true)) $q->where('application_type', $t);
        }
        // Status is not exposed as a general filter (list defaults apply).
        if ($from = $request->query('date_from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('date_to')) {
            $q->whereDate('created_at', '<=', $to);
        }
        if ($m = $request->query('month')) {
            $q->whereMonth('created_at', $m);
        }
        if ($y = $request->query('year')) {
            $q->whereYear('created_at', $y);
        }
        if ($search = trim((string)$request->query('q'))) {
            $q->where(function ($qq) use ($search) {
                $qq->where('reference', 'like', "%{$search}%")
                   ->orWhereHas('applicant', function ($u) use ($search) {
                       $u->where('name','like', "%{$search}%")
                         ->orWhere('email','like', "%{$search}%");
                   });
            });
        }

        return $q;
    }

    private function recordsListPage(string $title, string $type)
    {
        $rows = collect();
        if ($type === 'accreditation' && class_exists(\App\Models\AccreditationRecord::class) && Schema::hasTable('accreditation_records')) {
            $rows = \App\Models\AccreditationRecord::query()->with('holder')->latest('issued_at')->paginate(20);
        }
        if ($type === 'registration' && class_exists(\App\Models\RegistrationRecord::class) && Schema::hasTable('registration_records')) {
            $rows = \App\Models\RegistrationRecord::query()->with('contact')->latest('issued_at')->paginate(20);
        }

        return view('staff.officer.records_list', [
            'title' => $title,
            'type' => $type,
            'rows' => $rows,
        ]);
    }

    private function recordsSuspendedPage()
    {
        $acc = collect();
        $reg = collect();
        if (class_exists(\App\Models\AccreditationRecord::class) && Schema::hasTable('accreditation_records')) {
            $acc = \App\Models\AccreditationRecord::whereIn('status', ['suspended','revoked'])->latest('updated_at')->paginate(20, ['*'], 'acc');
        }
        if (class_exists(\App\Models\RegistrationRecord::class) && Schema::hasTable('registration_records')) {
            $reg = \App\Models\RegistrationRecord::whereIn('status', ['suspended','revoked'])->latest('updated_at')->paginate(20, ['*'], 'reg');
        }

        return view('staff.officer.records_suspended', [
            'acc' => $acc,
            'reg' => $reg,
        ]);
    }

    private function documentsListPage(string $title, ?string $verificationStatus)
    {
        $apps = collect();

        if (class_exists(\App\Models\ApplicationDocument::class) && Schema::hasTable('application_documents')) {
            $docStatus = null;
            if ($verificationStatus === 'pending') $docStatus = 'pending';
            if ($verificationStatus === 'verified') $docStatus = 'accepted';
            if ($verificationStatus === 'rejected') $docStatus = 'rejected';

            $appsQ = Application::query()
                ->with(['applicant','documents'])
                ->latest();

            // exclude drafts
            $appsQ->where(function ($qq) {
                $qq->whereNull('status')->orWhere('status','!=', Application::DRAFT);
            });

            if ($t = request()->query('application_type')) {
                if (in_array($t, ['accreditation','registration'], true)) $appsQ->where('application_type', $t);
            }

            if ($from = request()->query('date_from')) {
                $appsQ->whereDate('created_at','>=', $from);
            }
            if ($to = request()->query('date_to')) {
                $appsQ->whereDate('created_at','<=', $to);
            }

            if ($search = trim((string)request()->query('q'))) {
                $appsQ->where(function ($qq) use ($search) {
                    $qq->where('reference', 'like', "%{$search}%")
                       ->orWhereHas('applicant', function ($u) use ($search) {
                           $u->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                       });
                });
            }

            if ($recSearch = trim((string)request()->query('record_number'))) {
                $appsQ->where(function ($qq) use ($recSearch) {
                    $qq->whereHas('accreditationRecord', function ($ar) use ($recSearch) {
                        $ar->where('certificate_no', 'like', "%{$recSearch}%");
                    })->orWhereHas('registrationRecord', function ($rr) use ($recSearch) {
                        $rr->where('registration_no', 'like', "%{$recSearch}%");
                    });
                });
            }

            $appsQ->whereHas('documents', function ($dq) use ($docStatus) {
                if ($docStatus) {
                    $dq->where('status', $docStatus);
                }
            });

            if ($docStatus) {
                $appsQ->with(['documents' => function ($dq) use ($docStatus) {
                    $dq->where('status', $docStatus)->latest();
                }]);
            }

            $apps = $appsQ->paginate(15)->withQueryString();
        }

        return view('staff.officer.documents_grouped', [
            'title' => $title,
            'applications' => $apps,
        ]);
    }

    private function expiriesPage(string $title, bool $expiredOnly = false)
    {
        $months = (int) request()->query('months', 3);
        $cutoff = now()->addMonths(max(1, $months));

        $acc = collect();
        $reg = collect();

        if (class_exists(\App\Models\AccreditationRecord::class) && Schema::hasTable('accreditation_records')) {
            $q = \App\Models\AccreditationRecord::query()->with('holder');
            $q->whereNotNull('expires_at');
            if ($expiredOnly) {
                $q->where('expires_at', '<', now());
            } else {
                $q->where('expires_at', '<=', $cutoff)->where('expires_at', '>=', now());
            }
            $acc = $q->orderBy('expires_at')->paginate(20, ['*'], 'acc');
        }

        if (class_exists(\App\Models\RegistrationRecord::class) && Schema::hasTable('registration_records')) {
            $q = \App\Models\RegistrationRecord::query()->with('contact');
            $q->whereNotNull('expires_at');
            if ($expiredOnly) {
                $q->where('expires_at', '<', now());
            } else {
                $q->where('expires_at', '<=', $cutoff)->where('expires_at', '>=', now());
            }
            $reg = $q->orderBy('expires_at')->paginate(20, ['*'], 'reg');
        }

        return view('staff.officer.expiries', [
            'title' => $title,
            'months' => $months,
            'acc' => $acc,
            'reg' => $reg,
        ]);
    }

    private function followUpQueuePage()
    {
        $rows = collect();
        if (class_exists(\App\Models\OfficerFollowUp::class) && Schema::hasTable('officer_followups')) {
            $rows = \App\Models\OfficerFollowUp::query()->latest()->paginate(20);
        }
        return view('staff.officer.followups', [
            'title' => 'Officer Follow-up Queue',
            'rows' => $rows,
        ]);
    }

    private function complianceListPage(string $title)
    {
        $rows = collect();
        if (class_exists(\App\Models\Violation::class) && Schema::hasTable('violations')) {
            $rows = \App\Models\Violation::query()->latest()->paginate(20);
        }
        return view('staff.officer.compliance_list', [
            'title' => $title,
            'rows' => $rows,
        ]);
    }

    private function complianceCasesPage()
    {
        $rows = collect();
        if (class_exists(\App\Models\ComplianceCase::class) && Schema::hasTable('compliance_cases')) {
            $rows = \App\Models\ComplianceCase::query()->latest()->paginate(20);
        }
        return view('staff.officer.compliance_cases', [
            'title' => 'Investigation Cases',
            'rows' => $rows,
        ]);
    }

    private function unaccreditedReportsPage()
    {
        $rows = collect();
        if (class_exists(\App\Models\UnaccreditedReport::class) && Schema::hasTable('unaccredited_reports')) {
            $rows = \App\Models\UnaccreditedReport::query()->latest()->paginate(20);
        }
        return view('staff.officer.unaccredited_reports', [
            'title' => 'Unaccredited Practice Reports',
            'rows' => $rows,
        ]);
    }

    private function auditTrailPage()
    {
        $applicationId = request()->query('application_id');
        $logs = collect();
        if (class_exists(\App\Models\ActivityLog::class) && Schema::hasTable('activity_logs')) {
            $q = \App\Models\ActivityLog::query()->latest();
            if ($applicationId) {
                $q->where('entity_type', Application::class)->where('entity_id', $applicationId);
            }
            $logs = $q->paginate(25);
        }

        return view('staff.officer.audit_trail', [
            'title' => 'Audit Trail (Per Application)',
            'logs' => $logs,
            'applicationId' => $applicationId,
        ]);
    }

    /* =========================
    /* ========================= Helpers ========================= */

    private function audit(string $action, Application $application, ?string $from, ?string $to, array $meta = []): void
    {
        $payload = array_merge([
            'actor_role' => session('active_staff_role'),
            'actor_user_id' => Auth::id(),
            'from_status' => $from,
            'to_status' => $to,
        ], $meta);

        ActivityLogger::log($action, $application, $from, $to, $payload);
        \App\Support\AuditTrail::log($action, $application, $payload);
    }

    private function safeSet(Application $application, array $fields): void
    {
        foreach ($fields as $k => $v) {
            if ($this->hasColumn('applications', $k)) {
                $application->{$k} = $v;
            }
        }
        $application->save();
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function persistMessageIfAvailable(Application $application, string $message): void
    {
        if (class_exists(\App\Models\ApplicationMessage::class) && Schema::hasTable('application_messages')) {
            \App\Models\ApplicationMessage::create([
                'application_id' => $application->id,
                'from_user_id'   => Auth::id(),
                'to_user_id'     => $application->applicant_user_id,
                'channel'        => 'internal',
                'subject'        => null,
                'body'           => $message,
                'sent_at'        => now(),
                'read_at'        => null,
            ]);
        }
    }
}
