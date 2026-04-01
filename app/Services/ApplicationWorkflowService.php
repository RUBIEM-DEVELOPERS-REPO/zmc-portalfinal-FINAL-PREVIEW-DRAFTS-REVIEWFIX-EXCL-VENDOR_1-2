<?php

namespace App\Services;

use App\Models\Application;
use App\Models\User;
use App\Services\StatusTransitionValidator;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * ApplicationWorkflowService
 * 
 * Handles application workflow transitions with strict validation.
 * All workflow actions must go through this service.
 */
class ApplicationWorkflowService
{
    /**
     * Submit application (from applicant)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function submitApplication(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::SUBMITTED_TO_ACCREDITATION_OFFICER
            );
            
            $application->update([
                'status' => Application::SUBMITTED_TO_ACCREDITATION_OFFICER,
                'current_stage' => 'accreditation_officer',
                'submitted_at' => now(),
                'is_draft' => false,
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'application_submitted',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => 'applicant',
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Approve application (Accreditation Officer)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function approveApplication(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Master Flow transition: AO Approve -> Parallel Registrar & Accounts
            $newStatus = Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER;
            $stage = 'registrar_and_accounts_review';

            // Special case for media house registration (Two-stage payment)
            if ($application->application_type === 'registration' && $application->request_type === 'new') {
                $newStatus = Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR;
                $stage = 'registrar_review_mediahouse';
            }
            
            // Validate transition
            StatusTransitionValidator::validateOrFail($application, $newStatus);
            
            $application->update([
                'status' => $newStatus,
                'current_stage' => $stage,
                'approved_at' => now(),
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
                'assigned_officer_id' => Auth::id(),
                'decision_notes' => $data['notes'] ?? null,
            ]);
            
            // Make visible to Registrar and Accounts
            // (handled by queries filtering on status)
            
            // Log activity
            ActivityLogger::log(
                'officer_approved',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'accreditation_officer'),
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Return application to applicant (Accreditation Officer)
     *
     * @param Application $application
     * @param string $reason
     * @param array $data
     * @return Application
     */
    public static function returnToApplicant(Application $application, string $reason, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $reason, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::CORRECTION_REQUESTED
            );
            
            $application->update([
                'status' => Application::CORRECTION_REQUESTED,
                'current_stage' => 'applicant_correction',
                'correction_notes' => $reason,
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'officer_request_correction',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'accreditation_officer'),
                    'actor_user_id' => Auth::id(),
                    'reason' => $reason,
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Forward without approval (Special/Waiver cases)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function forwardWithoutApproval(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL
            );
            
            $application->update([
                'status' => Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL,
                'current_stage' => 'registrar_special_review',
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
                'decision_notes' => $data['notes'] ?? null,
            ]);
            
            // Log activity
            ActivityLogger::log(
                'officer_forward_without_approval',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'accreditation_officer'),
                    'actor_user_id' => Auth::id(),
                    'reason' => 'Special case / Waiver',
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Registrar raises fix request
     *
     * @param Application $application
     * @param string $reason
     * @param array $data
     * @return Application
     */
    public static function registrarRaiseFixRequest(Application $application, string $reason, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $reason, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::REGISTRAR_RAISED_FIX_REQUEST
            );
            
            $application->update([
                'status' => Application::REGISTRAR_RAISED_FIX_REQUEST,
                'current_stage' => 'officer_fix',
                'correction_notes' => $reason,
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'registrar_raise_fix_request',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'registrar'),
                    'actor_user_id' => Auth::id(),
                    'reason' => $reason,
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Registrar approves (Media House flow)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function registrarApprove(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Check if media house two-stage payment
            if ($application->application_type === 'registration') {
                $newStatus = Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT;
                $stage = 'registration_fee_payment';
            } else {
                // Journalist flow: Registrar can approve even if payment is pending (parallel)
                // but final production requires both. For simplicity, we move to a 'registrar_approved' state.
                $newStatus = Application::REGISTRAR_APPROVED;
                $stage = 'awaiting_final_verification';
            }
            
            // Validate transition
            StatusTransitionValidator::validateOrFail($application, $newStatus);
            
            $application->update([
                'status' => $newStatus,
                'current_stage' => $stage,
                'registrar_reviewed_at' => now(),
                'registrar_reviewed_by' => Auth::id(),
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'registrar_approved',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'registrar'),
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Registrar pushes special case to accounts
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function registrarPushToAccounts(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL
            );
            
            $application->update([
                'status' => Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL,
                'current_stage' => 'accounts_special_review',
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'registrar_push_to_accounts',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'registrar'),
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Send to production (after payment verified)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function sendToProduction(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::PRODUCTION_QUEUE
            );
            
            $application->update([
                'status' => Application::PRODUCTION_QUEUE,
                'current_stage' => 'production',
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'sent_to_production',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => 'system',
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Mark as produced (Production complete)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function markProduced(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::PRODUCED_READY_FOR_COLLECTION
            );
            
            $application->update([
                'status' => Application::PRODUCED_READY_FOR_COLLECTION,
                'current_stage' => 'ready_for_collection',
                'printed_at' => now(),
                'printed_by' => Auth::id(),
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'production_complete',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'accreditation_officer'),
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            return $application->fresh();
        });
    }

    /**
     * Mark as issued (Final state)
     *
     * @param Application $application
     * @param array $data
     * @return Application
     */
    public static function markIssued(Application $application, array $data = []): Application
    {
        return DB::transaction(function () use ($application, $data) {
            $fromStatus = $application->status;
            
            // Validate transition
            StatusTransitionValidator::validateOrFail(
                $application,
                Application::ISSUED
            );
            
            $application->update([
                'status' => Application::ISSUED,
                'current_stage' => 'issued',
                'issued_at' => now(),
                'issued_by' => Auth::id(),
                'last_action_at' => now(),
                'last_action_by' => Auth::id(),
            ]);
            
            // Log activity
            ActivityLogger::log(
                'application_issued',
                $application,
                $fromStatus,
                $application->status,
                array_merge([
                    'actor_role' => session('active_staff_role', 'accreditation_officer'),
                    'actor_user_id' => Auth::id(),
                ], $data)
            );
            
            return $application->fresh();
        });
    }
}
