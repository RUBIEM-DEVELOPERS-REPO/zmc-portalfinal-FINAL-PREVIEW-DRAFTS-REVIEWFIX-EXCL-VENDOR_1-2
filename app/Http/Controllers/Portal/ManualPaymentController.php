<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ManualPaymentController extends Controller
{
    public function uploadProof(Request $request, Application $application)
    {
        // Must be owner
        if ((int)$application->applicant_user_id !== (int)Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $allowedStatuses = [
            Application::SUBMITTED,
            Application::ACCOUNTS_REVIEW,
            Application::APPROVED_AWAITING_PAYMENT,
            Application::PAYMENT_REJECTED,
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
        ];
        if (!in_array($application->status ?? '', $allowedStatuses, true)) {
            return response()->json(['ok' => false, 'message' => 'Payment proof can only be submitted when your application is ready for payment.'], 422);
        }

        $data = $request->validate([
            'proof_first_name' => ['required','string','max:100'],
            'proof_last_name'  => ['required','string','max:100'],
            'proof_payment_date' => ['required','date'],
            'proof_amount_paid'  => ['required','numeric','min:0'],
            'proof_bank_name'    => ['required','string','max:120'],
            'proof_file'         => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'supporting_docs'    => ['nullable','array'],
            'supporting_docs.*'  => ['file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);

        $file = $request->file('proof_file');
        $path = $file->store('payment_proofs', 'public');

        $abs = Storage::disk('public')->path($path);
        $hash = is_file($abs) ? hash_file('sha256', $abs) : null;

        $from = $application->status;
        $application->update([
            'payment_proof_path' => $path,
            'payment_proof_uploaded_at' => now(),
            'proof_status' => 'submitted',

            'proof_payer_first_name' => $data['proof_first_name'],
            'proof_payer_last_name'  => $data['proof_last_name'],
            'proof_payment_date'     => $data['proof_payment_date'],
            'proof_amount_paid'      => $data['proof_amount_paid'],
            'proof_bank_name'        => $data['proof_bank_name'],
            'proof_original_name'    => $file->getClientOriginalName(),
            'proof_mime'             => $file->getMimeType(),
            'proof_file_hash'        => $hash,

            'status'                 => Application::AWAITING_ACCOUNTS_VERIFICATION,
            'payment_status'         => $application->payment_status ?: 'pending',
            'last_action_at'         => now(),
            'last_action_by'         => Auth::id(),
        ]);

        // Supporting documents
        if ($request->hasFile('supporting_docs')) {
            foreach ($request->file('supporting_docs') as $sfile) {
                $spath = $sfile->store('payment_supporting', 'public');
                $application->documents()->create([
                    'doc_type' => 'payment_supporting',
                    'file_path' => $spath,
                    'original_name' => $sfile->getClientOriginalName(),
                    'status' => 'pending',
                ]);
            }
        }

        ActivityLogger::log('applicant_proof_uploaded', $application, $from, $application->status, [
            'actor_role' => 'public',
            'proof_payment_date' => $data['proof_payment_date'],
            'proof_amount_paid'  => (string)$data['proof_amount_paid'],
            'proof_bank_name'    => $data['proof_bank_name'],
            'proof_file_hash'    => $hash,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Payment proof submitted successfully. Accounts will verify it shortly.',
            'proof' => [
                'uploaded_at' => optional($application->payment_proof_uploaded_at)->toDateTimeString(),
                'amount' => $application->proof_amount_paid,
                'bank' => $application->proof_bank_name,
                'hash' => $application->proof_file_hash,
            ]
        ]);
    }

    public function uploadWaiver(Request $request, Application $application)
    {
        if ((int)$application->applicant_user_id !== (int)Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $allowedStatuses = [
            Application::ACCOUNTS_REVIEW,
            Application::APPROVED_AWAITING_PAYMENT,
            Application::PAYMENT_REJECTED,
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
        ];
        if (!in_array($application->status ?? '', $allowedStatuses, true)) {
            return response()->json(['ok' => false, 'message' => 'Waiver can only be submitted when your application is ready for payment.'], 422);
        }

        $data = $request->validate([
            'waiver_first_name' => ['required','string','max:100'],
            'waiver_last_name'  => ['required','string','max:100'],
            'waiver_offered_date' => ['required','date'],
            'waiver_offered_by'   => ['required','string','max:150'],
            'waiver_file'         => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'supporting_docs'    => ['nullable','array'],
            'supporting_docs.*'  => ['file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);

        $file = $request->file('waiver_file');
        $path = $file->store('waivers', 'public');

        $abs = Storage::disk('public')->path($path);
        $hash = is_file($abs) ? hash_file('sha256', $abs) : null;

        $from = $application->status;
        $application->update([
            'waiver_path' => $path,
            'waiver_status' => 'submitted',

            'waiver_beneficiary_first_name' => $data['waiver_first_name'],
            'waiver_beneficiary_last_name'  => $data['waiver_last_name'],
            'waiver_offered_date'           => $data['waiver_offered_date'],
            'waiver_offered_by_name'        => $data['waiver_offered_by'],
            'waiver_original_name'          => $file->getClientOriginalName(),
            'waiver_mime'                   => $file->getMimeType(),
            'waiver_file_hash'              => $hash,

            'status'                        => Application::AWAITING_ACCOUNTS_VERIFICATION,
            'payment_status'                => $application->payment_status ?: 'pending',
            'last_action_at'                => now(),
            'last_action_by'                => Auth::id(),
        ]);

        // Supporting documents
        if ($request->hasFile('supporting_docs')) {
            foreach ($request->file('supporting_docs') as $sfile) {
                $spath = $sfile->store('payment_supporting', 'public');
                $application->documents()->create([
                    'doc_type' => 'payment_supporting',
                    'file_path' => $spath,
                    'original_name' => $sfile->getClientOriginalName(),
                    'status' => 'pending',
                ]);
            }
        }

        ActivityLogger::log('applicant_waiver_uploaded', $application, $from, $application->status, [
            'actor_role' => 'public',
            'waiver_offered_date' => $data['waiver_offered_date'],
            'waiver_offered_by'   => $data['waiver_offered_by'],
            'waiver_file_hash'    => $hash,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Waiver submitted successfully. Accounts will review it shortly.',
            'waiver' => [
                'uploaded_at' => now()->toDateTimeString(),
                'hash' => $application->waiver_file_hash,
            ]
        ]);
    }

    public function submitReference(Request $request, Application $application)
    {
        if ((int)$application->applicant_user_id !== (int)Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $allowedStatuses = [
            Application::ACCOUNTS_REVIEW,
            Application::APPROVED_AWAITING_PAYMENT,
            Application::PAYMENT_REJECTED,
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
        ];
        if (!in_array($application->status ?? '', $allowedStatuses, true)) {
            return response()->json(['ok' => false, 'message' => 'PayNow reference can only be submitted when your application is ready for payment.'], 422);
        }

        $data = $request->validate([
            'paynow_reference' => ['required', 'string', 'max:100'],
        ]);

        $from = $application->status;
        $application->update([
            'paynow_ref_submitted' => $data['paynow_reference'],
            'status' => Application::AWAITING_ACCOUNTS_VERIFICATION,
            'payment_status' => $application->payment_status ?: 'pending',
            'last_action_at' => now(),
            'last_action_by' => Auth::id(),
        ]);

        ActivityLogger::log('applicant_paynow_ref_submitted', $application, $from, $application->status, [
            'actor_role' => 'public',
            'paynow_reference' => $data['paynow_reference'],
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'PayNow reference submitted successfully. Accounts will verify your payment shortly.',
        ]);
    }
}
