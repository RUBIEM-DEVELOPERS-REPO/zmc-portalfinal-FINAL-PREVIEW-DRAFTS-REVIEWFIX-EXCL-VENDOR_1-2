<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Log;

/**
 * StatusTransitionValidator
 * 
 * Enforces strict status transition rules for the ZMC workflow.
 * No status skipping allowed - must follow defined transitions.
 */
class StatusTransitionValidator
{
    /**
     * Valid status transitions map
     * Format: 'current_status' => ['allowed_next_status_1', 'allowed_next_status_2', ...]
     */
    protected static array $transitions = [
        // Initial submission
        Application::DRAFT => [
            Application::SUBMITTED_TO_ACCREDITATION_OFFICER,
            Application::WITHDRAWN,
        ],
        
        Application::SUBMITTED_TO_ACCREDITATION_OFFICER => [
            Application::APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT,
            Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER, // NEW Master Parallel Flow
            Application::CORRECTION_REQUESTED,
            Application::OFFICER_REJECTED,
            Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL, // Special/waiver cases
        ],
        
        // After officer approval - payment required (Legacy/Simple)
        Application::APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT => [
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::REGISTRAR_REVIEW, 
        ],

        // NEW: Master Parallel Flow (AO Approved -> Both Registrar & Accounts)
        Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER => [
            Application::AWAITING_ACCOUNTS_VERIFICATION, // When applicant submits payment/PoP
            Application::REGISTRAR_REVIEW,
            Application::REGISTRAR_APPROVED, // If they approve while in this state
        ],
        
        // Correction flow
        Application::CORRECTION_REQUESTED => [
            Application::SUBMITTED_TO_ACCREDITATION_OFFICER, // After applicant fixes
            Application::WITHDRAWN,
        ],
        
        // Registrar review (parallel with payment)
        Application::REGISTRAR_REVIEW => [
            Application::REGISTRAR_RAISED_FIX_REQUEST,
            Application::AWAITING_ACCOUNTS_VERIFICATION, 
            Application::REGISTRAR_APPROVED, 
            Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT, // For media house
        ],
        
        Application::REGISTRAR_RAISED_FIX_REQUEST => [
            Application::OFFICER_REVIEW, 
            Application::SUBMITTED_TO_ACCREDITATION_OFFICER,
            Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR, // Return for media house fix
        ],
        
        // Accounts verification
        Application::AWAITING_ACCOUNTS_VERIFICATION => [
            Application::PAYMENT_VERIFIED,
            Application::PAYMENT_REJECTED,
        ],
        
        Application::PAYMENT_REJECTED => [
            Application::AWAITING_ACCOUNTS_VERIFICATION, // Resubmit payment
            Application::WITHDRAWN,
        ],
        
        Application::PAYMENT_VERIFIED => [
            Application::PRODUCTION_QUEUE,
        ],
        
        // Special/waiver path
        Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL => [
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL,
            Application::RETURNED_TO_OFFICER,
        ],
        
        Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL => [
            Application::PAYMENT_VERIFIED,
            Application::PAYMENT_REJECTED,
        ],
        
        // Media house two-stage payment
        Application::SUBMITTED_WITH_APP_FEE => [
            Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR,
            Application::PAYMENT_REJECTED,
        ],
        
        Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR => [
            Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT,
            Application::REGISTRAR_RAISED_FIX_REQUEST,
        ],
        
        Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT => [
            Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
            Application::REGISTRATION_FEE_AWAITING_VERIFICATION, // Master status
        ],
        
        Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION => [
            Application::PAYMENT_VERIFIED,
            Application::PAYMENT_REJECTED,
        ],

        Application::REGISTRATION_FEE_AWAITING_VERIFICATION => [
            Application::PAYMENT_VERIFIED,
            Application::PAYMENT_REJECTED,
        ],
        
        // Production
        Application::PRODUCTION_QUEUE => [
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRODUCED_READY_FOR_COLLECTION,
        ],
        
        Application::CARD_GENERATED => [
            Application::PRINTED,
            Application::PRODUCED_READY_FOR_COLLECTION,
        ],
        
        Application::CERT_GENERATED => [
            Application::PRINTED,
            Application::PRODUCED_READY_FOR_COLLECTION,
        ],
        
        Application::PRINTED => [
            Application::PRODUCED_READY_FOR_COLLECTION,
        ],
        
        Application::PRODUCED_READY_FOR_COLLECTION => [
            Application::ISSUED,
        ],
        
        Application::ISSUED => [
            // Terminal state
        ],
        
        // Legacy statuses (backward compatibility)
        Application::SUBMITTED => [
            Application::SUBMITTED_TO_ACCREDITATION_OFFICER,
        ],
        
        Application::OFFICER_REVIEW => [
            Application::OFFICER_APPROVED,
            Application::CORRECTION_REQUESTED,
            Application::OFFICER_REJECTED,
        ],
        
        Application::OFFICER_APPROVED => [
            Application::APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT,
            Application::REGISTRAR_REVIEW,
        ],
        
        Application::ACCOUNTS_REVIEW => [
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::PAID_CONFIRMED,
        ],
        
        Application::PAID_CONFIRMED => [
            Application::PAYMENT_VERIFIED,
            Application::PRODUCTION_QUEUE,
        ],
    ];

    /**
     * Validate if a status transition is allowed
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @return bool
     */
    public static function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        // Same status is always allowed (idempotent updates)
        if ($currentStatus === $newStatus) {
            return true;
        }

        // Check if transition is defined
        if (!isset(self::$transitions[$currentStatus])) {
            Log::warning("Status transition validation: Unknown current status", [
                'current_status' => $currentStatus,
                'new_status' => $newStatus,
            ]);
            return false;
        }

        $allowedTransitions = self::$transitions[$currentStatus];
        
        return in_array($newStatus, $allowedTransitions, true);
    }

    /**
     * Validate and throw exception if transition is invalid
     *
     * @param Application $application
     * @param string $newStatus
     * @throws \InvalidArgumentException
     */
    public static function validateOrFail(Application $application, string $newStatus): void
    {
        $currentStatus = $application->status;
        
        if (!self::isValidTransition($currentStatus, $newStatus)) {
            $allowedTransitions = self::$transitions[$currentStatus] ?? [];
            
            throw new \InvalidArgumentException(
                "Invalid status transition from '{$currentStatus}' to '{$newStatus}'. " .
                "Allowed transitions: " . implode(', ', $allowedTransitions)
            );
        }
    }

    /**
     * Get allowed next statuses for current status
     *
     * @param string $currentStatus
     * @return array
     */
    public static function getAllowedTransitions(string $currentStatus): array
    {
        return self::$transitions[$currentStatus] ?? [];
    }

    /**
     * Check if status requires payment submission
     *
     * @param string $status
     * @return bool
     */
    public static function requiresPayment(string $status): bool
    {
        return in_array($status, [
            Application::APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT,
            Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER,
            Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT,
        ], true);
    }

    /**
     * Check if status is in accounts verification stage
     *
     * @param string $status
     * @return bool
     */
    public static function isAccountsStage(string $status): bool
    {
        return in_array($status, [
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::REGISTRATION_FEE_AWAITING_VERIFICATION,
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL,
            Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
            Application::ACCOUNTS_REVIEW,
        ], true);
    }

    /**
     * Check if status is in production stage
     *
     * @param string $status
     * @return bool
     */
    public static function isProductionStage(string $status): bool
    {
        return in_array($status, [
            Application::PRODUCTION_QUEUE,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::PRODUCED_READY_FOR_COLLECTION,
        ], true);
    }
}
