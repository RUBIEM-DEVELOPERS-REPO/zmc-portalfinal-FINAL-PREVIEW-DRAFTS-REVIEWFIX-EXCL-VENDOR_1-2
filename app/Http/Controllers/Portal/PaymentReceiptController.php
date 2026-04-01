<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class PaymentReceiptController extends Controller
{
    public function download(Application $application)
    {
        $user = Auth::user();

        if ((int)$application->applicant_user_id !== (int)$user->id) {
            abort(403);
        }

        // Receipt only available once payment is confirmed/verified
        $receiptStatuses = [
            Application::PAID_CONFIRMED,
            Application::PAYMENT_VERIFIED,
            Application::PRODUCTION_QUEUE,
            Application::PRODUCED_READY,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::ISSUED,
        ];

        // Also allow if proof was approved by accounts
        $proofApproved = $application->proof_status === 'approved';
        $paynowPaid    = $application->payment_status === 'paid';

        if (!in_array($application->status, $receiptStatuses, true) && !$proofApproved && !$paynowPaid) {
            return back()->with('error', 'Receipt is not yet available. Payment must be confirmed first.');
        }

        return view('portal.payment-receipt', compact('application'));
    }
}
