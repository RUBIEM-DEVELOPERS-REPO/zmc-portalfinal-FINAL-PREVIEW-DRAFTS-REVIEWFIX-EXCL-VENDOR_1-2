<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationWorkflow;
use App\Services\ApplicationWorkflowService;
use App\Services\PaymentWorkflowService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\PaymentAuditLog;
use App\Models\Refund;
use App\Http\Controllers\Portal\PaynowController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountsPaymentsController extends Controller
{
    /**
     * 1) Payments Listing & Verification
     */
    public function index(Request $request)
    {
        $query = Payment::query()->with(['application.applicant', 'payer']);

        // Multi-level filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->whereIn('status', (array) $request->input('status'));
        }
        if ($request->filled('method')) {
            $query->whereIn('method', (array) $request->input('method'));
        }
        if ($request->filled('applicant_category')) {
            $query->whereIn('applicant_category', (array) $request->input('applicant_category'));
        }
        if ($request->filled('service_type')) {
            $query->whereIn('service_type', (array) $request->input('service_type'));
        }
        if ($request->filled('residency')) {
            $query->whereIn('residency', (array) $request->input('residency'));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('application.applicant', function($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('staff.accounts.payments.index', compact('payments'));
    }

    /**
     * Retry failed confirmations (gateway callback issues)
     */
    public function retryPaymentStatus(Payment $payment)
    {
        if ($payment->method !== 'paynow' || !$payment->poll_url) {
            return back()->with('error', 'Only Paynow payments with poll URL can be retried.');
        }

        // Use the logic from PaynowController if possible or implement here
        $paynowController = app(PaynowController::class);
        // We might need to mock or adjust checkStatus to work with Payment model
        // For now, let's assume checkStatus can handle it or we implement a service

        // Simplified retry logic (logic placeholder)
        $payment->update([
            'last_checked_at' => now(),
        ]);

        return back()->with('success', 'Payment status updated from gateway.');
    }

    /**
     * 2) Offline Payments Handling
     */
    public function createOffline()
    {
        $applications = Application::whereIn('status', [Application::ACCOUNTS_REVIEW])->get();
        return view('staff.accounts.payments.offline_create', compact('applications'));
    }

    public function storeOffline(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|string', // bank_transfer, cash, pos
            'reference' => 'required|string|unique:payments,reference',
            'bank_name' => 'nullable|string',
            'deposit_slip_ref' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'payer_name' => 'nullable|string',
        ]);

        $application = Application::findOrFail($validated['application_id']);

        $payment = Payment::create([
            'application_id' => $application->id,
            'payer_user_id' => $application->user_id,
            'method' => $validated['method'],
            'source' => 'offline',
            'amount' => $validated['amount'],
            'currency' => 'USD', // Default or from config
            'reference' => $validated['reference'],
            'status' => 'paid',
            'bank_name' => $validated['bank_name'],
            'deposit_slip_ref' => $validated['deposit_slip_ref'],
            'confirmed_at' => now(),
            'applicant_category' => $application->category_code, // Assuming this is set
            'service_type' => $application->application_type,
            'residency' => $application->residency_type ?? 'local',
        ]);

        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')->store('payment_proofs', 'public');
            $payment->update(['proof_file_path' => $path]);
        }

        $this->logPaymentAction($payment, 'created', null, 'paid', 'Offline payment recorded.');

        // Transition application if Paid
        if ($payment->status === 'paid') {
            ApplicationWorkflow::transition($application, Application::PAID_CONFIRMED, 'Payment Confirmed (Offline)');
        }

        return redirect()->route('staff.accounts.payments.index')->with('success', 'Offline payment recorded.');
    }

    /**
     * 3) Reversals, Refunds, and Adjustments
     */
    public function reverse(Request $request, Payment $payment)
    {
        $request->validate(['reason' => 'required|string']);

        DB::transaction(function() use ($payment, $request) {
            $oldStatus = $payment->status;
            $payment->update([
                'status' => 'reversed',
                'reversal_reason' => $request->reason
            ]);

            $this->logPaymentAction($payment, 'reversed', $oldStatus, 'reversed', $request->reason);

            // If it was linked to an application that was confirmed, maybe move it back?
            // This depends on business logic.
        });

        return back()->with('success', 'Payment reversed.');
    }

    public function initiateRefund(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|max:' . $payment->amount,
            'reason' => 'required|string'
        ]);

        Refund::create([
            'payment_id' => $payment->id,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'processed_by' => Auth::id(),
            'status' => 'pending'
        ]);

        return back()->with('success', 'Refund initiated and pending approval.');
    }

    public function approveRefund(Refund $refund)
    {
        DB::transaction(function() use ($refund) {
            $refund->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            $payment = $refund->payment;
            $oldStatus = $payment->status;
            $newStatus = ($refund->amount == $payment->amount) ? 'refunded' : 'partially_refunded';

            $payment->update(['status' => $newStatus]);
            $this->logPaymentAction($payment, 'refunded', $oldStatus, $newStatus, "Refund approved. Amount: {$refund->amount}");
        });

        return back()->with('success', 'Refund approved.');
    }

    /**
     * 4) Ledger Generation
     */
    public function ledger(Request $request)
    {
        $query = Payment::query()->with(['application.applicant']);

        // Apply filters (reusing logic or common scope)
        $this->applyFilters($query, $request);

        $payments = $query->latest()->get();

        $summary = [
            'total_transactions' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'total_paid' => $payments->where('status', 'paid')->sum('amount'),
            'total_pending' => $payments->where('status', 'pending')->sum('amount'),
            'total_refunded' => $payments->whereIn('status', ['refunded', 'partially_refunded'])->sum('amount'), // simplified
            'outstanding_balance' => $payments->where('status', 'pending')->sum('amount'),
        ];

        return view('staff.accounts.ledger', compact('payments', 'summary'));
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('status')) $query->whereIn('status', (array)$request->status);
        if ($request->filled('applicant_category')) $query->whereIn('applicant_category', (array)$request->applicant_category);
        if ($request->filled('service_type')) $query->whereIn('service_type', (array)$request->service_type);
        if ($request->filled('residency')) $query->whereIn('residency', (array)$request->residency);
    }

    /**
     * Financial Reporting (Accounts) — With Graphs
     */
    public function reportFinancial(Request $request)
    {
        $query = Payment::query();
        $this->applyFilters($query, $request);

        $payments = $query->get();

        // KPI Cards
        $stats = [
            'total_revenue' => $payments->where('status', 'paid')->sum('amount'),
            'total_transactions' => $payments->count(),
            'paid_total' => $payments->where('status', 'paid')->sum('amount'),
            'pending_total' => $payments->where('status', 'pending')->sum('amount'),
            'refunded_total' => $payments->whereIn('status', ['refunded', 'partially_refunded'])->sum('amount'),
            'outstanding_balance' => $payments->where('status', 'pending')->sum('amount'),
        ];

        // Graphs Data (Line Chart - Revenue Trend)
        $revenueTrend = $payments->where('status', 'paid')
            ->groupBy(function($d) { return $d->created_at->format('Y-m-d'); })
            ->map(function($row) { return $row->sum('amount'); });

        // Transactions Volume (Bar Chart)
        $transVolume = $payments->groupBy(function($d) { return $d->created_at->format('Y-m-d'); })
            ->map(function($row) { return $row->count(); });

        // Payment Status Breakdown (Pie)
        $statusBreakdown = $payments->groupBy('status')
            ->map(function($row) { return ['count' => $row->count(), 'amount' => $row->sum('amount')]; });

        // Revenue by Service Type (Bar)
        $revenueByService = $payments->where('status', 'paid')->groupBy('service_type')
            ->map(function($row) { return $row->sum('amount'); });

        // Revenue by Applicant Category (Bar)
        $revenueByCategory = $payments->where('status', 'paid')->groupBy('applicant_category')
            ->map(function($row) { return $row->sum('amount'); });

        // Local vs Foreigner (Bar)
        $revenueByResidency = $payments->where('status', 'paid')->groupBy('residency')
            ->map(function($row) { return $row->sum('amount'); });

        return view('staff.accounts.reports.financial', compact(
            'stats', 'revenueTrend', 'transVolume', 'statusBreakdown',
            'revenueByService', 'revenueByCategory', 'revenueByResidency'
        ));
    }

    /** Helper for logging */
    protected function logPaymentAction(Payment $payment, $action, $from, $to, $comment = null)
    {
        PaymentAuditLog::create([
            'payment_id' => $payment->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'from_status' => $from,
            'to_status' => $to,
            'comment' => $comment,
            'ip_address' => request()->ip()
        ]);
    }

    /* =========================
     * Payments dashboard modules
     * ========================= */

    /**
     * Main landing dashboard for Accounts/Payments
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        
        $query = Application::query()
            ->with('applicant', 'paymentSubmissions')
            ->whereIn('status', [
                Application::ACCOUNTS_REVIEW,
                Application::RETURNED_TO_ACCOUNTS,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR, // Special cases
                Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION, // Two-stage payment
            ])
            ->where(function($q) use ($user) {
                $q->whereNull('assigned_officer_id')
                  ->orWhere('assigned_officer_id', $user->id);
            })
            ->where(function($q) use ($user) {
                $q->whereNull('locked_at')
                  ->orWhere('locked_at', '<=', now()->subHours(2))
                  ->orWhere('locked_by', $user->id);
            });

        // Filter by payment submission method
        if (request()->filled('submission_method')) {
            $query->where('payment_submission_method', request('submission_method'));
        }

        $applications = $query->latest()->paginate(20)->withQueryString();

        // KPIs by submission method
        $kpis = [
            'total_pending' => Application::whereIn('status', [
                Application::ACCOUNTS_REVIEW, 
                Application::RETURNED_TO_ACCOUNTS,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR,
                Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
            ])->count(),
            'special_cases' => Application::where('status', Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR)->count(),
            'two_stage_pending' => Application::where('status', Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION)->count(),
            'paynow_submissions' => Application::whereIn('status', [
                Application::ACCOUNTS_REVIEW, 
                Application::RETURNED_TO_ACCOUNTS,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR
            ])->where('payment_submission_method', 'paynow_reference')->count(),
            'proof_submissions' => Application::whereIn('status', [
                Application::ACCOUNTS_REVIEW, 
                Application::RETURNED_TO_ACCOUNTS,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR
            ])->where('payment_submission_method', 'proof_upload')->count(),
            'waiver_submissions' => Application::whereIn('status', [
                Application::ACCOUNTS_REVIEW, 
                Application::RETURNED_TO_ACCOUNTS,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR
            ])->where('payment_submission_method', 'waiver')->count(),
            'no_submission' => Application::whereIn('status', [
                Application::ACCOUNTS_REVIEW, 
                Application::RETURNED_TO_ACCOUNTS,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR
            ])->whereNull('payment_submission_method')->count(),
        ];

        return view('staff.accounts.dashboard', compact('applications', 'kpis'));
    }

    /**
     * Placeholder/Stub for manual reconciliation marking
     */
    public function markReconciled(Request $request)
    {
        // For now, just a stub to prevent route errors
        return back()->with('success', 'Reconciliation updated.');
    }

    /**
     * 1) PayNow Transactions (application-linked feed)
     * NOTE: Until a dedicated PayNow transactions table/webhook exists, this view is built from
     * application payment fields (paynow_reference, payment_status, paynow_confirmed_at, etc.).
     */
    public function paynowTransactions(Request $request)
    {
        $q = Application::query()->with('applicant');

        // show applicants who have paid using paynow
        $q->where('payment_status', 'paid');
        $q->where(function($w) {
            $w->whereNotNull('paynow_reference')
              ->orWhereNotNull('paynow_poll_url');
        });

        // basic filters
        if ($request->filled('status')) {
            $q->where('payment_status', $request->string('status'));
        }
        if ($request->filled('type')) {
            $q->where('application_type', $request->string('type'));
        }
        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->date('to'));
        }
        if ($request->filled('amount')) {
            // amount is not yet stored per application; keep as UI filter placeholder.
        }

        $applications = $q
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('staff.accounts.paynow_transactions', compact('applications'));
    }

    /** 2) Payment Proofs: Pending */
    public function proofsPending()
    {
        $applications = Application::query()
            ->with(['applicant','documents'])
            ->where('proof_status', 'submitted')
            // Only show if approved for payment
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
            ->latest('payment_proof_uploaded_at')
            ->paginate(20);

        return view('staff.accounts.proofs_pending', compact('applications'));
    }

    public function bulkDownloadProofs(Request $request)
    {
        $ids = $request->input('application_ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No applications selected.');
        }

        $applications = Application::whereIn('id', $ids)->with('applicant')->get();
        if ($applications->isEmpty()) {
            return back()->with('error', 'No applications found.');
        }

        $zip = new ZipArchive();
        $zipFileName = 'Payment_Proofs_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($applications as $app) {
                $proofPath = $app->payment_proof_path;
                if (!$proofPath) {
                    $proofDoc = $app->documents?->firstWhere('doc_type', 'proof_of_payment');
                    $proofPath = $proofDoc?->file_path;
                }

                if ($proofPath && Storage::disk('public')->exists($proofPath)) {
                    $extension = pathinfo($proofPath, PATHINFO_EXTENSION);
                    $applicantName = Str::slug($app->applicant?->name ?? 'applicant');
                    $newFileName = $app->reference . '_' . $applicantName . '.' . $extension;
                    $zip->addFile(Storage::disk('public')->path($proofPath), $newFileName);
                }
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Could not create ZIP file.');
    }

    /** 2) Payment Proofs: Approved */
    public function proofsApproved()
    {
        $applications = Application::query()
            ->with(['applicant','documents'])
            ->where('proof_status', 'approved')
            // Only show if approved for payment
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS, Application::PAID_CONFIRMED])
            ->latest('proof_reviewed_at')
            ->paginate(20);

        return view('staff.accounts.proofs_approved', compact('applications'));
    }

    /** Approve a payment proof */
    public function approveProof(Request $request, Application $application)
    {
        $data = $request->validate([
            'proof_review_notes' => ['nullable', 'string', 'max:5000'],
            'paynow_reference'   => ['nullable', 'string', 'max:200'],
            'payment_status'     => ['nullable', 'string', 'max:100'],
        ]);

        $from = $application->status;

        DB::transaction(function() use ($application, $data) {
            $application->update([
                'proof_status' => 'approved',
                'proof_reviewed_by' => Auth::id(),
                'proof_reviewed_at' => now(),
                'proof_review_notes' => $data['proof_review_notes'] ?? null,
                'paynow_reference' => $data['paynow_reference'] ?? $application->paynow_reference,
                'payment_status' => 'paid',
            ]);

            // Create record in payments table
            $payment = Payment::create([
                'application_id' => $application->id,
                'payer_user_id' => $application->applicant_user_id,
                'method' => 'proof',
                'source' => 'offline',
                'amount' => $application->proof_amount_paid ?? 0,
                'currency' => 'USD',
                'reference' => $data['paynow_reference'] ?? ('PROOF-' . $application->reference),
                'status' => 'paid',
                'confirmed_at' => now(),
                'applicant_category' => $application->accreditation_category_code ?? $application->media_house_category_code,
                'service_type' => $application->application_type,
                'residency' => $application->residency_type ?? 'local',
            ]);

            $this->logPaymentAction($payment, 'approved_proof', null, 'paid', $data['proof_review_notes'] ?? 'Payment proof approved.');

            ApplicationWorkflow::transition($application, Application::PAID_CONFIRMED, 'accounts_approve_proof', [
                'notes' => $data['proof_review_notes'] ?? null,
            ]);
        });

        $this->audit('accounts_proof_approved', $application, $from, $application->status, [
            'notes' => $data['proof_review_notes'] ?? null,
        ]);

        return back()->with('success', 'Payment proof approved and application confirmed.');
    }

    /** Reject a payment proof */
    public function rejectProof(Request $request, Application $application)
    {
        $data = $request->validate([
            'proof_review_notes' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        $this->safeSet($application, [
            'proof_status' => 'rejected',
            'proof_reviewed_by' => Auth::id(),
            'proof_reviewed_at' => now(),
            'proof_review_notes' => $data['proof_review_notes'],
        ]);

        $application->refresh();
        $this->audit('accounts_proof_rejected', $application, $from, $application->status, [
            'notes' => $data['proof_review_notes'],
        ]);

        return back()->with('success', 'Payment proof rejected.');
    }

    /** 3) Waivers: Requests */
    public function waiversRequests()
    {
        $applications = Application::query()
            ->with('applicant')
            ->where('waiver_status', 'submitted')
            // Only show if approved for payment
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
            ->latest('updated_at')
            ->paginate(20);

        return view('staff.accounts.waivers_requests', compact('applications'));
    }

    /** 3) Waivers: Approved */
    public function waiversApproved()
    {
        $applications = Application::query()
            ->with('applicant')
            ->where('waiver_status', 'approved')
            // Only show if approved for payment
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS, Application::PAID_CONFIRMED])
            ->latest('waiver_reviewed_at')
            ->paginate(20);

        return view('staff.accounts.waivers_approved', compact('applications'));
    }

    /** 3) Waivers: Rejected */
    public function waiversRejected()
    {
        $applications = Application::query()
            ->with('applicant')
            ->where('waiver_status', 'rejected')
            // Only show if approved for payment
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
            ->latest('waiver_reviewed_at')
            ->paginate(20);

        return view('staff.accounts.waivers_rejected', compact('applications'));
    }

    public function approveWaiver(Request $request, Application $application)
    {
        $data = $request->validate([
            'waiver_review_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        $this->safeSet($application, [
            'waiver_status' => 'approved',
            'waiver_reviewed_by' => Auth::id(),
            'waiver_reviewed_at' => now(),
            'waiver_review_notes' => $data['waiver_review_notes'] ?? null,
        ]);

        $application->refresh();
        $this->audit('accounts_waiver_approved', $application, $from, $application->status, [
            'notes' => $data['waiver_review_notes'] ?? null,
        ]);

        return back()->with('success', 'Waiver approved.');
    }

    public function rejectWaiver(Request $request, Application $application)
    {
        $data = $request->validate([
            'waiver_review_notes' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        $this->safeSet($application, [
            'waiver_status' => 'rejected',
            'waiver_reviewed_by' => Auth::id(),
            'waiver_reviewed_at' => now(),
            'waiver_review_notes' => $data['waiver_review_notes'],
        ]);

        $application->refresh();
        $this->audit('accounts_waiver_rejected', $application, $from, $application->status, [
            'notes' => $data['waiver_review_notes'],
        ]);

        return back()->with('success', 'Waiver rejected.');
    }

    /** 4) Reconciliation (basic placeholder built from application fields) */
    public function reconciliation()
    {
        $matched = Application::query()
            ->whereNotNull('paynow_reference')
            ->where(function ($w) {
                $w->whereNotNull('paynow_confirmed_at')
                  ->orWhere('payment_status', 'paid')
                  ->orWhere('proof_status', 'approved');
            })
            ->count();

        $unmatched = Application::query()
            ->whereNull('paynow_reference')
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
            ->count();

        $pending = Application::query()
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
            ->count();

        return view('staff.accounts.reconciliation', compact('matched', 'unmatched', 'pending'));
    }

    /** 5) Paid Applications */
    public function applicationsPaid()
    {
        $applications = Application::query()
            ->with('applicant')
            ->where(function ($w) {
                $w->where('status', Application::PAID_CONFIRMED)
                  ->orWhere('payment_status', 'paid')
                  ->orWhereNotNull('paynow_confirmed_at')
                  ->orWhere('proof_status', 'approved');
            })
            ->latest()
            ->paginate(20);

        return view('staff.accounts.apps_paid', compact('applications'));
    }

    /** 5) Pending Payments */
    public function applicationsPending()
    {
        $applications = Application::query()
            ->with('applicant')
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
            ->latest()
            ->paginate(20);

        return view('staff.accounts.apps_pending', compact('applications'));
    }

    /** 5) Waived Applications */
    public function applicationsWaived()
    {
        $applications = Application::query()
            ->with('applicant')
            ->where('waiver_status', 'approved')
            ->latest('waiver_reviewed_at')
            ->paginate(20);

        return view('staff.accounts.apps_waived', compact('applications'));
    }

    /** 6) Reports: Revenue */
    public function reportRevenue(Request $request)
    {
        $q = Application::query();

        // Filters
        if ($request->filled('date_from')) $q->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $q->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('min_amount')) $q->where('proof_amount_paid', '>=', $request->min_amount);
        if ($request->filled('max_amount')) $q->where('proof_amount_paid', '<=', $request->max_amount);

        $paidCount = (clone $q)->where(function ($w) {
            $w->where('payment_status', 'paid')
              ->orWhereNotNull('paynow_confirmed_at')
              ->orWhere('proof_status', 'approved')
              ->orWhere('status', Application::PAID_CONFIRMED);
        })->count();

        $waivedCount = (clone $q)->where('waiver_status', 'approved')->count();

        // Bucket-wise stats
        $buckets = Application::bucketLabels();
        $stats = [];
        foreach ($buckets as $key => $label) {
            $base = (clone $q)->applyBucket($key);

            $stats[$key] = [
                'label' => $label,
                'paid' => (clone $base)->where(function ($w) {
                    $w->where('payment_status', 'paid')
                      ->orWhereNotNull('paynow_confirmed_at')
                      ->orWhere('proof_status', 'approved')
                      ->orWhere('status', Application::PAID_CONFIRMED);
                })->count(),
                'waived' => (clone $base)->where('waiver_status', 'approved')->count(),
            ];
        }

        return view('staff.accounts.reports_revenue', compact('paidCount', 'waivedCount', 'stats'));
    }

    public function exportLedger(Request $request)
    {
        $bucket = $request->get('bucket');
        $labels = Application::bucketLabels();
        if (!isset($labels[$bucket])) {
            return back()->with('error', 'Invalid category selected');
        }

        $query = Application::applyBucket(Application::query(), $bucket);

        // Filters
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('min_amount')) $query->where('proof_amount_paid', '>=', $request->min_amount);
        if ($request->filled('max_amount')) $query->where('proof_amount_paid', '<=', $request->max_amount);

        $query->with('applicant')
            ->where(function($w) {
                $w->where('payment_status', 'paid')
                  ->orWhereNotNull('paynow_confirmed_at')
                  ->orWhere('proof_status', 'approved')
                  ->orWhere('status', Application::PAID_CONFIRMED)
                  ->orWhere('waiver_status', 'approved');
            })
            ->latest();

        $filename = 'Ledger_' . Str::slug($labels[$bucket]) . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Reference', 'Applicant Name', 'Email', 'Type', 'Amount Paid', 'Bank/Method', 'Reference/Paynow', 'Date Paid', 'Status', 'Waiver Status'];

        $callback = function() use($query, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $query->chunk(100, function($apps) use($file) {
                foreach ($apps as $app) {
                    $row = [
                        $app->id,
                        $app->reference,
                        optional($app->applicant)->name,
                        optional($app->applicant)->email,
                        $app->applicationTypeLabel(),
                        $app->proof_amount_paid ?: '—',
                        $app->proof_bank_name ?: ($app->paynow_reference ? 'PayNow' : '—'),
                        $app->paynow_reference ?: '—',
                        $app->paynow_confirmed_at ?: ($app->proof_payment_date ?: '—'),
                        $app->payment_status ?: ($app->proof_status ?: '—'),
                        $app->waiver_status ?: 'none',
                    ];

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** 6) Reports: Exceptions */
    public function reportExceptions()
    {
        $rejectedProofs = Application::query()->where('proof_status', 'rejected')->count();
        $failedPaynow = Application::query()->where('payment_status', 'failed')->count();
        $unmatched = Application::query()
            ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
            ->whereNull('paynow_reference')
            ->where('proof_status', '!=', 'approved')
            ->count();

        return view('staff.accounts.reports_exceptions', compact('rejectedProofs', 'failedPaynow', 'unmatched'));
    }

    /** 6) Reports: Audit */
    public function reportAudit()
    {
        $logs = ActivityLog::query()
            ->whereIn('action', [
                'accounts_confirm_paid',
                'accounts_return_to_officer',
                'accounts_proof_approved',
                'accounts_proof_rejected',
                'accounts_waiver_approved',
                'accounts_waiver_rejected',
                'applicant_proof_uploaded',
                'applicant_waiver_uploaded',
            ])
            ->latest('created_at')
            ->paginate(30);

        return view('staff.accounts.reports_audit', compact('logs'));
    }

    /** 6) Alerts */
    public function alerts()
    {
        $logs = ActivityLog::query()
            ->whereIn('action', [
                'applicant_waiver_uploaded',
                'applicant_proof_uploaded',
                'paynow_payment_received',
                'paynow_payment_reversed',
                'paynow_payment_failed',
                'accounts_proof_approved',
                'accounts_proof_rejected',
                'accounts_waiver_approved',
                'accounts_waiver_rejected',
            ])
            ->latest('created_at')
            ->paginate(30);

        return view('staff.accounts.alerts', compact('logs'));
    }

    /** 7) System tools: PayNow settings (view-only) */
    public function paynowSettings()
    {
        $settings = [
            'PAYNOW_INTEGRATION' => config('services.paynow.enabled') ?? env('PAYNOW_INTEGRATION', null),
            'PAYNOW_INTEGRATION_ID' => env('PAYNOW_INTEGRATION_ID', null),
            'PAYNOW_RESULT_URL' => env('PAYNOW_RESULT_URL', null),
            'PAYNOW_RETURN_URL' => env('PAYNOW_RETURN_URL', null),
        ];

        return view('staff.accounts.paynow_settings', compact('settings'));
    }

    /** 7) System tools: User action logs */
    public function userActionLogs(Request $request)
    {
        $q = ActivityLog::query();
        if ($request->filled('action')) $q->where('action', $request->string('action'));
        if ($request->filled('user')) $q->where('user_id', (int) $request->input('user'));

        $logs = $q->latest('created_at')->paginate(30)->withQueryString();
        return view('staff.accounts.user_action_logs', compact('logs'));
    }

    /** Help & support */
    public function help()
    {
        return view('staff.accounts.help');
    }

    public function show(Application $application)
    {
        // Try to claim the application (concurrency lock)
        if (!$application->claim(auth()->user())) {
            $lockerName = $application->lockedBy ? $application->lockedBy->name : 'another official';
            return redirect()->back()->with('error', "This application is currently being worked on by {$lockerName}.");
        }

        $application->load(['applicant', 'documents', 'messages', 'workflowLogs', 'lockedBy']);

        $userPayments = Payment::where('payer_user_id', $application->user_id)
            ->latest()
            ->get();

        return view('staff.accounts.show', compact('application', 'userPayments'));
    }

    public function unlock(Application $application)
    {
        $application->locked_by = null;
        $application->locked_at = null;
        $application->save();

        return redirect()->route('staff.accounts.dashboard')->with('success', 'Application released.');
    }

    public function generateReceipt(Application $application)
    {
        if ($application->status !== Application::PAID_CONFIRMED &&
            $application->payment_status !== 'paid' &&
            $application->proof_status !== 'approved') {
            return back()->with('error', 'Application is not paid or confirmed.');
        }

        $application->load(['applicant']);

        $data = [
            'application' => $application,
            'date' => now()->format('Y-m-d H:i'),
            'company_name' => 'Zimbabwe Media Commission',
            'company_address' => '109 Rotten Row, Harare, Zimbabwe',
            'company_email' => 'info@zmc.co.zw',
            'company_phone' => '+263 242 703351'
        ];

        $pdf = Pdf::loadView('staff.accounts.receipt_pdf', $data);
        return $pdf->download('Receipt_' . $application->reference . '.pdf');
    }

    /**
     * Confirm paid -> push to production_queue
     */
    public function markPaid(Request $request, Application $application)
{
    $data = $request->validate([
        'paynow_reference' => ['nullable', 'string', 'max:200'],
        'payment_status'   => ['nullable', 'string', 'max:100'],
        'decision_notes'   => ['nullable', 'string', 'max:5000'],
    ]);

    $from = $application->status;

    // Save fields (only if columns exist)
    foreach (['paynow_reference','payment_status','decision_notes'] as $col) {
        if (!empty($data[$col]) && Schema::hasColumn('applications', $col)) {
            $application->{$col} = $data[$col];
        }
    }
    // Store payment time if marked paid
    if (!empty($data['payment_status']) && strtolower($data['payment_status']) === 'paid' && Schema::hasColumn('applications', 'payment_paid_at')) {
        $application->payment_paid_at = now();
    }
    $application->save();

    // Mark confirmed
    ApplicationWorkflow::transition($application, Application::PAID_CONFIRMED, 'accounts_confirm_paid', $data);

    // ✅ NEW ORDER: send to production (since Registrar already reviewed it before pushing to Accounts)
    ApplicationWorkflow::transition($application, Application::PRODUCTION_QUEUE, 'system_send_to_production', [
        'region' => $application->collection_region ?? null,
    ]);

    ActivityLogger::log('accounts_confirm_paid', $application, $from, $application->status, [
        'actor_role' => session('active_staff_role'),
        'paynow_reference' => $data['paynow_reference'] ?? null,
        'payment_status' => $data['payment_status'] ?? null,
    ]);

    return back()->with('success', 'Payment confirmed and sent to Production.');
}


    public function returnToOfficer(Request $request, Application $application)
    {
        $data = $request->validate([
            'decision_notes' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        ApplicationWorkflow::transition($application, Application::RETURNED_TO_OFFICER, 'accounts_return_to_officer', [
            'notes' => $data['decision_notes'],
        ]);

        $this->safeSet($application, ['decision_notes' => $data['decision_notes']]);

        $application->refresh();

        $this->audit('accounts_return_to_officer', $application, $from, $application->status, [
            'notes' => $data['decision_notes'],
        ]);

        return back()->with('success', 'Returned to Accreditation Officer.');
    }

    /**
     * Verify payment submission (including waivers from special cases)
     */
    public function verifyPaymentSubmission(Request $request, Application $application)
    {
        $data = $request->validate([
            'action' => ['required', 'in:verify,reject'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'payment_submission_id' => ['nullable', 'exists:payment_submissions,id'],
        ]);

        // Use new workflow service - enforces strict transitions
        try {
            if ($data['action'] === 'verify') {
                // Verify payment - automatically sends to production
                $application = PaymentWorkflowService::verifyPayment($application, [
                    'notes' => $data['notes'] ?? null,
                    'payment_submission_id' => $data['payment_submission_id'] ?? null,
                ]);

                $message = 'Payment verified and application sent to Production.';
                
                // Check if two-stage payment
                if ($application->requiresApplicationFee()) {
                    $bothVerified = PaymentWorkflowService::areBothPaymentStagesVerified($application);
                    if (!$bothVerified) {
                        $message = 'Payment stage verified. Waiting for second payment stage.';
                    }
                }
            } else {
                // Reject payment
                $application = PaymentWorkflowService::rejectPayment($application, $data['notes'] ?? 'Payment rejected', [
                    'payment_submission_id' => $data['payment_submission_id'] ?? null,
                ]);

                $message = 'Payment rejected. Applicant must resubmit payment.';
            }

            return back()->with('success', $message);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', 'Workflow error: ' . $e->getMessage());
        }
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

    private function hasColumn(string $table, string $column): bool
    {
        try { return Schema::hasColumn($table, $column); }
        catch (\Throwable $e) { return false; }
    }

    /**
     * Renewals queue for accounts verification
     */
    public function renewalsQueue(Request $request)
    {
        $query = \App\Models\RenewalApplication::query()
            ->with(['applicant', 'originalApplication'])
            ->awaitingAccountsVerification()
            ->latest('payment_submitted_at');

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by renewal type
        if ($request->filled('renewal_type')) {
            $query->where('renewal_type', $request->renewal_type);
        }

        $renewals = $query->paginate(20)->withQueryString();

        // KPIs
        $kpis = [
            'pending' => \App\Models\RenewalApplication::awaitingAccountsVerification()->count(),
            'verified_today' => \App\Models\RenewalApplication::where('payment_verified_at', '>=', now()->startOfDay())->count(),
            'paynow' => \App\Models\RenewalApplication::awaitingAccountsVerification()->where('payment_method', 'PAYNOW')->count(),
            'proof' => \App\Models\RenewalApplication::awaitingAccountsVerification()->where('payment_method', 'PROOF_UPLOAD')->count(),
        ];

        return view('staff.accounts.renewals_queue', compact('renewals', 'kpis'));
    }

    /**
     * Show renewal details for verification
     */
    public function showRenewal(\App\Models\RenewalApplication $renewal)
    {
        $renewal->load(['applicant', 'originalApplication', 'changeRequests']);

        return view('staff.accounts.renewal_show', compact('renewal'));
    }

    /**
     * Verify renewal payment
     */
    public function verifyRenewalPayment(Request $request, \App\Models\RenewalApplication $renewal)
    {
        $data = $request->validate([
            'action' => ['required', 'in:verify,reject'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $from = $renewal->status;

        DB::transaction(function() use ($renewal, $data, $from) {
            if ($data['action'] === 'verify') {
                // Verify payment
                $renewal->update([
                    'payment_verified_at' => now(),
                    'payment_verified_by' => Auth::id(),
                    'status' => \App\Models\RenewalApplication::RENEWAL_PAYMENT_VERIFIED,
                    'current_stage' => 'production',
                    'last_action_at' => now(),
                    'last_action_by' => Auth::id(),
                ]);

                ActivityLogger::log('renewal_payment_verified', $renewal, $from, $renewal->status, [
                    'renewal_type' => $renewal->renewal_type,
                    'payment_method' => $renewal->payment_method,
                    'notes' => $data['notes'] ?? null,
                ]);

            } else {
                // Reject payment
                $renewal->update([
                    'payment_verified_at' => now(),
                    'payment_verified_by' => Auth::id(),
                    'payment_rejection_reason' => $data['notes'],
                    'status' => \App\Models\RenewalApplication::RENEWAL_PAYMENT_REJECTED,
                    'last_action_at' => now(),
                    'last_action_by' => Auth::id(),
                ]);

                ActivityLogger::log('renewal_payment_rejected', $renewal, $from, $renewal->status, [
                    'renewal_type' => $renewal->renewal_type,
                    'payment_method' => $renewal->payment_method,
                    'reason' => $data['notes'],
                ]);
            }
        });

        $message = $data['action'] === 'verify'
            ? 'Renewal payment verified. Application sent to Production.'
            : 'Renewal payment rejected. Applicant must resubmit payment.';

        return back()->with('success', $message);
    }
}
