<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Paynow\Payments\Paynow;

class PaynowController extends Controller
{
    private function getPaynow(): Paynow
    {
        $integrationId = config('services.paynow.integration_id');
        $integrationKey = config('services.paynow.integration_key');
        $resultUrl = route('paynow.callback');
        $returnUrl = route('paynow.return');

        return new Paynow($integrationId, $integrationKey, $resultUrl, $returnUrl);
    }

    public function initiate(Request $request, Application $application)
    {
        $user = Auth::user();

        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!in_array($application->status, [
            Application::ACCOUNTS_REVIEW, 
            Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
            Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT
        ], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This application is not awaiting payment.'
            ], 400);
        }

        $fee = $this->calculateFee($application);

        $paynow = $this->getPaynow();
        $payment = $paynow->createPayment($application->reference, $user->email ?? 'applicant@zmc.org.zw');

        $description = $application->application_type === 'accreditation'
            ? 'Journalist Accreditation Fee'
            : 'Media House Registration Fee';

        $payment->add($description, $fee);

        $response = $paynow->send($payment);

        if ($response->success()) {
            $application->update([
                'paynow_reference' => $response->pollUrl(),
                'payment_status' => 'pending',
                'payment_submission_method' => 'paynow_reference',
                'payment_submitted_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => $response->redirectLink(),
                'poll_url' => $response->pollUrl(),
            ]);
        } else {
            Log::error('Paynow initiation failed', [
                'application_id' => $application->id,
                'errors' => $response->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed. Please try again.',
                'errors' => $response->errors(),
            ], 500);
        }
    }

    public function initiateMobile(Request $request, Application $application)
    {
        $user = Auth::user();

        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!in_array($application->status, [
            Application::ACCOUNTS_REVIEW, 
            Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
            Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT
        ], true)) {
            return response()->json([
                'success' => false,
                'message' => 'This application is not awaiting payment.'
            ], 400);
        }

        $validated = $request->validate([
            'phone' => 'required|string|regex:/^0[7][0-9]{8}$/',
            'method' => 'required|in:ecocash,onemoney',
        ]);

        $fee = $this->calculateFee($application);

        $paynow = $this->getPaynow();
        $payment = $paynow->createPayment($application->reference, $user->email ?? 'applicant@zmc.org.zw');

        $description = $application->application_type === 'accreditation'
            ? 'Journalist Accreditation Fee'
            : 'Media House Registration Fee';

        $payment->add($description, $fee);

        $response = $paynow->sendMobile($payment, $validated['phone'], $validated['method']);

        if ($response->success()) {
            $application->update([
                'paynow_reference' => $response->pollUrl(),
                'payment_status' => 'awaiting_confirmation',
                'payment_submission_method' => 'paynow_reference',
                'payment_submitted_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment request sent. Please approve on your phone.',
                'poll_url' => $response->pollUrl(),
            ]);
        } else {
            Log::error('Paynow mobile initiation failed', [
                'application_id' => $application->id,
                'errors' => $response->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Mobile payment failed. Please try again.',
                'errors' => $response->errors(),
            ], 500);
        }
    }

    public function checkStatus(Application $application)
    {
        $user = Auth::user();

        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$application->paynow_reference) {
            return response()->json([
                'success' => false,
                'message' => 'No payment initiated for this application.',
            ], 400);
        }

        if ($application->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'paid' => true,
                'message' => 'Payment already confirmed.',
            ]);
        }

        try {
            $paynow = $this->getPaynow();
            $status = $paynow->pollTransaction($application->paynow_reference);

            if ($status && $status->paid()) {
                $application->update([
                    'payment_status' => 'paid',
                    'payment_paid_at' => now(),
                    'status' => Application::PAID_CONFIRMED,
                    'last_action_at' => now(),
                ]);

                Log::info('Payment confirmed via polling', [
                    'application_id' => $application->id,
                    'reference' => $application->reference,
                ]);

                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'message' => 'Payment confirmed!',
                ]);
            }

            $statusText = $status ? $status->status() : 'unknown';

            return response()->json([
                'success' => true,
                'paid' => false,
                'status' => $statusText,
                'message' => 'Payment not yet confirmed. Status: ' . $statusText,
            ]);

        } catch (\Exception $e) {
            Log::error('Paynow polling failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'paid' => false,
                'message' => 'Could not check payment status. Please try again.',
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('Paynow callback received', $request->all());

        $reference = $request->input('reference');
        $status = strtolower($request->input('status', ''));
        $pollUrl = $request->input('pollurl');

        if (!$reference) {
            Log::warning('Paynow callback: Missing reference');
            return response('OK', 200);
        }

        $application = Application::where('reference', $reference)->first();

        if (!$application) {
            Log::warning('Paynow callback: Application not found', ['reference' => $reference]);
            return response('OK', 200);
        }

        if ($pollUrl && $application->paynow_reference !== $pollUrl) {
            Log::warning('Paynow callback: Poll URL mismatch', [
                'reference' => $reference,
                'expected_poll_url' => $application->paynow_reference,
                'received_poll_url' => $pollUrl,
            ]);
            return response('OK', 200);
        }

        if ($application->payment_status === 'paid') {
            Log::info('Paynow callback: Payment already confirmed', ['reference' => $reference]);
            return response('OK', 200);
        }

        if ($status === 'paid') {
            try {
                $paynow = $this->getPaynow();
                $pollResult = $paynow->pollTransaction($application->paynow_reference);

                if ($pollResult && $pollResult->paid()) {
                    $application->update([
                        'payment_status' => 'paid',
                        'payment_paid_at' => now(),
                        'status' => Application::PAID_CONFIRMED,
                        'last_action_at' => now(),
                    ]);

                    Log::info('Paynow payment confirmed (verified via poll)', [
                        'application_id' => $application->id,
                        'reference' => $reference,
                    ]);
                } else {
                    Log::warning('Paynow callback: Status mismatch on verification', [
                        'reference' => $reference,
                        'callback_status' => $status,
                        'poll_status' => $pollResult ? $pollResult->status() : 'null',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Paynow callback: Verification failed', [
                    'reference' => $reference,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response('OK', 200);
    }

    public function return(Request $request)
    {
        $pollUrl = $request->query('pollurl');

        if ($pollUrl) {
            $application = Application::where('paynow_reference', $pollUrl)->first();

            if ($application && $application->payment_status !== 'paid') {
                try {
                    $paynow = $this->getPaynow();
                    $status = $paynow->pollTransaction($pollUrl);

                    if ($status && $status->paid()) {
                        $application->update([
                            'payment_status' => 'paid',
                            'payment_paid_at' => now(),
                            'status' => Application::PAID_CONFIRMED,
                            'last_action_at' => now(),
                        ]);

                        $portalRoute = $application->application_type === 'accreditation'
                            ? 'accreditation.home'
                            : 'mediahouse.portal';

                        return redirect()->route($portalRoute)
                            ->with('success', 'Payment successful! Your application is now being processed.');
                    }

                    $portalRoute = $application->application_type === 'accreditation'
                        ? 'accreditation.home'
                        : 'mediahouse.portal';

                    return redirect()->route($portalRoute)
                        ->with('info', 'Payment processing. Status: ' . ($status ? $status->status() : 'pending'));

                } catch (\Exception $e) {
                    Log::error('Paynow return: Polling failed', [
                        'poll_url' => $pollUrl,
                        'error' => $e->getMessage(),
                    ]);
                }

                $portalRoute = $application->application_type === 'accreditation'
                    ? 'accreditation.home'
                    : 'mediahouse.portal';

                return redirect()->route($portalRoute)
                    ->with('info', 'Payment status could not be verified. Please check your dashboard.');
            }

            if ($application && $application->payment_status === 'paid') {
                $portalRoute = $application->application_type === 'accreditation'
                    ? 'accreditation.home'
                    : 'mediahouse.portal';

                return redirect()->route($portalRoute)
                    ->with('success', 'Payment already confirmed!');
            }
        }

        return redirect()->route('home');
    }

    private function calculateFee(Application $application): float
    {
        if ($application->application_type === 'accreditation') {
            $scope = $application->journalist_scope ?? 'local';
            $requestType = $application->request_type ?? 'new';

            if ($scope === 'foreign') {
                return $requestType === 'new' ? 150.00 : 100.00;
            }

            return $requestType === 'new' ? 50.00 : 30.00;
        }

        $requestType = $application->request_type ?? 'new';
        return $requestType === 'new' ? 500.00 : 300.00;
    }

    /**
     * Submit manual PayNow reference number
     */
    public function submitReference(Request $request, Application $application)
    {
        $user = Auth::user();

        if ($application->applicant_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reference' => 'required|string|max:100',
        ]);

        // Transition to accounts verification
        $newStatus = Application::AWAITING_ACCOUNTS_VERIFICATION;
        if ($application->application_type === 'registration' && $application->status === Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT) {
            $newStatus = Application::REGISTRATION_FEE_AWAITING_VERIFICATION;
        }

        $application->update([
            'status' => $newStatus,
            'paynow_reference' => $validated['reference'],
            'payment_status' => 'submitted_reference',
            'payment_submission_method' => 'paynow_manual_reference',
            'payment_submitted_at' => now(),
            'last_action_at' => now(),
        ]);

        Log::info('Manual PayNow reference submitted', [
            'application_id' => $application->id,
            'reference' => $validated['reference'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reference number submitted successfully. Awaiting accounts verification.',
        ]);
    }
}
