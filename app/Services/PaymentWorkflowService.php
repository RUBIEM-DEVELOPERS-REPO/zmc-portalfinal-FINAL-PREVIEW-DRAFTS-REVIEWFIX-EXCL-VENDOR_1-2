<?php

namespace App\Services;

use App\Models\Application;
use App\Models\PaymentSubmission;
use App\Services\StatusTransitionValidator;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * PaymentWorkflowService
 * 
 * Handles payment submission and verification workflow.
 * Enforces strict payment flow rules.
 */
class PaymentWorkflowService
{
    /**
     * Submit PayNow payment reference
     *
     * @param Application $application
     * @param string $paynowReference
     * @param string $paymentStage 'application_fee' or 'registration_fee'
     * @param array $data
     * @return Application
     */
    public static function submitPayNowPayment(
        Application $application,
        string $paynowReference,
        string $paymentStage = 'single',
        array $data = []
    ): Application {
        return DB::transaction(function () use ($application, $paynowReference, $paymentStage, $data) {
            $fromStatus = $application->status;
            
            // Determine target status based on payment stage
            if ($paymentStage === 'registration_fee') {
                $newStatus = Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION;
            } else {
                $newStatus = Application::AWAITING_ACCOUNTS_VERIFICATION;
            }
            
            // Validate transition
            StatusTransitionValidator::validateOrFail($application, $newStatus);
            
            // Create payment submission record
            $paymentSubmission = PaymentSubmission::create([
                'application_id' => $application->id,
                'payment_method' => 'paynow_reference',
                'payment_stage' => $paymentStage,
                'paynow_reference' => $paynowReference,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
                'status' => 'pending',
            ]);
            
            // Update application
            $application->update([
                'status' => $newStatus,
                'current_stage' => 'accounts_verification',
                'payment_submission_method' => 'paynow_reference',
                'payment_submitted_at' => now(),
                'paynow_reference' => $paynowReference,
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'payment_submitted_paynow',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => 'applicant',
                    'actor_user_id' => Auth::id(),
                    'paynow_reference' => $paynowReference,
                    'payment_stage' => $paymentStage,
                    'payment_submission_id' => $paymentSubmission->id,
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Submit payment proof upload
     *
     * @param Application $application
     * @param string $proofPath
     * @param string $paymentStage
     * @param array $proofData
     * @return Application
     */
    public static function submitProofPayment(
        Application $application,
        string $proofPath,
        string $paymentStage = 'single',
        array $proofData = []
    ): Application {
        return DB::transaction(function () use ($application, $proofPath, $paymentStage, $proofData) {
            $fromStatus = $application->status;
            
            // Determine target status
            if ($paymentStage === 'registration_fee') {
                $newStatus = Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION;
            } else {
                $newStatus = Application::AWAITING_ACCOUNTS_VERIFICATION;
            }
            
            // Validate transition
            StatusTransitionValidator::validateOrFail($application, $newStatus);
            
            // Create payment submission record
            $paymentSubmission = PaymentSubmission::create([
                'application_id' => $application->id,
                'payment_method' => 'proof_upload',
                'payment_stage' => $paymentStage,
                'proof_file_path' => $proofPath,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
                'status' => 'pending',
                'proof_data' => $proofData,
            ]);
            
            // Update application
            $application->update([
                'status' => $newStatus,
                'current_stage' => 'accounts_verification',
                'payment_submission_method' => 'proof_upload',
                'payment_submitted_at' => now(),
                'payment_proof_path' => $proofPath,
                'payment_proof_uploaded_at' => now(),
                'proof_status' => 'submitted',
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'payment_submitted_proof',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => 'applicant',
                    'actor_user_id' => Auth::id(),
                    'payment_stage' => $paymentStage,
                    'payment_submission_id' => $paymentSubmission->id,
                ], $proofData)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Submit waiver request
     *
     * @param Application $application
     * @param string $waiverPath
     * @param array $waiverData
     * @return Application
     */
    public static function submitWaiver(
        Application $application,
        string $waiverPath,
        array $waiverData = []
    ): Application {
        return DB::transaction(function () use ($application, $waiverPath, $waiverData) {
            $fromStatus = $application->status;
            
            // Waiver goes to special accounts review
            $newStatus = Application::AWAITING_ACCOUNTS_VERIFICATION;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail($application, $newStatus);
            
            // Create payment submission record
            $paymentSubmission = PaymentSubmission::create([
                'application_id' => $application->id,
                'payment_method' => 'waiver',
                'payment_stage' => 'single',
                'proof_file_path' => $waiverPath,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
                'status' => 'pending',
                'proof_data' => $waiverData,
            ]);
            
            // Update application
            $application->update([
                'status' => $newStatus,
                'current_stage' => 'accounts_verification',
                'payment_submission_method' => 'waiver',
                'payment_submitted_at' => now(),
                'waiver_path' => $waiverPath,
                'waiver_status' => 'submitted',
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'waiver_submitted',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => 'applicant',
                    'actor_user_id' => Auth::id(),
                    'payment_submission_id' => $paymentSubmission->id,
                ], $waiverData)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Verify payment (Accounts Officer)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function verifyPayment(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::PAYMENT_VERIFIED
            );
            
            // Update payment submission if provided
            if (isset($data['payment_submission_id'])) {
                $paymentSubmission = PaymentSubmission::find($data['payment_submission_id']);
                if ($paymentSubmission) {
                    $paymentSubmission->update([
                        'status' => 'verified',
                        'verified_at' => now(),
                        'verified_by' => Auth::id(),
                        'verification_notes' => $data['notes'] ?? null,
                    ]);
                }
            }
            
            // Update application
            $application->update([
                'status' => Application::PAYMENT_VERIFIED,
                'current_stage' => 'payment_verified',
                'payment_status' => 'paid',
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Update proof/waiver status if applicable
            if ($application->payment_submission_method === 'proof_upload') {
                $application->update([
                    'proof_status' => 'approved',
                    'proof_reviewed_at' => now(),
                    'proof_reviewed_by' => Auth::id(),
                    'proof_review_notes' => $data['notes'] ?? null,
                ]);
            } elseif ($application->payment_submission_method === 'waiver') {
                $application->update([
                    'waiver_status' => 'approved',
                    'waiver_reviewed_at' => now(),
                    'waiver_reviewed_by' => Auth::id(),
                    'waiver_review_notes' => $data['notes'] ?? null,
                ]);
            }
            
            // Log activity
            ActivityLogger::log(
                'payment_verified',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'accounts'),
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            // Automatically send to production
            return ApplicationWorkflowService::sendToProduction($application, $data);
        });
    }

    /**
     * Reject payment (Accounts Officer)
     *
     * @param Application $application
     * @param string $reason
     * @param array $data
     * @return Application
     */
    public static function rejectPayment(Application $application, string $reason, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $reason, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::PAYMENT_REJECTED
            );
            
            // Update payment submission if provided
            if (isset($data['payment_submission_id'])) {
                $paymentSubmission = PaymentSubmission::find($data['payment_submission_id']);
                if ($paymentSubmission) {
                    $paymentSubmission->update([
                        'status' => 'rejected',
                        'verified_at' => now(),
                        'verified_by' => Auth::id(),
                        'rejection_reason' => $reason,
                    ]);
                }
            }
            
            // Update application
            $application->update([
                'status' => Application::PAYMENT_REJECTED,
                'current_stage' => 'payment_rejected',
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Update proof/waiver status if applicable
            if ($application->payment_submission_method === 'proof_upload') {
                $application->update([
                    'proof_status' => 'rejected',
                    'proof_reviewed_at' => now(),
                    'proof_reviewed_by' => Auth::id(),
                    'proof_review_notes' => $reason,
                ]);
            } elseif ($application->payment_submission_method === 'waiver') {
                $application->update([
                    'waiver_status' => 'rejected',
                    'waiver_reviewed_at' => now(),
                    'waiver_reviewed_by' => Auth::id(),
                    'waiver_review_notes' => $reason,
                ]);
            }
            
            // Log activity
            ActivityLogger::log(
                'payment_rejected',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'accounts'),
                    'actor_user_id' => Auth::id(),
                    'reason' => $reason,
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Check if both payment stages are verified (for two-stage payment)
     *
     * @param Application $application
     * @return bool
     */
    public static function areBothPaymentStagesVerified(Application $application): bool
    {
        if (!$application->requiresApplicationFee()) {
            return false;
        }
        
        return $application->hasApplicationFeePaid() && $application->hasRegistrationFeePaid();
    }
}
