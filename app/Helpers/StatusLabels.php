<?php

namespace App\Helpers;

use App\Models\Application;

/**
 * StatusLabels Helper
 * 
 * Provides human-readable labels for application statuses.
 * Used in views to display status badges and labels.
 */
class StatusLabels
{
    /**
     * Get human-readable label for status
     *
     * @param string $status
     * @return string
     */
    public static function getLabel(string $status): string
    {
        $labels = [
            // Draft and submission
            Application::DRAFT => 'Draft',
            Application::SUBMITTED => 'Submitted',
            Application::SUBMITTED_TO_ACCREDITATION_OFFICER => 'Submitted to Officer',
            Application::WITHDRAWN => 'Withdrawn',
            
            // Officer stage
            Application::OFFICER_REVIEW => 'Under Officer Review',
            Application::OFFICER_APPROVED => 'Approved by Officer',
            Application::APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT => 'Approved - Awaiting Payment',
            Application::OFFICER_REJECTED => 'Rejected by Officer',
            Application::CORRECTION_REQUESTED => 'Correction Requested',
            
            // Registrar stage
            Application::REGISTRAR_REVIEW => 'Under Registrar Review',
            Application::REGISTRAR_APPROVED => 'Approved by Registrar',
            Application::REGISTRAR_REJECTED => 'Rejected by Registrar',
            Application::RETURNED_TO_OFFICER => 'Returned to Officer',
            Application::REGISTRAR_RAISED_FIX_REQUEST => 'Fix Request from Registrar',
            
            // Special/Waiver flow
            Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL => 'Forwarded to Registrar (Special Case)',
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR => 'Pending Accounts Review (Special)',
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL => 'Pending Accounts Review (Special)',
            
            // Payment stage
            Application::ACCOUNTS_REVIEW => 'Under Accounts Review',
            Application::AWAITING_ACCOUNTS_VERIFICATION => 'Awaiting Payment Verification',
            Application::PAID_CONFIRMED => 'Payment Confirmed',
            Application::PAYMENT_VERIFIED => 'Payment Verified',
            Application::PAYMENT_REJECTED => 'Payment Rejected',
            Application::RETURNED_TO_ACCOUNTS => 'Returned to Accounts',
            
            // Media house two-stage
            Application::SUBMITTED_WITH_APP_FEE => 'Submitted with Application Fee',
            Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR => 'Verified - Pending Registrar',
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE => 'Approved - Pending Registration Fee',
            Application::REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT => 'Approved - Pending Registration Fee',
            Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION => 'Registration Fee Submitted',
            
            // Production stage
            Application::PRODUCTION_QUEUE => 'In Production Queue',
            Application::CARD_GENERATED => 'Card Generated',
            Application::CERT_GENERATED => 'Certificate Generated',
            Application::PRINTED => 'Printed',
            Application::PRODUCED_READY_FOR_COLLECTION => 'Ready for Collection',
            Application::ISSUED => 'Issued',
        ];
        
        return $labels[$status] ?? ucwords(str_replace('_', ' ', $status));
    }

    /**
     * Get badge color class for status
     *
     * @param string $status
     * @return string
     */
    public static function getBadgeClass(string $status): string
    {
        $classes = [
            // Draft/Submitted - gray
            Application::DRAFT => 'bg-gray-100 text-gray-800',
            Application::SUBMITTED => 'bg-blue-100 text-blue-800',
            Application::SUBMITTED_TO_ACCREDITATION_OFFICER => 'bg-blue-100 text-blue-800',
            
            // Under review - yellow
            Application::OFFICER_REVIEW => 'bg-yellow-100 text-yellow-800',
            Application::REGISTRAR_REVIEW => 'bg-yellow-100 text-yellow-800',
            Application::ACCOUNTS_REVIEW => 'bg-yellow-100 text-yellow-800',
            Application::AWAITING_ACCOUNTS_VERIFICATION => 'bg-yellow-100 text-yellow-800',
            
            // Approved - green
            Application::OFFICER_APPROVED => 'bg-green-100 text-green-800',
            Application::APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT => 'bg-green-100 text-green-800',
            Application::REGISTRAR_APPROVED => 'bg-green-100 text-green-800',
            Application::PAID_CONFIRMED => 'bg-green-100 text-green-800',
            Application::PAYMENT_VERIFIED => 'bg-green-100 text-green-800',
            
            // Rejected/Returned - red
            Application::OFFICER_REJECTED => 'bg-red-100 text-red-800',
            Application::REGISTRAR_REJECTED => 'bg-red-100 text-red-800',
            Application::PAYMENT_REJECTED => 'bg-red-100 text-red-800',
            Application::WITHDRAWN => 'bg-red-100 text-red-800',
            
            // Correction/Fix - orange
            Application::CORRECTION_REQUESTED => 'bg-orange-100 text-orange-800',
            Application::RETURNED_TO_OFFICER => 'bg-orange-100 text-orange-800',
            Application::REGISTRAR_RAISED_FIX_REQUEST => 'bg-orange-100 text-orange-800',
            Application::RETURNED_TO_ACCOUNTS => 'bg-orange-100 text-orange-800',
            
            // Special cases - purple
            Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL => 'bg-purple-100 text-purple-800',
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR => 'bg-purple-100 text-purple-800',
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL => 'bg-purple-100 text-purple-800',
            
            // Production - indigo
            Application::PRODUCTION_QUEUE => 'bg-indigo-100 text-indigo-800',
            Application::CARD_GENERATED => 'bg-indigo-100 text-indigo-800',
            Application::CERT_GENERATED => 'bg-indigo-100 text-indigo-800',
            Application::PRINTED => 'bg-indigo-100 text-indigo-800',
            Application::PRODUCED_READY_FOR_COLLECTION => 'bg-indigo-100 text-indigo-800',
            
            // Issued - teal
            Application::ISSUED => 'bg-teal-100 text-teal-800',
        ];
        
        return $classes[$status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status stage (for grouping)
     *
     * @param string $status
     * @return string
     */
    public static function getStage(string $status): string
    {
        if (in_array($status, [
            Application::DRAFT,
            Application::SUBMITTED,
            Application::SUBMITTED_TO_ACCREDITATION_OFFICER,
        ])) {
            return 'submission';
        }
        
        if (in_array($status, [
            Application::OFFICER_REVIEW,
            Application::OFFICER_APPROVED,
            Application::APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT,
            Application::CORRECTION_REQUESTED,
        ])) {
            return 'officer';
        }
        
        if (in_array($status, [
            Application::REGISTRAR_REVIEW,
            Application::REGISTRAR_APPROVED,
            Application::REGISTRAR_RAISED_FIX_REQUEST,
            Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL,
        ])) {
            return 'registrar';
        }
        
        if (in_array($status, [
            Application::ACCOUNTS_REVIEW,
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::PAID_CONFIRMED,
            Application::PAYMENT_VERIFIED,
            Application::PAYMENT_REJECTED,
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL,
        ])) {
            return 'accounts';
        }
        
        if (in_array($status, [
            Application::PRODUCTION_QUEUE,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::PRODUCED_READY_FOR_COLLECTION,
        ])) {
            return 'production';
        }
        
        if ($status === Application::ISSUED) {
            return 'issued';
        }
        
        return 'other';
    }
}
