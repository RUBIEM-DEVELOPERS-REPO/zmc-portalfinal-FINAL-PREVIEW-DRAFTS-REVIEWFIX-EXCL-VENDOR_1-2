<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PortalReceiptController extends Controller
{
    public function download(Payment $payment)
    {
        if ($payment->payer_user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($payment->status !== 'paid' || !$payment->receipt_number) {
            return back()->with('error', 'Receipt not available for this payment.');
        }

        $payment->load(['application.applicant']);
        $application = $payment->application;

        $methodLabels = [
            'paynow_reference' => 'PayNow',
            'paynow' => 'PayNow',
            'proof' => 'Proof of Payment',
            'proof_upload' => 'Proof of Payment',
            'waiver' => 'Fee Waiver',
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'general' => 'Other',
        ];

        $data = [
            'application' => $application,
            'payment' => $payment,
            'receipt_number' => $payment->receipt_number,
            'amount' => $payment->amount,
            'payment_method' => $payment->method ?? 'N/A',
            'payment_date' => $payment->confirmed_at ?? $payment->created_at,
            'date' => now()->format('Y-m-d H:i'),
            'company_name' => 'Zimbabwe Media Commission',
            'company_address' => '108 Swan Drive, Alexandra Park, Harare',
            'company_email' => 'zmcaccreditation@gmail.com',
            'company_phone' => '253509/10 or 253572/5/6',
        ];

        $pdf = Pdf::loadView('staff.accounts.receipt_pdf', $data);
        return $pdf->download('Receipt_' . $payment->receipt_number . '.pdf');
    }
}
