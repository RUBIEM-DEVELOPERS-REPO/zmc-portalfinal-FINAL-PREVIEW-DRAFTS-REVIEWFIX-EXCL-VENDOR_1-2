<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ActivityLog;
use App\Models\PrintLog;
use App\Models\DocumentVersion;
use App\Models\Notice;
use App\Models\Event;
use App\Models\News;
use App\Services\ApplicationWorkflow;
use App\Services\ApplicationWorkflowService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class RegistrarController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();

        // KPIs
        $kpis = [
            'awaiting_registrar' => Application::whereIn('status', [
                    Application::REGISTRAR_REVIEW,
                    Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
                    Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR
                ])
                ->where(function($q) use ($user) {
                    $q->whereNull('assigned_officer_id')
                      ->orWhere('assigned_officer_id', $user->id);
                })->count(),
            'approved_today' => Application::whereIn('status', [Application::ACCOUNTS_REVIEW, Application::REGISTRAR_APPROVED, Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT])->where('last_action_at', '>=', $today)->count(),
            'approved_this_week' => Application::whereIn('status', [Application::ACCOUNTS_REVIEW, Application::REGISTRAR_APPROVED, Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT])->where('last_action_at', '>=', $thisWeek)->count(),
            'returned_to_officer' => Application::whereIn('status', [Application::RETURNED_TO_OFFICER, Application::REGISTRAR_RAISED_FIX_REQUEST])->count(),

            // Category mismatches: items where registrar changed the category
            'category_mismatches' => ActivityLog::where('action', 'registrar_reassign_category')->where('created_at', '>=', $thisWeek)->count(),

            'certificates_generated_today' => DocumentVersion::where('document_type', 'certificate')->where('created_at', '>=', $today)->count(),
            'prints_today' => PrintLog::where('created_at', '>=', $today)->count(),

            // Reprints flagged: prints > 1
            'flagged_reprints' => Application::where('print_count', '>', 1)->count(),

            // New: Applications awaiting Registrar to approve for payment
            'awaiting_payment_approval' => Application::whereIn('status', [
                    Application::REGISTRAR_REVIEW,
                    Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
                    Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR
                ])
                ->whereNull('registrar_reviewed_at')
                ->where('payment_status', '!=', 'paid')
                ->where(function($q) use ($user) {
                    $q->whereNull('assigned_officer_id')
                      ->orWhere('assigned_officer_id', $user->id);
                })
                ->count(),
        ];

        // Global Filters logic for the main dashboard list
        $query = Application::query()
            ->with(['applicant', 'lastActionBy'])
            ->withCount('printLogs');
            
        // Apply year filter if it's not the current year
        if (!$isCurrentYear) {
            $query->whereYear('created_at', $year);
        }

        // Concurrency visibility logic
        $query->where(function($q) use ($user) {
            $q->whereNull('assigned_officer_id')
              ->orWhere('assigned_officer_id', $user->id);
        });

        $query->where(function($q) use ($user) {
            $q->whereNull('locked_at')
              ->orWhere('locked_at', '<=', now()->subHours(2))
              ->orWhere('locked_by', $user->id);
        });

        if ($request->filled('type')) {
            $query->where('application_type', $request->type);
        }
        if ($request->filled('classification')) {
            $query->where('journalist_scope', $request->classification);
        }
        if ($request->filled('residency')) {
            $query->where('residency_type', $request->residency);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('reference', 'like', "%$s%")
                  ->orWhereHas('applicant', function($aq) use ($s) {
                      $aq->where('name', 'like', "%$s%");
                  });
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // By default show items needing attention or recently approved
        if (!$request->filled('status')) {
            $query->whereIn('status', [
                Application::PAID_CONFIRMED,
                Application::REGISTRAR_REVIEW,
                Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
                Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR,
                Application::REGISTRAR_APPROVED,
                Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT,
                Application::RETURNED_TO_OFFICER,
                Application::REGISTRAR_RAISED_FIX_REQUEST,
                Application::ACCOUNTS_REVIEW,
            ]);
        }

        $applications = $query->latest()->paginate(20)->withQueryString();

        // Activity feed - recent registrar-related actions
        $activity = ActivityLog::query()
            ->whereIn('action', [
                'registrar_approve',
                'registrar_reject',
                'registrar_return_to_accounts',
                'registrar_reassign_category',
                'registrar_approve_for_payment',
                'accounts_confirm_paid',
                'officer_approve',
                'application_submitted',
            ])
            ->with(['user', 'entity'])
            ->latest()
            ->limit(15)
            ->get();

        return view('staff.registrar.dashboard', compact('applications', 'kpis', 'activity'));
    }

    /**
     * 2) Incoming Queue: “Confirmed by Accreditation Officer”
     * Driven by workflow: confirmed items that cleared accounts.
     */
    public function incomingQueue(Request $request)
    {
        $query = Application::query()
            ->with(['applicant', 'assignedOfficer', 'payments'])
            ->whereIn('status', [
                Application::REGISTRAR_REVIEW,
                Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
                Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR
            ]);

        // Apply filters
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('reference', 'like', "%$s%")
                  ->orWhereHas('applicant', function($aq) use ($s) {
                      $aq->where('name', 'like', "%$s%");
                  });
            });
        }

        $applications = $query->latest('last_action_at')->paginate(20);

        return view('staff.registrar.incoming_queue', compact('applications'));
    }

    /**
     * Registrar approves for payment.
     */
    public function approveForPayment(Request $request, Application $application)
    {
        $data = $request->validate([
            'decision_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        // Transitions to accounts_review so they can handle payment
        ApplicationWorkflow::transition($application, Application::ACCOUNTS_REVIEW, 'registrar_approve_for_payment', [
            'notes' => $data['decision_notes'] ?? null,
        ]);

        $this->safeSet($application, [
            'registrar_reviewed_at' => now(),
            'registrar_reviewed_by' => Auth::id(),
            'decision_notes' => $data['decision_notes'] ?? $application->decision_notes,
        ]);

        return back()->with('success', 'Application approved for payment and forwarded to Accounts.');
    }

    public function applicationsList(Request $request, string $type, string $bucket)
    {
        abort_unless(in_array($type, ['accreditation', 'registration'], true), 404);

        $statusMap = [
            'new' => [Application::PAID_CONFIRMED],
            'under-review' => [Application::REGISTRAR_REVIEW],
            'approved' => [Application::REGISTRAR_APPROVED],
            'rejected' => [Application::REGISTRAR_REJECTED],
            'corrections' => [Application::RETURNED_TO_OFFICER, Application::RETURNED_TO_ACCOUNTS],
        ];

        $statuses = $statusMap[$bucket] ?? [Application::PAID_CONFIRMED, Application::REGISTRAR_REVIEW];

        $applications = Application::query()
            ->with('applicant')
            ->withCount('documents')
            ->where('application_type', $type)
            ->whereIn('status', $statuses)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $title = ucfirst($type) . ' • ' . ucwords(str_replace('-', ' ', $bucket));

        return view('staff.registrar.applications_list', compact('applications', 'title', 'type', 'bucket'));
    }

    public function renewalsList(Request $request, string $bucket)
    {
        $base = Application::query()
            ->with('applicant')
            ->withCount('documents')
            ->where('request_type', 'renewal');

        if ($bucket === 'due-soon') {
            $base->whereIn('status', [
                Application::PAID_CONFIRMED,
                Application::REGISTRAR_REVIEW,
                Application::REGISTRAR_APPROVED,
                Application::ISSUED,
            ]);
        } elseif ($bucket === 'submitted') {
            $base->whereIn('status', [
                Application::PAID_CONFIRMED,
                Application::REGISTRAR_REVIEW,
            ]);
        } elseif ($bucket === 'renewed-expired') {
            $base->whereIn('status', [
                Application::REGISTRAR_APPROVED,
                Application::ISSUED,
            ]);
        }

        $applications = $base->latest()->paginate(20)->withQueryString();

        $title = 'Renewals (AP5) • ' . ucwords(str_replace('-', ' ', $bucket));

        return view('staff.registrar.applications_list', compact('applications', 'title'));
    }

    public function placeholder(string $title, string $hint = '')
    {
        return view('staff.registrar.placeholder', compact('title', 'hint'));
    }

    public function show(Application $application)
    {
        // Try to claim the application (concurrency lock)
        if (!$application->claim(auth()->user())) {
            $lockerName = $application->lockedBy ? $application->lockedBy->name : 'another official';
            return redirect()->back()->with('error', "This application is currently being worked on by {$lockerName}.");
        }

        $application->load(['applicant', 'documents', 'messages', 'workflowLogs', 'payments', 'printLogs', 'documentVersions', 'lockedBy']);

        // Audit trail from ActivityLog
        $auditTrail = ActivityLog::where('entity_type', get_class($application))
            ->where('entity_id', $application->id)
            ->with('user')
            ->latest()
            ->get();

        return view('staff.registrar.show', compact('application', 'auditTrail'));
    }

    /**
     * Registrar reassigns category.
     */
    public function reassignCategory(Request $request, Application $application)
    {
        $data = $request->validate([
            'category_code' => 'required|string',
            'reason' => 'required|string|max:1000',
        ]);

        $oldCategory = $application->accreditation_category_code ?? $application->media_house_category_code;

        if ($application->application_type === 'registration') {
            $application->media_house_category_code = $data['category_code'];
        } else {
            $application->accreditation_category_code = $data['category_code'];
        }

        $application->save();

        ActivityLogger::log('registrar_reassign_category', $application, $application->status, $application->status, [
            'old_category' => $oldCategory,
            'new_category' => $data['category_code'],
            'reason' => $data['reason'],
        ]);

        return back()->with('success', 'Category reassigned successfully.');
    }

    /**
     * Registrar approves -> moves to Accounts for payment
     */
    public function approve(Request $request, Application $application)
    {
        $data = $request->validate([
            'decision_notes' => ['nullable', 'string', 'max:5000'],
            'category_code'  => ['required', 'string', 'max:10'],
        ]);

        try {
            // Apply category first as it might be needed for fee calculation
            if ($application->application_type === 'registration') {
                $application->media_house_category_code = $data['category_code'];
            } else {
                $application->accreditation_category_code = $data['category_code'];
            }
            $application->save();

            $application = ApplicationWorkflowService::registrarApprove($application, [
                'notes' => $data['decision_notes'] ?? null,
                'category_code' => $data['category_code'],
            ]);

            return back()->with('success', 'Application approved by Registrar.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Workflow error: ' . $e->getMessage());
        }
    }


    public function reject(Request $request, Application $application)
    {
        $data = $request->validate([
            'decision_notes' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        $this->safeSet($application, [
            'rejection_reason' => $data['decision_notes'],
            'decision_notes' => $data['decision_notes'],
        ]);

        ApplicationWorkflow::transition($application, Application::REGISTRAR_REJECTED, 'registrar_reject', [
            'reason' => $data['decision_notes'],
        ]);

        $application->refresh();

        $this->audit('registrar_reject', $application, $from, $application->status, [
            'reason' => $data['decision_notes'],
        ]);

        return back()->with('success', 'Rejected.');
    }

    public function returnToAccounts(Request $request, Application $application)
    {
        $data = $request->validate([
            'decision_notes' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        ApplicationWorkflow::transition($application, Application::RETURNED_TO_ACCOUNTS, 'registrar_return_to_accounts', [
            'notes' => $data['decision_notes'],
        ]);

        $this->safeSet($application, ['decision_notes' => $data['decision_notes']]);

        $application->refresh();

        $this->audit('registrar_return_to_accounts', $application, $from, $application->status, [
            'notes' => $data['decision_notes'],
        ]);

        return back()->with('success', 'Returned to Accounts/Payments.');
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

    /* helpers */
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

    public function reports(Request $request)
    {
        $query = Application::query();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $approvals = (clone $query)->where('status', Application::REGISTRAR_APPROVED)->count();
        $reassignments = ActivityLog::where('action', 'registrar_reassign_category')
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->count();

        $totalPrints = PrintLog::when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->count();

        $edits = DocumentVersion::when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->count();

        return view('staff.registrar.reports', compact('approvals', 'reassignments', 'totalPrints', 'edits'));
    }

    public function auditTrailSearch(Request $request)
    {
        $query = ActivityLog::query()->with(['user', 'entity']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('action', 'like', "%$s%")
                  ->orWhere('meta', 'like', "%$s%")
                  ->orWhereHas('user', function($uq) use ($s) {
                      $uq->where('name', 'like', "%$s%");
                  });
            });
        }

        $logs = $query->latest()->paginate(50);
        return view('staff.registrar.audit_trail', compact('logs'));
    }

    private function hasColumn(string $table, string $column): bool
    {
        try { return Schema::hasColumn($table, $column); }
        catch (\Throwable $e) { return false; }
    }

    /**
     * View Notices & Events (Read-only)
     */
    public function noticesEvents(Request $request)
    {
        $notices = Notice::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(20);

        $events = Event::query()
            ->where('is_published', true)
            ->orderBy('starts_at')
            ->paginate(20);

        return view('staff.registrar.notices_events', compact('notices', 'events'));
    }

    /**
     * View News / Press Statements (Read-only)
     */
    public function news(Request $request)
    {
        $news = News::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(20);

        return view('staff.registrar.news', compact('news'));
    }

    /**
     * Send fix request to Accreditation Officer
     */
    public function sendFixRequest(Request $request, Application $application)
    {
        $data = $request->validate([
            'request_type' => ['required', 'in:data_correction,category_change,document_issue'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        // Create fix request
        $fixRequest = \App\Models\FixRequest::create([
            'application_id' => $application->id,
            'requested_by' => Auth::id(),
            'request_type' => $data['request_type'],
            'description' => $data['description'],
            'status' => 'pending',
        ]);

        // Use new workflow service - enforces strict transitions
        try {
            $application = ApplicationWorkflowService::registrarRaiseFixRequest($application, $data['description'], [
                'fix_request_id' => $fixRequest->id,
                'request_type' => $data['request_type'],
            ]);

            return back()->with('success', 'Fix request sent to Accreditation Officer.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Workflow error: ' . $e->getMessage());
        }
    }

    /**
     * View fix requests sent by this registrar
     */
    public function fixRequests(Request $request)
    {
        $query = \App\Models\FixRequest::query()
            ->with(['application.applicant', 'resolver'])
            ->where('requested_by', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $fixRequests = $query->latest()->paginate(20)->withQueryString();

        return view('staff.registrar.fix_requests', compact('fixRequests'));
    }

    /**
     * Approve special case (forwarded without approval)
     */
    public function approveSpecialCase(Request $request, Application $application)
    {
        $data = $request->validate([
            'decision_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        // Validate status
        if ($application->status !== Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL) {
            return back()->with('error', 'This application is not a special case awaiting approval.');
        }

        // Save decision notes
        if (!empty($data['decision_notes'])) {
            $application->decision_notes = $data['decision_notes'];
            $application->save();
        }

        // Use new workflow service - enforces strict transitions
        try {
            $application = ApplicationWorkflowService::registrarPushToAccounts($application, [
                'notes' => $data['decision_notes'] ?? null,
                'forward_reason' => $application->forward_no_approval_reason,
            ]);

            $this->safeSet($application, [
                'registrar_reviewed_at' => now(),
                'registrar_reviewed_by' => Auth::id(),
            ]);

            return back()->with('success', 'Special case approved and sent to Accounts for payment verification.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Workflow error: ' . $e->getMessage());
        }
    }

    /**
     * Approve media house application with official letter upload
     * (Two-stage payment: after this, applicant pays registration fee)
     */
    public function approveWithOfficialLetter(Request $request, Application $application)
    {
        $data = $request->validate([
            'official_letter' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'decision_notes' => ['nullable', 'string', 'max:5000'],
            'category_code' => ['required', 'string', 'max:10'],
        ]);

        // Validate: Media house only
        if ($application->application_type !== 'registration') {
            return back()->with('error', 'Official letter is only required for media house registrations.');
        }

        try {
            \DB::transaction(function() use ($application, $data) {
                // Apply category first
                $application->media_house_category_code = $data['category_code'];

                // Upload official letter
                $file = $data['official_letter'];
                $path = $file->store('official_letters', 'public');
                $hash = hash_file('sha256', \Storage::disk('public')->path($path));

                $officialLetter = \App\Models\OfficialLetter::create([
                    'application_id' => $application->id,
                    'uploaded_by' => \Auth::id(),
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_hash' => $hash,
                    'uploaded_at' => now(),
                ]);

                // Link to application
                $application->official_letter_id = $officialLetter->id;
                $application->save();

                // Use workflow service
                ApplicationWorkflowService::registrarApprove($application, [
                    'notes' => $data['decision_notes'] ?? null,
                    'category_code' => $data['category_code'],
                ]);
            });

            return back()->with('success', 'Application approved. Official letter uploaded. Applicant will be prompted to pay registration fee.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Workflow error: ' . $e->getMessage());
        }
    }

    /**
     * Payment Oversight Dashboard (Read-Only)
     */
    public function paymentOversight(Request $request)
    {
        // Query payment submissions with filters
        $query = \App\Models\PaymentSubmission::query()
            ->with(['application.applicant', 'verifier'])
            ->latest('submitted_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by method
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // Filter by payment stage
        if ($request->filled('payment_stage')) {
            $query->where('payment_stage', $request->payment_stage);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Search by application reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('application', function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        // Calculate KPIs
        $kpis = [
            'pending' => \App\Models\PaymentSubmission::where('status', 'submitted')->count(),
            'verified' => \App\Models\PaymentSubmission::where('status', 'verified')->count(),
            'rejected' => \App\Models\PaymentSubmission::where('status', 'rejected')->count(),
            'paynow' => \App\Models\PaymentSubmission::where('method', 'PAYNOW')->count(),
            'proof' => \App\Models\PaymentSubmission::where('method', 'PROOF_UPLOAD')->count(),
            'waiver' => \App\Models\PaymentSubmission::where('method', 'WAIVER')->count(),
            'app_fee' => \App\Models\PaymentSubmission::where('payment_stage', 'application_fee')->count(),
            'reg_fee' => \App\Models\PaymentSubmission::where('payment_stage', 'registration_fee')->count(),
        ];

        // Log oversight access - using Auth user as entity since this is a view action
        ActivityLogger::log('registrar_view_payment_oversight', Auth::user(), null, null, [
            'actor_role' => session('active_staff_role'),
            'filters' => $request->only(['status', 'method', 'payment_stage', 'date_from', 'date_to', 'search']),
        ]);

        return view('staff.registrar.payment_oversight', compact('payments', 'kpis'));
    }

    /**
     * Payment Detail View (Read-Only)
     */
    public function paymentDetail(\App\Models\PaymentSubmission $paymentSubmission)
    {
        $paymentSubmission->load(['application.applicant', 'application.workflowLogs', 'verifier']);

        // Get all payment submissions for this application (for context)
        $allPayments = \App\Models\PaymentSubmission::where('application_id', $paymentSubmission->application_id)
            ->with('verifier')
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Log detail view access
        ActivityLogger::log('registrar_view_payment_detail', $paymentSubmission->application, null, null, [
            'actor_role' => session('active_staff_role'),
            'payment_submission_id' => $paymentSubmission->id,
            'payment_stage' => $paymentSubmission->payment_stage,
        ]);

        return view('staff.registrar.payment_detail', compact('paymentSubmission', 'allPayments'));
    }

}

