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

        if (in_array($toStatus, [
            Application::OFFICER_APPROVED,
            Application::APPROVED_AWAITING_PAYMENT,
            Application::REGISTRAR_APPROVED,
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
        ], true)) {
            $app->approved_at = now();
            $app->rejected_at = null;
        }

        if (in_array($toStatus, [
            Application::OFFICER_REJECTED,
            Application::REGISTRAR_REJECTED,
            Application::PAYMENT_REJECTED,
        ], true)) {
            $app->rejected_at = now();
            $app->approved_at = null;
        }

        $app->save();

        ActivityLogger::log($action, $app, $from, $toStatus, $meta);
    }

    public static function allowed(string $from, string $to): bool
    {
        $map = self::transitionMap();
        return in_array($to, $map[$from] ?? [], true);
    }

    public static function transitionMap(): array
    {
        return [
            // === JOURNALIST NEW APPLICATION FLOW ===
            // Applicant submits → Officer queue
            Application::DRAFT => [
                Application::SUBMITTED,
            ],
            Application::SUBMITTED => [
                Application::OFFICER_REVIEW,
            ],

            // Officer reviews → approve (awaiting payment), reject, return, or forward to registrar
            Application::OFFICER_REVIEW => [
                Application::APPROVED_AWAITING_PAYMENT,
                Application::OFFICER_REJECTED,
                Application::RETURNED_TO_APPLICANT,
                Application::FORWARDED_TO_REGISTRAR,
                Application::VERIFIED_BY_OFFICER,
                Application::CORRECTION_REQUESTED,
            ],

            // Returned to applicant → resubmit
            Application::RETURNED_TO_APPLICANT => [
                Application::SUBMITTED,
                Application::OFFICER_REVIEW,
                Application::WITHDRAWN,
            ],

            // Correction requested (legacy) → resubmit
            Application::CORRECTION_REQUESTED => [
                Application::SUBMITTED,
                Application::OFFICER_REVIEW,
            ],

            // Officer approved awaiting payment → applicant pays → awaiting verification
            Application::APPROVED_AWAITING_PAYMENT => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
            ],

            Application::AWAITING_ACCOUNTS_VERIFICATION => [
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
                Application::PAID_CONFIRMED,
                Application::SUBMITTED,
            ],

            // Payment verified → production
            Application::PAYMENT_VERIFIED => [
                Application::PRODUCTION_QUEUE,
            ],

            // Payment rejected → resubmit payment or return
            Application::PAYMENT_REJECTED => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::APPROVED_AWAITING_PAYMENT,
                Application::RETURNED_TO_APPLICANT,
            ],

            // === FORWARDED TO REGISTRAR (waiver/special cases) ===
            Application::FORWARDED_TO_REGISTRAR => [
                Application::REGISTRAR_REVIEW,
            ],

            Application::REGISTRAR_REVIEW => [
                Application::REGISTRAR_APPROVED,
                Application::REGISTRAR_REJECTED,
                Application::RETURNED_TO_OFFICER,
                Application::REGISTRAR_FIX_REQUEST,
                Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
                Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
                Application::ACCOUNTS_REVIEW,
                Application::RETURNED_TO_ACCOUNTS,
            ],

            // Registrar fix request → back to officer
            Application::REGISTRAR_FIX_REQUEST => [
                Application::OFFICER_REVIEW,
            ],

            // Registrar approved → production (or accounts if payment needed)
            Application::REGISTRAR_APPROVED => [
                Application::PRODUCTION_QUEUE,
                Application::AWAITING_ACCOUNTS_VERIFICATION,
            ],

            // Pending accounts from registrar (waiver path)
            Application::PENDING_ACCOUNTS_FROM_REGISTRAR => [
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
                Application::PAID_CONFIRMED,
                Application::RETURNED_TO_OFFICER,
            ],

            // Returned to officer → re-review
            Application::RETURNED_TO_OFFICER => [
                Application::OFFICER_REVIEW,
            ],

            // === MEDIA HOUSE TWO-STAGE PAYMENT FLOW ===
            // Media house pays app fee then submits
            Application::SUBMITTED_WITH_APP_FEE => [
                Application::VERIFIED_BY_OFFICER,
                Application::RETURNED_TO_APPLICANT,
                Application::OFFICER_REJECTED,
            ],

            // Verified by officer → registrar
            Application::VERIFIED_BY_OFFICER => [
                Application::REGISTRAR_REVIEW,
            ],

            // Registrar approved pending reg fee → applicant pays reg fee
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
            ],

            // === LEGACY ACCOUNTS FLOW ===
            Application::ACCOUNTS_REVIEW => [
                Application::PAID_CONFIRMED,
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
                Application::RETURNED_TO_OFFICER,
                Application::RETURNED_TO_ACCOUNTS,
            ],

            Application::PAID_CONFIRMED => [
                Application::PRODUCTION_QUEUE,
            ],

            Application::RETURNED_TO_ACCOUNTS => [
                Application::ACCOUNTS_REVIEW,
            ],

            // === LEGACY OFFICER APPROVED → REGISTRAR ===
            Application::OFFICER_APPROVED => [
                Application::REGISTRAR_REVIEW,
                Application::APPROVED_AWAITING_PAYMENT,
            ],

            Application::REGISTRAR_REJECTED => [
                Application::RETURNED_TO_OFFICER,
                Application::OFFICER_REVIEW,
            ],

            // === PRODUCTION ===
            Application::PRODUCTION_QUEUE => [
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
                Application::PRODUCED_READY,
                Application::PRINTED,
            ],
            Application::CARD_GENERATED => [
                Application::CERT_GENERATED,
                Application::PRODUCED_READY,
                Application::PRINTED,
            ],
            Application::CERT_GENERATED => [
                Application::CARD_GENERATED,
                Application::PRODUCED_READY,
                Application::PRINTED,
            ],
            Application::PRODUCED_READY => [
                Application::PRINTED,
                Application::ISSUED,
            ],
            Application::PRINTED => [
                Application::ISSUED,
            ],
        ];
    }

    public static function statusesForRole(string $role): array
    {
        return match($role) {
            'accreditation_officer' => [
                Application::SUBMITTED,
                Application::OFFICER_REVIEW,
                Application::RETURNED_TO_OFFICER,
                Application::REGISTRAR_FIX_REQUEST,
                Application::SUBMITTED_WITH_APP_FEE,
            ],
            'registrar' => [
                Application::FORWARDED_TO_REGISTRAR,
                Application::REGISTRAR_REVIEW,
                Application::VERIFIED_BY_OFFICER,
            ],
            'accounts' => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::ACCOUNTS_REVIEW,
                Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
            ],
            'production' => [
                Application::PRODUCTION_QUEUE,
                Application::PAYMENT_VERIFIED,
                Application::PAID_CONFIRMED,
            ],
            default => [],
        };
    }
}
