<?php

namespace App\Observers;

use App\Models\Application;
use App\Notifications\ApplicationStatusChanged;
use App\Services\ApplicationAssignmentService;

class ApplicationObserver
{
    public function updated(Application $application): void
    {
        // Auto-assign officer when application is submitted
        if ($application->wasChanged('status') && $application->status === Application::SUBMITTED) {
            $assignmentService = new ApplicationAssignmentService();
            $officer = $assignmentService->assign($application);
            
            if ($officer) {
                // Log the assignment
                \Log::info('Application assigned to officer', [
                    'application_id' => $application->id,
                    'officer_id' => $officer->id,
                    'collection_region' => $application->collection_region
                ]);
            }
        }

        $applicant = $application->applicant;
        if (!$applicant) {
            return;
        }

        if ($application->wasChanged('status')) {
            $status = $application->status;

            $message = match ($status) {
                Application::SUBMITTED => 'Your application has been submitted successfully.',
                Application::OFFICER_REVIEW => 'Your application is now under review.',
                Application::CORRECTION_REQUESTED => 'Corrections are required for your application. Please check Notices.',
                Application::OFFICER_REJECTED, Application::REGISTRAR_REJECTED => 'Your application has been rejected. Please check the details and reason on the portal.',
                Application::REGISTRAR_APPROVED => 'Your application has been approved. Payment may be required depending on your application type.',
                Application::ACCOUNTS_REVIEW => 'Your application is being processed by Accounts/Payments.',
                Application::PAID_CONFIRMED => 'Your payment has been confirmed. Your application is moving to production.',
                Application::PRODUCTION_QUEUE => 'Your application is in the production queue.',
                Application::CARD_GENERATED => 'Your card has been generated and is ready for printing/collection.',
                Application::CERT_GENERATED => 'Your certificate has been generated and is ready for printing/collection.',
                Application::PRINTED => 'Your documents have been printed. Please await collection instructions.',
                Application::ISSUED => 'Your card/certificate has been issued and is ready for collection.',
                default => 'Your application status has been updated: ' . str_replace('_', ' ', $status),
            };

            $applicant->notify(new ApplicationStatusChanged($application, $message));
        }

        if ($application->wasChanged('payment_status')) {
            $paymentStatus = $application->payment_status;
            $message = match ($paymentStatus) {
                'requested' => 'Payment is now required for your application. Please proceed to Payments.',
                'paid' => 'We have received your payment. It will be verified shortly.',
                'uploaded_waiver' => 'Your waiver/proof has been uploaded and will be reviewed.',
                'rejected' => 'Your payment proof/waiver was rejected. Please check Payments for details.',
                default => 'Payment status updated: ' . $paymentStatus,
            };
            $applicant->notify(new ApplicationStatusChanged($application, $message));
        }
    }
}
