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

        if (in_array($toStatus, [Application::OFFICER_APPROVED, Application::APPROVED_AWAITING_PAYMENT, Application::REGISTRAR_APPROVED], true)) {
            $app->approved_at = now();
            $app->rejected_at = null;
        }

        if (in_array($toStatus, [Application::OFFICER_REJECTED, Application::REGISTRAR_REJECTED, Application::PAYMENT_REJECTED], true)) {
            $app->rejected_at = now();
            $app->approved_at = null;
        }

        $app->save();

        ActivityLogger::log($action, $app, $from, $toStatus, $meta);
    }

    public static function allowed(string $from, string $to): bool
    {
        $map = [
            // --- MEDIA PRACTITIONER NEW ACCREDITATION ---
            // Applicant submits → Officer review
            Application::SUBMITTED => [
                Application::OFFICER_REVIEW,
            ],

            // Officer reviews
            Application::OFFICER_REVIEW => [
                Application::APPROVED_AWAITING_PAYMENT,
                Application::OFFICER_APPROVED,
                Application::OFFICER_REJECTED,
                Application::CORRECTION_REQUESTED,
                Application::RETURNED_TO_APPLICANT,
                Application::FORWARDED_TO_REGISTRAR,
                Application::VERIFIED_BY_OFFICER,
            ],

            // Returned to applicant → resubmit
            Application::CORRECTION_REQUESTED => [Application::OFFICER_REVIEW, Application::SUBMITTED],
            Application::RETURNED_TO_APPLICANT => [Application::OFFICER_REVIEW, Application::SUBMITTED],

            // Officer approved → payment prompt for applicant, then Registrar + Accounts see it
            Application::APPROVED_AWAITING_PAYMENT => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::REGISTRAR_REVIEW,
            ],

            // Officer approved (legacy compat) → Registrar or Accounts
            Application::OFFICER_APPROVED => [
                Application::REGISTRAR_REVIEW,
                Application::ACCOUNTS_REVIEW,
                Application::APPROVED_AWAITING_PAYMENT,
            ],

            // Applicant submits payment ref/proof → Accounts verification
            Application::AWAITING_ACCOUNTS_VERIFICATION => [
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
                Application::RETURNED_TO_ACCOUNTS,
                Application::PAID_CONFIRMED,
            ],

            // Payment verified → Production
            Application::PAYMENT_VERIFIED => [
                Application::PRODUCTION_QUEUE,
            ],

            // Payment rejected → applicant resubmits
            Application::PAYMENT_REJECTED => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::APPROVED_AWAITING_PAYMENT,
            ],

            // --- WAIVER / SPECIAL CASE ---
            // Forwarded to Registrar without officer approval
            Application::FORWARDED_TO_REGISTRAR => [
                Application::REGISTRAR_REVIEW,
                Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
                Application::RETURNED_TO_OFFICER,
            ],

            // Registrar pushes waiver to Accounts
            Application::PENDING_ACCOUNTS_FROM_REGISTRAR => [
                Application::PAYMENT_VERIFIED,
                Application::PAYMENT_REJECTED,
                Application::PAID_CONFIRMED,
            ],

            // Registrar fix request → back to Officer
            Application::REGISTRAR_FIX_REQUEST => [
                Application::OFFICER_REVIEW,
                Application::RETURNED_TO_OFFICER,
            ],

            // Registrar review
            Application::REGISTRAR_REVIEW => [
                Application::REGISTRAR_APPROVED,
                Application::REGISTRAR_REJECTED,
                Application::REGISTRAR_FIX_REQUEST,
                Application::ACCOUNTS_REVIEW,
                Application::RETURNED_TO_OFFICER,
                Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
            ],

            // --- MEDIA HOUSE TWO-STAGE PAYMENT ---
            // Submitted with application fee
            Application::SUBMITTED_WITH_APP_FEE => [
                Application::OFFICER_REVIEW,
            ],

            // Officer verified → Registrar
            Application::VERIFIED_BY_OFFICER => [
                Application::REGISTRAR_REVIEW,
            ],

            // Registrar approved → pending registration fee
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE => [
                Application::AWAITING_ACCOUNTS_VERIFICATION,
                Application::PAID_CONFIRMED,
            ],

            // Registrar approved (final) → Production or Accounts
            Application::REGISTRAR_APPROVED => [
                Application::PRODUCTION_QUEUE,
                Application::ACCOUNTS_REVIEW,
            ],

            // --- LEGACY / ACCOUNTS ---
            Application::ACCOUNTS_REVIEW => [
                Application::PAID_CONFIRMED,
                Application::PAYMENT_VERIFIED,
                Application::RETURNED_TO_OFFICER,
                Application::RETURNED_TO_ACCOUNTS,
            ],

            Application::PAID_CONFIRMED => [
                Application::PRODUCTION_QUEUE,
                Application::REGISTRAR_REVIEW,
                Application::PAYMENT_VERIFIED,
            ],

            Application::RETURNED_TO_OFFICER => [Application::OFFICER_REVIEW],
            Application::RETURNED_TO_ACCOUNTS => [Application::ACCOUNTS_REVIEW, Application::AWAITING_ACCOUNTS_VERIFICATION],

            // --- PRODUCTION ---
            Application::PRODUCTION_QUEUE => [
                Application::CARD_GENERATED,
                Application::CERT_GENERATED,
                Application::PRINTED,
                Application::PRODUCED_READY,
                Application::ISSUED,
            ],
            Application::PRODUCED_READY => [Application::ISSUED],
            Application::CARD_GENERATED => [Application::CERT_GENERATED, Application::PRINTED, Application::ISSUED],
            Application::CERT_GENERATED => [Application::PRINTED, Application::ISSUED],
            Application::PRINTED => [Application::ISSUED],
        ];

        return in_array($to, $map[$from] ?? [], true);
    }

    public static function allStatuses(): array
    {
        return [
            Application::DRAFT,
            Application::SUBMITTED,
            Application::WITHDRAWN,
            Application::OFFICER_REVIEW,
            Application::OFFICER_APPROVED,
            Application::OFFICER_REJECTED,
            Application::CORRECTION_REQUESTED,
            Application::RETURNED_TO_APPLICANT,
            Application::APPROVED_AWAITING_PAYMENT,
            Application::FORWARDED_TO_REGISTRAR,
            Application::REGISTRAR_FIX_REQUEST,
            Application::REGISTRAR_REVIEW,
            Application::REGISTRAR_APPROVED,
            Application::REGISTRAR_REJECTED,
            Application::RETURNED_TO_OFFICER,
            Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
            Application::ACCOUNTS_REVIEW,
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::PAYMENT_VERIFIED,
            Application::PAYMENT_REJECTED,
            Application::PAID_CONFIRMED,
            Application::RETURNED_TO_ACCOUNTS,
            Application::SUBMITTED_WITH_APP_FEE,
            Application::VERIFIED_BY_OFFICER,
            Application::PRODUCTION_QUEUE,
            Application::PRODUCED_READY,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::ISSUED,
        ];
    }
}
