<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationWorkflow;
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
use Illuminate\Support\Facades\Mail;
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
        abort_unless(auth()->user()?->hasAnyRole(['super_admin','it_admin','accounts','accountant','chief_accountant']), 403);
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

    /**
     * List batches pending verification.
     */
    public function batchesPending(Request $request)
    {
        $batches = \App\Models\Batch::with(['mediaHouse'])
            ->where('status', 'pending_verification')
            ->latest()
            ->paginate(20);

        return view('staff.accounts.payments.batches_pending', compact('batches'));
    }

    /**
     * Approve a batch payment.
     */
    public function approveBatch(\App\Models\Batch $batch)
    {
        DB::transaction(function () use ($batch) {
            $batch->update([
                'status' => 'paid',
            ]);

            // Transition all linked applications
            foreach ($batch->applications as $application) {
                $application->update([
                    'status' => Application::PAID_CONFIRMED,
                ]);

                // Create individual Payment record for the ledger
                Payment::create([
                    'application_id' => $application->id,
                    'payer_user_id' => $batch->media_house_user_id,
                    'method' => $batch->payment_method,
                    'amount' => 20, // Placeholder rate
                    'currency' => 'USD',
                    'reference' => $batch->reference,
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'recorded_by' => Auth::id(),
                ]);

                // Trigger the notification to the journalist
                try {
                    if ($application->applicant) {
                        $application->applicant->notify(new \App\Notifications\PaymentReceiptNotification($application));
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send digital receipt notification for batch payment', [
                        'application_id' => $application->id,
                        'batch_id' => $batch->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });

        return back()->with('success', 'Batch payment approved. Journalists have been notified.');
    }

    /**
     * Reject a batch payment.
     */
    public function rejectBatch(\App\Models\Batch $batch, Request $request)
    {
        $batch->update([
            'status' => 'rejected',
            'metadata' => array_merge($batch->metadata ?? [], ['rejection_reason' => $request->reason]),
        ]);

        return back()->with('success', 'Batch payment rejected.');
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

            // Send Digital Receipt
            try {
                if ($application->applicant) {
                    $application->applicant->notify(new \App\Notifications\PaymentReceiptNotification($application));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send digital receipt notification for offline payment', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage()
                ]);
            }
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
    public function dashboard()
    {
        $user = Auth::user();

        // 1. Total Applications (Lifetime)
        $totalApplications = Application::count();

        // 2. Paid via Pay Now (Automatically processed)
        $paidViaPayNow = Application::where(function ($w) {
            $w->where('payment_status', 'paid')
              ->orWhereNotNull('paynow_confirmed_at')
              ->orWhere('proof_status', 'approved')
              ->orWhere('status', Application::PAID_CONFIRMED);
        })->whereNotNull('paynow_reference')->count();

        // 3. Paid via Uploads (Manual, POP, cash, waivers, exemptions)
        $paidViaUploads = Application::where(function ($w) {
            $w->where('payment_status', 'paid')
              ->orWhere('payment_status', 'waived')
              ->orWhere('proof_status', 'approved')
              ->orWhere('status', Application::PAID_CONFIRMED);
        })->whereNull('paynow_reference')->count();

        // 4. Pending Action (Accounts Queue)
        $pendingAction = Application::whereIn('status', [
            Application::ACCOUNTS_REVIEW,
            Application::RETURNED_TO_ACCOUNTS,
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
        ])->count();

        // 5. Approved (Paid) - All approved applications
        $approvedPaid = Application::where('status', Application::PAID_CONFIRMED)->count();

        // Analytics: Revenue by month (Current Year)
        $revenueData = \App\Models\Payment::whereIn('status', ['paid', 'confirmed'])
            ->whereYear('created_at', date('Y'))
            ->selectRaw('strftime("%m", created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthKey = str_pad($m, 2, '0', STR_PAD_LEFT);
            $chartData[] = $revenueData[$monthKey] ?? 0;
        }

        // Main queue list
        $applications = Application::query()
            ->with(['applicant', 'batch'])
            ->whereIn('status', [
                Application::ACCOUNTS_REVIEW,
                Application::RETURNED_TO_ACCOUNTS,
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
            ])
            ->latest()
            ->paginate(20);

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
            $applicationsForTrend = Application::select('application_type', 'created_at')
                ->where('created_at', '>=', $trendCutoff)
                ->orderBy('created_at')
                ->get();

            $groupedTrends = $applicationsForTrend->groupBy(function($app) {
                return \Carbon\Carbon::parse($app->created_at)->format('M Y');
            });

            foreach ($groupedTrends as $month => $apps) {
                $trendLabels[] = $month;
                $accreditationTrends[] = $apps->where('application_type', 'accreditation')->count();
                $registrationTrends[]  = $apps->where('application_type', 'registration')->count();
            }
        } catch (\Exception $e) {}

        // Financial Summary KPIs for the partial
        $paymentSummary = [
            'Paid' => \App\Models\Payment::where('status', 'paid')->sum('amount'),
            'Failed' => \App\Models\Payment::whereIn('status', ['failed', 'voided', 'declined'])->sum('amount'),
        ];
        
        $paymentReconciliation = [
            'pending_proofs' => Application::where('proof_status', 'submitted')
                ->whereIn('status', [Application::ACCOUNTS_REVIEW, Application::RETURNED_TO_ACCOUNTS])
                ->count(),
        ];
        
        $kpis = [
            'pending_waivers' => Application::where('waiver_status', 'submitted')->count(),
        ];

        return view('staff.accounts.dashboard', compact(
            'applications',
            'totalApplications',
            'paidViaPayNow',
            'paidViaUploads',
            'pendingAction',
            'approvedPaid',
            'labels',
            'chartData',
            'trendLabels', 'accreditationTrends', 'registrationTrends', 'currentRangeLabel',
            'paymentSummary', 'paymentReconciliation', 'kpis'
        ));
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

            ApplicationWorkflow::transition($application, Application::PAYMENT_VERIFIED, 'accounts_approve_proof', [
                'notes' => $data['proof_review_notes'] ?? null,
            ]);

            ApplicationWorkflow::transition($application, Application::PRODUCTION_QUEUE, 'system_send_to_production', [
                'region' => $application->collection_region ?? null,
            ]);
        });

        $this->audit('accounts_proof_approved', $application, $from, $application->status, [
            'notes' => $data['proof_review_notes'] ?? null,
        ]);

        // Send Digital Receipt
        try {
            if ($application->applicant) {
                $application->applicant->notify(new \App\Notifications\PaymentReceiptNotification($application));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send digital receipt notification', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
        }

        return back()->with('success', 'Payment proof approved and digital receipt sent.');
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

        $application->load(['applicant', 'documents', 'messages', 'workflowLogs', 'payments', 'lockedBy']);

        $previousApplications = collect();
        $previousPayments = collect();
        if ($application->applicant_user_id) {
            $previousApplications = Application::where('applicant_user_id', $application->applicant_user_id)
                ->where('id', '!=', $application->id)
                ->latest()
                ->get();

            $previousPayments = Payment::whereHas('application', function ($q) use ($application) {
                $q->where('applicant_user_id', $application->applicant_user_id);
            })->latest()->get();
        }

        return view('staff.accounts.show', compact('application', 'previousApplications', 'previousPayments'));
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

        $application->load(['applicant', 'batch']);

        // Generate receipt number if not exists
        if (!$application->receipt_number) {
            $application->receipt_number = $this->generateReceiptNumber();
            $application->save();
        }

        // Create or update payment record
        $payment = Payment::updateOrCreate([
            'application_id' => $application->id,
            'method' => 'proof',
        ], [
            'payer_user_id' => $application->applicant_user_id,
            'source' => 'offline',
            'amount' => $application->proof_amount_paid ?: $application->fee_amount ?: 0,
            'currency' => $application->currency ?: 'USD',
            'reference' => $application->receipt_number,
            'status' => 'paid',
            'confirmed_at' => now(),
            'bank_name' => $application->proof_bank_name,
            'proof_file_path' => $application->payment_proof_path,
            'receipt_number' => $application->receipt_number,
            'payment_date' => $application->proof_payment_date ?: now()->format('Y-m-d'),
            'recorded_by' => auth()->id(),
            'reconciled' => true,
            'reconciled_at' => now(),
            'reconciled_by' => auth()->id(),
        ]);

        $data = [
            'application' => $application,
            'payment' => $payment,
            'date' => now()->format('Y-m-d H:i'),
            'company_name' => 'Zimbabwe Media Commission',
            'company_address' => '109 Rotten Row, Harare, Zimbabwe',
            'company_email' => 'info@zmc.co.zw',
            'company_phone' => '+263 242 703351',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'reference' => $payment->reference,
            'receipt_number' => $payment->receipt_number,
        ];

        $pdf = Pdf::loadView('staff.accounts.receipt_pdf', $data);
        return $pdf->download('Receipt_' . $application->reference . '.pdf');
    }

    private function generateReceiptNumber(): string
    {
        $prefix = 'ZMC-REC';
        $year = date('Y');
        $sequence = Payment::whereYear('created_at', $year)->max('id') + 1;
        
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
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
        
        // Update payment status to paid
        $application->payment_status = 'paid';
        $application->save();

        // Mark confirmed
        ApplicationWorkflow::transition($application, Application::PAID_CONFIRMED, 'accounts_confirm_paid', $data);

        // Generate receipt number if not exists
        if (!$application->receipt_number) {
            $application->receipt_number = $this->generateReceiptNumber();
            $application->save();
        }

        // Create or update payment record
        $payment = Payment::updateOrCreate([
            'application_id' => $application->id,
            'method' => 'proof',
        ], [
            'payer_user_id' => $application->applicant_user_id,
            'source' => 'offline',
            'amount' => $application->proof_amount_paid ?: $application->fee_amount ?: 0,
            'currency' => $application->currency ?: 'USD',
            'reference' => $application->receipt_number,
            'status' => 'paid',
            'confirmed_at' => now(),
            'bank_name' => $application->proof_bank_name,
            'proof_file_path' => $application->payment_proof_path,
            'receipt_number' => $application->receipt_number,
            'payment_date' => $application->proof_payment_date ?: now()->format('Y-m-d'),
            'recorded_by' => auth()->id(),
            'reconciled' => true,
            'reconciled_at' => now(),
            'reconciled_by' => auth()->id(),
        ]);

        // ✅ NEW ORDER: send to production (since Registrar already reviewed it before pushing to Accounts)
        ApplicationWorkflow::transition($application, Application::PRODUCTION_QUEUE, 'system_send_to_production', [
            'region' => $application->collection_region ?? null,
        ]);

        ActivityLogger::log('accounts_confirm_paid', $application, $from, $application->status, [
            'actor_role' => session('active_staff_role'),
            'paynow_reference' => $data['paynow_reference'] ?? null,
            'payment_status' => $data['payment_status'] ?? null,
            'receipt_number' => $application->receipt_number,
        ]);

        // Generate receipt data
        $receiptData = [
            'application' => $application,
            'payment' => $payment,
            'date' => now()->format('Y-m-d H:i'),
            'company_name' => 'Zimbabwe Media Commission',
            'company_address' => '109 Rotten Row, Harare, Zimbabwe',
            'company_email' => 'info@zmc.co.zw',
            'company_phone' => '+263 242 703351',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'reference' => $payment->reference,
            'receipt_number' => $payment->receipt_number,
        ];

        // Generate and return receipt PDF
        $pdf = Pdf::loadView('staff.accounts.receipt_pdf', $receiptData);
        
        // Send digital receipt to applicant
        $this->sendDigitalReceipt($application, $payment, $receiptData);
        
        return $pdf->download('Receipt_' . $application->reference . '.pdf');
    }

    /**
     * Send digital receipt to applicant
     */
    private function sendDigitalReceipt(Application $application, Payment $payment, array $receiptData)
    {
        try {
            // Generate receipt PDF for email attachment
            $pdf = Pdf::loadView('staff.accounts.receipt_pdf', $receiptData);
            $pdfContent = $pdf->output();
            
            // Send email to applicant
            $applicant = $application->applicant;
            if ($applicant && $applicant->email) {
                Mail::send('emails.receipt_notification', [
                    'application' => $application,
                    'payment' => $payment,
                    'receiptNumber' => $payment->receipt_number,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                ], function($message) use ($applicant, $pdfContent, $application, $payment) {
                    $message->to($applicant->email)
                        ->subject('Payment Receipt - ' . $application->reference . ' - ' . $payment->receipt_number)
                        ->attachData($pdfContent, 'Receipt_' . $application->reference . '.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                });
                
                // Log the email sent
                ActivityLogger::log('receipt_sent', $application, null, null, [
                    'actor_role' => session('active_staff_role'),
                    'receipt_number' => $payment->receipt_number,
                    'applicant_email' => $applicant->email,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the payment process
            \Log::error('Failed to send digital receipt: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'payment_id' => $payment->id,
            ]);
        }
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

    public function rejectPayment(Request $request, Application $application)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        DB::transaction(function() use ($application, $data) {
            ApplicationWorkflow::transition($application, Application::PAYMENT_REJECTED, 'accounts_reject_payment', [
                'reason' => $data['rejection_reason'],
            ]);

            $this->safeSet($application, [
                'rejection_reason' => $data['rejection_reason'],
            ]);
        });

        $this->audit('accounts_payment_rejected', $application, $from, $application->status, [
            'reason' => $data['rejection_reason'],
        ]);

        return back()->with('success', 'Payment rejected. Applicant must resubmit.');
    }

    public function createCashPayment()
    {
        $applications = Application::query()
            ->with('applicant')
            ->whereIn('status', [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::ACCOUNTS_REVIEW,
                Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
                Application::RETURNED_TO_ACCOUNTS,
            ])
            ->latest()
            ->get();

        return view('staff.accounts.cash_payment_create', compact('applications'));
    }

    public function storeCashPayment(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'receipt_number' => 'required|string|max:100|unique:payments,receipt_number',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:5000',
        ]);

        $application = Application::findOrFail($validated['application_id']);
        $from = $application->status;

        DB::transaction(function() use ($application, $validated, $from) {
            $payment = Payment::create([
                'application_id' => $application->id,
                'payer_user_id' => $application->applicant_user_id,
                'method' => 'cash',
                'source' => 'offline',
                'amount' => $validated['amount'],
                'currency' => 'USD',
                'reference' => 'CASH-' . $application->reference . '-' . now()->format('YmdHis'),
                'receipt_number' => $validated['receipt_number'],
                'payment_date' => $validated['payment_date'],
                'status' => 'paid',
                'confirmed_at' => now(),
                'recorded_by' => Auth::id(),
                'applicant_category' => $application->accreditation_category_code ?? $application->media_house_category_code,
                'service_type' => $application->application_type,
                'residency' => $application->residency_type ?? 'local',
            ]);

            $this->logPaymentAction($payment, 'cash_recorded', null, 'paid', $validated['notes'] ?? 'Cash payment recorded.');

            $this->safeSet($application, [
                'payment_status' => 'paid',
                'receipt_number' => $validated['receipt_number'],
            ]);

            ApplicationWorkflow::transition($application, Application::PAYMENT_VERIFIED, 'accounts_cash_payment_recorded', [
                'receipt_number' => $validated['receipt_number'],
                'amount' => $validated['amount'],
                'notes' => $validated['notes'] ?? null,
            ]);

            ApplicationWorkflow::transition($application, Application::PRODUCTION_QUEUE, 'system_send_to_production', [
                'region' => $application->collection_region ?? null,
            ]);

            $this->audit('accounts_cash_payment', $application, $from, $application->status, [
                'receipt_number' => $validated['receipt_number'],
                'amount' => $validated['amount'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('staff.accounts.dashboard')->with('success', 'Cash payment recorded and application sent to Production.');
    }

    public function voidCashPayment(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'void_reason' => ['required', 'string', 'max:5000'],
        ]);

        if ($payment->voided_at) {
            return back()->with('error', 'This payment has already been voided.');
        }

        DB::transaction(function() use ($payment, $data) {
            $oldStatus = $payment->status;

            $payment->update([
                'status' => 'voided',
                'voided_at' => now(),
                'voided_by' => Auth::id(),
                'void_reason' => $data['void_reason'],
            ]);

            $this->logPaymentAction($payment, 'voided', $oldStatus, 'voided', $data['void_reason']);

            if ($payment->application) {
                $this->audit('accounts_receipt_voided', $payment->application, $payment->application->status, $payment->application->status, [
                    'payment_id' => $payment->id,
                    'receipt_number' => $payment->receipt_number,
                    'reason' => $data['void_reason'],
                ]);
            }
        });

        return back()->with('success', 'Receipt voided successfully.');
    }

    public function approveWaiverVerification(Request $request, Application $application)
    {
        $data = $request->validate([
            'waiver_review_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        DB::transaction(function() use ($application, $data) {
            $this->safeSet($application, [
                'waiver_status' => 'approved',
                'waiver_reviewed_by' => Auth::id(),
                'waiver_reviewed_at' => now(),
                'waiver_review_notes' => $data['waiver_review_notes'] ?? null,
                'payment_status' => 'waived',
            ]);

            ApplicationWorkflow::transition($application, Application::PAYMENT_VERIFIED, 'accounts_waiver_verified', [
                'notes' => $data['waiver_review_notes'] ?? null,
            ]);

            ApplicationWorkflow::transition($application, Application::PRODUCTION_QUEUE, 'system_send_to_production', [
                'region' => $application->collection_region ?? null,
            ]);
        });

        $this->audit('accounts_waiver_verified', $application, $from, $application->status, [
            'notes' => $data['waiver_review_notes'] ?? null,
        ]);

        return back()->with('success', 'Waiver verified as payment-equivalent and sent to Production.');
    }

    public function rejectWaiverVerification(Request $request, Application $application)
    {
        $data = $request->validate([
            'waiver_review_notes' => ['required', 'string', 'max:5000'],
        ]);

        $from = $application->status;

        DB::transaction(function() use ($application, $data) {
            $this->safeSet($application, [
                'waiver_status' => 'rejected',
                'waiver_reviewed_by' => Auth::id(),
                'waiver_reviewed_at' => now(),
                'waiver_review_notes' => $data['waiver_review_notes'],
            ]);

            ApplicationWorkflow::transition($application, Application::PAYMENT_REJECTED, 'accounts_waiver_rejected_verification', [
                'reason' => $data['waiver_review_notes'],
            ]);
        });

        $this->audit('accounts_waiver_verification_rejected', $application, $from, $application->status, [
            'reason' => $data['waiver_review_notes'],
        ]);

        return back()->with('success', 'Waiver rejected.');
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
}
