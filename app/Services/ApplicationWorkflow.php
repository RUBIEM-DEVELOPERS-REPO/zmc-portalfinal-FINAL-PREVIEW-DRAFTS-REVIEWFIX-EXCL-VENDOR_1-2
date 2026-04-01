<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ApplicationWorkflow
{
    public static function transition(Application $app, string $toStatus, string $action, array $meta = []): void
    {
        $from = (string) $app->status;

        if (!self::allowed($from, $toStatus)) {
            throw ValidationException::withMessages([
                'status' => "Invalid transition from [$from] to [$toStatus].",
            ]);
        }

        $app->status = $toStatus;
        $app->current_stage = Application::stageForStatus($toStatus);
        $app->last_action_at = now();
        $app->last_action_by = Auth::id();

        // keep your existing decision timestamps in sync
        if (in_array($toStatus, [Application::OFFICER_APPROVED, Application::REGISTRAR_APPROVED], true)) {
            $app->approved_at = now();
            $app->rejected_at = null;
        }

        if (in_array($toStatus, [Application::OFFICER_REJECTED, Application::REGISTRAR_REJECTED], true)) {
            $app->rejected_at = now();
            $app->approved_at = null;
        }

        $app->save();

        ActivityLogger::log($action, $app, $from, $toStatus, $meta);
    }

    public static function allowed(string $from, string $to): bool
    {
        // Workflow: Officer → Registrar → Accounts → Production
        $map = [
            Application::SUBMITTED            => [Application::OFFICER_REVIEW],

            Application::OFFICER_REVIEW       => [
                Application::OFFICER_APPROVED,
                Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER, // NEW Master Flow
                Application::OFFICER_REJECTED,
                Application::CORRECTION_REQUESTED,
                Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL, // NEW: Forward without approval
            ],
            Application::CORRECTION_REQUESTED => [
                Application::OFFICER_REVIEW,
                Application::SUBMITTED_TO_ACCREDITATION_OFFICER,
            ],
            
            // NEW Master Flow
            Application::APPROVED_BY_OFFICER_AWAITING_PAYMENT_AND_REGISTRAR_MASTER => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::REGISTRAR_REVIEW,
                Application::REGISTRAR_APPROVED,
            ],
            Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL => [
                Application::REGISTRAR_REJECTED,
                Application::RETURNED_TO_OFFICER,
                Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR,
            ],

            // NEW: Media house two-stage payment path
            Application::SUBMITTED_WITH_APP_FEE => [
                Application::RETURNED_TO_APPLICANT,
                Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR,
            ],

            Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR => [
                Application::RETURNED_TO_OFFICER, // via fix request
                Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
            ],

            Application::REGISTRAR_APPROVED_PENDING_REG_FEE => [
                Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
                Application::REGISTRATION_FEE_AWAITING_VERIFICATION,
            ],

            Application::REGISTRATION_FEE_AWAITING_VERIFICATION => [
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
            ],

            Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION => [
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
            ],

            // NEW: Accounts review from Registrar (waiver path)
            Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR => [
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
            ],

            // Officer approved → Registrar Review
            Application::OFFICER_APPROVED     => [Application::REGISTRAR_REVIEW],

            // Registrar Review → Accounts (for payment) OR Return to AO
            Application::REGISTRAR_REVIEW     => [
                Application::REGISTRAR_APPROVED, 
                Application::REGISTRAR_REJECTED,
                Application::ACCOUNTS_REVIEW,    
                Application::RETURNED_TO_OFFICER,
                Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
                Application::REGISTRAR_RAISED_FIX_REQUEST,
            ],

            Application::REGISTRAR_RAISED_FIX_REQUEST => [
                Application::OFFICER_REVIEW,
                Application::SUBMITTED_TO_ACCREDITATION_OFFICER,
            ],

            // Accounts Review → Production (after payment) OR Return to AO/Registrar
            Application::ACCOUNTS_REVIEW      => [
                Application::PAID_CONFIRMED,
                Application::RETURNED_TO_OFFICER,
                Application::RETURNED_TO_ACCOUNTS, // Self-loop for corrections
            ],

            // Payment confirmed → Production
            Application::PAID_CONFIRMED       => [Application::PRODUCTION_QUEUE, Application::REGISTRAR_REVIEW],

            // NEW: Payment verified → Production
            Application::PAYMENT_VERIFIED     => [Application::PRODUCTION_QUEUE],

            // NEW: Payment rejected → Return to applicant
            Application::PAYMENT_REJECTED     => [
                Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION, // Resubmit
                Application::RETURNED_TO_APPLICANT,
            ],

            Application::RETURNED_TO_OFFICER  => [Application::OFFICER_REVIEW],
            Application::RETURNED_TO_ACCOUNTS => [Application::ACCOUNTS_REVIEW],

            // Registrar approved (Final) → Production
            Application::REGISTRAR_APPROVED   => [Application::PRODUCTION_QUEUE],

            Application::PRODUCTION_QUEUE     => [
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::ISSUED,
            ],
            Application::CARD_GENERATED       => [Application::CERT_GENERATED, Application::PRINTED, Application::ISSUED],
            Application::CERT_GENERATED       => [Application::PRINTED, Application::ISSUED],
            Application::PRINTED              => [Application::ISSUED],
        ];

        return in_array($to, $map[$from] ?? [], true);
    }
}
