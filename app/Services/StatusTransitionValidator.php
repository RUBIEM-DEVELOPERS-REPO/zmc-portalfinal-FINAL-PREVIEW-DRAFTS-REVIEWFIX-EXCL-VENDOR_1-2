<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Log;

class StatusTransitionValidator
{
    public static function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        if ($currentStatus === $newStatus) {
            return true;
        }

        $map = ApplicationWorkflow::transitionMap();

        if (!isset($map[$currentStatus])) {
            Log::warning("Status transition validation: Unknown current status", [
                'current_status' => $currentStatus,
                'new_status' => $newStatus,
            ]);
            return false;
        }

        return in_array($newStatus, $map[$currentStatus], true);
    }

    public static function validateOrFail(Application $application, string $newStatus): void
    {
        $currentStatus = $application->status;

        if (!self::isValidTransition($currentStatus, $newStatus)) {
            $map = ApplicationWorkflow::transitionMap();
            $allowedTransitions = $map[$currentStatus] ?? [];

            throw new \InvalidArgumentException(
                "Invalid status transition from '{$currentStatus}' to '{$newStatus}'. " .
                "Allowed transitions: " . implode(', ', $allowedTransitions)
            );
        }
    }

    public static function getAllowedTransitions(string $currentStatus): array
    {
        $map = ApplicationWorkflow::transitionMap();
        return $map[$currentStatus] ?? [];
    }

    public static function requiresPayment(string $status): bool
    {
        return in_array($status, [
            Application::APPROVED_AWAITING_PAYMENT,
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
        ], true);
    }

    public static function isAccountsStage(string $status): bool
    {
        return in_array($status, [
            Application::AWAITING_ACCOUNTS_VERIFICATION,
            Application::ACCOUNTS_REVIEW,
            Application::PENDING_ACCOUNTS_FROM_REGISTRAR,
        ], true);
    }

    public static function isProductionStage(string $status): bool
    {
        return in_array($status, [
            Application::PRODUCTION_QUEUE,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::PRODUCED_READY,
        ], true);
    }
}
