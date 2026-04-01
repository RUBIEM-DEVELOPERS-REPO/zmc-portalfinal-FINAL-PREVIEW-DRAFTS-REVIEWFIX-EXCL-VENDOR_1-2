<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;

class ReceiptController extends Controller
{
    /**
     * Display receipt generation form.
     */
    public function create(Request $request, $applicationId)
    {
        $application = Application::with(['applicant', 'payment'])->findOrFail($applicationId);
        
        // Check if payment exists and is confirmed
        if (!$application->payment || $application->payment->status !== 'confirmed') {
            return back()->with('error', 'Payment must be confirmed before generating receipt.');
        }

        // Check if receipt already exists
        $existingReceipt = Receipt::where('application_id', $application->id)->first();
        if ($existingReceipt) {
            return redirect()->route('receipts.show', $existingReceipt->id)
                           ->with('info', 'Receipt already exists for this application.');
        }

        $paymentMethods = Receipt::getPaymentMethods();

        return view('staff.receipts.create', compact('application', 'paymentMethods'));
    }

    /**
     * Generate and store receipt.
     */
    public function store(Request $request)
    {
        $request->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'payment_method' => ['required', 'in:' . implode(',', array_keys(Receipt::getPaymentMethods()))],
            'transaction_id' => ['nullable', 'required_if:payment_method,paynow', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application = Application::with(['applicant', 'payment'])->findOrFail($request->application_id);

        // Generate receipt
        $receipt = Receipt::create([
            'application_id' => $application->id,
            'applicant_id' => $application->applicant_id,
            'payment_reference' => $application->reference . '-PAY',
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'amount' => $application->payment->amount,
            'payment_date' => $application->payment->confirmed_at ?? now(),
            'status' => Receipt::STATUS_VERIFIED,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'notes' => $request->notes,
            'generated_by' => Auth::id(),
        ]);

        // Generate PDF receipt
        $this->generatePDFReceipt($receipt);

        return redirect()->route('receipts.show', $receipt->id)
                        ->with('success', 'Receipt generated successfully.');
    }

    /**
     * Display receipt details.
     */
    public function show($id)
    {
        $receipt = Receipt::with(['application.applicant', 'verifier', 'generator'])->findOrFail($id);

        return view('staff.receipts.show', compact('receipt'));
    }

    /**
     * Display receipt in browser.
     */
    public function view($id)
    {
        $receipt = Receipt::with(['application.applicant', 'verifier', 'generator'])->findOrFail($id);

        return view('receipts.payment_receipt', compact('receipt'));
    }

    /**
     * Download receipt PDF.
     */
    public function download($id)
    {
        $receipt = Receipt::findOrFail($id);
        $filePath = 'receipts/pdfs/' . $receipt->receipt_number . '.pdf';

        if (!Storage::disk('public')->exists($filePath)) {
            $this->generatePDFReceipt($receipt);
        }

        return Storage::disk('public')->download($filePath, $receipt->receipt_number . '.pdf');
    }

    /**
     * List all receipts.
     */
    public function index(Request $request)
    {
        $query = Receipt::with(['application.applicant', 'verifier']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('application', function ($subQ) use ($search) {
                      $subQ->where('reference', 'like', "%{$search}%");
                  })
                  ->orWhereHas('applicant', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(20);

        $paymentMethods = Receipt::getPaymentMethods();

        return view('staff.receipts.index', compact('receipts', 'paymentMethods'));
    }

    /**
     * Verify payment (public endpoint).
     */
    public function verifyPayment($reference)
    {
        $receipt = Receipt::with(['application.applicant'])
                          ->where('payment_reference', $reference)
                          ->firstOrFail();

        return view('receipts.verification', compact('receipt'));
    }

    /**
     * Export receipts.
     */
    public function export(Request $request)
    {
        $query = Receipt::with(['application.applicant', 'verifier']);

        // Apply same filters as index method
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $receipts = $query->orderBy('created_at', 'desc')->get();

        $filename = 'receipts_export_' . now()->format('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($receipts) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Receipt Number',
                'Payment Reference',
                'Application Reference',
                'Applicant Name',
                'Email',
                'Payment Method',
                'Transaction ID',
                'Amount',
                'Payment Date',
                'Status',
                'Verified By',
                'Generated At',
            ]);

            foreach ($receipts as $receipt) {
                fputcsv($file, [
                    $receipt->receipt_number,
                    $receipt->payment_reference,
                    $receipt->application?->reference,
                    $receipt->applicant?->name,
                    $receipt->applicant?->email,
                    $receipt->payment_method_label,
                    $receipt->transaction_id,
                    $receipt->amount,
                    $receipt->payment_date->format('Y-m-d H:i'),
                    $receipt->status_label,
                    $receipt->verifier?->name,
                    $receipt->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate PDF receipt.
     */
    private function generatePDFReceipt(Receipt $receipt): void
    {
        $receipt->load(['application.applicant', 'verifier', 'generator']);
        
        $pdf = PDF::loadView('receipts.payment_receipt', [
            'receipt' => $receipt
        ]);

        $filePath = 'receipts/pdfs/' . $receipt->receipt_number . '.pdf';
        
        // Ensure directory exists
        $directory = dirname($filePath);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        Storage::disk('public')->put($filePath, $pdf->output());
    }

    /**
     * Verify receipt.
     */
    public function verifyReceipt(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);
        
        if ($receipt->isVerified()) {
            return response()->json(['success' => false, 'message' => 'Receipt is already verified.']);
        }

        if ($receipt->markAsVerified(Auth::id())) {
            ActivityLogger::log('receipt_verified', $receipt, null, null, [
                'verified_by' => Auth::id(),
                'receipt_number' => $receipt->receipt_number,
            ]);

            return response()->json(['success' => true, 'message' => 'Receipt verified successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to verify receipt.']);
    }

    /**
     * Cancel receipt.
     */
    public function cancelReceipt(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);
        
        if ($receipt->isCancelled()) {
            return response()->json(['success' => false, 'message' => 'Receipt is already cancelled.']);
        }

        if ($receipt->cancel()) {
            ActivityLogger::log('receipt_cancelled', $receipt, null, null, [
                'cancelled_by' => Auth::id(),
                'receipt_number' => $receipt->receipt_number,
            ]);

            return response()->json(['success' => true, 'message' => 'Receipt cancelled successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to cancel receipt.']);
    }

    /**
     * Handle PayNow webhook (for automatic receipt generation).
     */
    public function paynowWebhook(Request $request)
    {
        $payload = $request->all();
        
        // Validate PayNow webhook signature
        // Implementation depends on PayNow's webhook structure
        
        $reference = $payload['reference'] ?? null;
        $status = $payload['status'] ?? null;
        
        if ($reference && $status === 'paid') {
            $application = Application::where('reference', $reference)->first();
            
            if ($application && $application->payment) {
                // Auto-generate receipt for PayNow payments
                $existingReceipt = Receipt::where('application_id', $application->id)->first();
                
                if (!$existingReceipt) {
                    Receipt::create([
                        'application_id' => $application->id,
                        'applicant_id' => $application->applicant_id,
                        'payment_reference' => $reference,
                        'payment_method' => Receipt::PAYMENT_METHOD_PAYNOW,
                        'transaction_id' => $payload['paynow_reference'] ?? null,
                        'amount' => $application->payment->amount,
                        'payment_date' => now(),
                        'status' => Receipt::STATUS_VERIFIED,
                        'verified_by' => 1, // System user
                        'verified_at' => now(),
                        'generated_by' => 1, // System user
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
