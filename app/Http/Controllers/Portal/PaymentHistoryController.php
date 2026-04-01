<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Application;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get all payments for the user's applications
        $payments = Payment::with(['application', 'payer'])
            ->whereHas('application', function ($query) use ($user) {
                $query->where('applicant_user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('portal.payments.history', compact('payments'));
    }

    public function showReceipt(Payment $payment)
    {
        // Check if the payment belongs to the authenticated user
        if ($payment->payer_user_id !== auth()->id() && 
            $payment->application->applicant_user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        $payment->load(['application.applicant', 'application.batch']);

        $data = [
            'application' => $payment->application,
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

        $pdf = Pdf::loadView('portal.payments.receipt_pdf', $data);
        return $pdf->download('Receipt_' . $payment->application->reference . '.pdf');
    }
}
