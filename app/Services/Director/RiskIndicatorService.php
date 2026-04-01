<?php

namespace App\Services\Director;

use App\Models\Application;
use App\Models\Payment;
use App\Models\ActivityLog;
use Carbon\Carbon;

/**
 * Risk Indicator Service
 * 
 * Evaluates operational risk indicators using configurable thresholds and
 * provides color-coded risk levels (green/amber/red) for executive monitoring.
 * Monitors waivers, rejections, processing times, revenue, reprints, and more.
 * 
 * @package App\Services\Director
 */
class RiskIndicatorService
{
    /**
     * Get all risk indicators with color coding.
     * 
     * Returns comprehensive risk assessment across 7 key operational areas,
     * each with calculated risk level based on configured thresholds.
     * 
     * @return array Associative array containing risk indicators:
     *               - excessive_waivers: Waiver approval risk assessment
     *               - rejection_spike: Application rejection rate risk
     *               - processing_time_sla: Processing time SLA compliance risk
     *               - revenue_drop: Revenue trend risk assessment
     *               - reprint_frequency: Excessive reprint risk
     *               - category_reassignment: Category reassignment frequency risk
     *               - payment_delay: Payment verification delay risk
     */
    public function getAllRiskIndicators(): array
    {
        return [
            'excessive_waivers' => $this->evaluateExcessiveWaivers(),
            'rejection_spike' => $this->evaluateRejectionSpike(),
            'processing_time_sla' => $this->evaluateProcessingTimeSLA(),
            'revenue_drop' => $this->evaluateRevenueDrop(),
            'reprint_frequency' => $this->evaluateReprintFrequency(),
            'category_reassignment' => $this->evaluateCategoryReassignment(),
            'payment_delay' => $this->evaluatePaymentDelay(),
        ];
    }

    /**
     * Evaluate excessive waivers risk.
     * 
     * Assesses risk based on number of waivers approved in the current month
     * against configured thresholds (default: green ≤5, amber 6-10, red ≥11).
     * 
     * @return array Risk indicator data containing:
     *               - title: Indicator name
     *               - status: Risk level (green/amber/red)
     *               - level: Risk level (duplicate for compatibility)
     *               - value: Current waiver count
     *               - description: Human-readable description
     *               - threshold: Configured threshold values
     */
    public function evaluateExcessiveWaivers(): array
    {
        $waiverCount = Application::where('waiver_status', 'approved')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        
        $thresholds = config('director-dashboard.risk_thresholds.excessive_waivers', [
            'green' => ['max' => 5],
            'amber' => ['min' => 6, 'max' => 10],
            'red' => ['min' => 11],
        ]);
        
        $level = $this->determineRiskLevel($waiverCount, $thresholds);
        
        return [
            'title' => 'Excessive Waivers',
            'status' => $level,
            'level' => $level,
            'value' => $waiverCount,
            'description' => "Waivers granted this month",
            'threshold' => $thresholds,
        ];
    }

    /**
     * Evaluate high rejection spike risk.
     * 
     * Assesses risk based on rejection rate percentage in the current month
     * against configured thresholds (default: green ≤10%, amber 11-20%, red ≥21%).
     * 
     * @return array Risk indicator data with rejection rate percentage
     */
    public function evaluateRejectionSpike(): array
    {
        $total = Application::whereIn('status', ['issued', 'officer_rejected', 'registrar_rejected'])
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        
        $rejected = Application::where('status', 'LIKE', '%rejected%')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        
        $rejectionRate = $total > 0 ? round(($rejected / $total) * 100, 1) : 0;
        
        $thresholds = config('director-dashboard.risk_thresholds.rejection_spike', [
            'green' => ['max' => 10],
            'amber' => ['min' => 11, 'max' => 20],
            'red' => ['min' => 21],
        ]);
        
        $level = $this->determineRiskLevel($rejectionRate, $thresholds);
        
        return [
            'title' => 'Rejection Rate',
            'status' => $level,
            'level' => $level,
            'value' => $rejectionRate . '%',
            'description' => "Current month rejection rate",
            'threshold' => $thresholds,
        ];
    }

    /**
     * Evaluate processing time SLA risk.
     * 
     * Assesses risk based on average processing time in days for the past month
     * against configured thresholds (default: green ≤5 days, amber 6-10 days, red ≥11 days).
     * 
     * @return array Risk indicator data with average processing time in days
     */
    public function evaluateProcessingTimeSLA(): array
    {
        $apps = Application::whereNotNull('submitted_at')
            ->whereNotNull('issued_at')
            ->where('issued_at', '>=', now()->subMonth())
            ->select('submitted_at', 'issued_at')
            ->get();
        
        $avgDays = 0;
        if ($apps->isNotEmpty()) {
            $totalHours = 0;
            foreach ($apps as $app) {
                $totalHours += Carbon::parse($app->submitted_at)
                    ->diffInHours(Carbon::parse($app->issued_at));
            }
            $avgDays = round($totalHours / $apps->count() / 24, 1);
        }
        
        $thresholds = config('director-dashboard.risk_thresholds.processing_time_sla', [
            'green' => ['max' => 5],
            'amber' => ['min' => 6, 'max' => 10],
            'red' => ['min' => 11],
        ]);
        
        $level = $this->determineRiskLevel($avgDays, $thresholds);
        
        return [
            'title' => 'Processing Time SLA',
            'status' => $level,
            'level' => $level,
            'value' => $avgDays . ' days',
            'description' => "Average processing time",
            'threshold' => $thresholds,
        ];
    }

    /**
     * Evaluate revenue drop risk.
     * 
     * Assesses risk based on month-over-month revenue change percentage
     * against configured thresholds (default: green ≥-5%, amber -6% to -15%, red ≤-16%).
     * 
     * @return array Risk indicator data with percentage change value
     */
    public function evaluateRevenueDrop(): array
    {
        $currentMonth = Payment::where('status', 'paid')
            ->whereBetween('confirmed_at', [now()->startOfMonth(), now()])
            ->sum('amount');
        
        $previousMonth = Payment::where('status', 'paid')
            ->whereBetween('confirmed_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->sum('amount');
        
        $percentageChange = $previousMonth > 0 
            ? round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1) 
            : 0;
        
        $thresholds = config('director-dashboard.risk_thresholds.revenue_drop', [
            'green' => ['min' => -5],
            'amber' => ['min' => -15, 'max' => -6],
            'red' => ['max' => -16],
        ]);
        
        $level = $this->determineRiskLevel($percentageChange, $thresholds);
        
        return [
            'title' => 'Revenue Trend',
            'status' => $level,
            'level' => $level,
            'value' => $percentageChange . '%',
            'description' => "Month-over-month change",
            'threshold' => $thresholds,
        ];
    }

    /**
     * Evaluate reprint frequency risk.
     * 
     * Assesses risk based on count of applications with excessive reprints
     * against configured thresholds (default: green ≤2, amber 3-4, red ≥5).
     * 
     * @return array Risk indicator data with excessive reprint count
     */
    public function evaluateReprintFrequency(): array
    {
        $threshold = config('director-dashboard.excessive_print_threshold', 2);
        $excessiveReprints = Application::where('print_count', '>', $threshold)->count();
        
        $thresholds = config('director-dashboard.risk_thresholds.reprint_frequency', [
            'green' => ['max' => 2],
            'amber' => ['min' => 3, 'max' => 4],
            'red' => ['min' => 5],
        ]);
        
        $level = $this->determineRiskLevel($excessiveReprints, $thresholds);
        
        return [
            'title' => 'Excessive Reprints',
            'status' => $level,
            'level' => $level,
            'value' => $excessiveReprints,
            'description' => "Applications with excessive reprints",
            'threshold' => $thresholds,
        ];
    }

    /**
     * Evaluate category reassignment risk.
     * 
     * Assesses risk based on number of category reassignments in the current month
     * against configured thresholds (default: green ≤3, amber 4-7, red ≥8).
     * 
     * @return array Risk indicator data with reassignment count
     */
    public function evaluateCategoryReassignment(): array
    {
        $reassignments = ActivityLog::where('action', 'registrar_reassign_category')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        
        $thresholds = config('director-dashboard.risk_thresholds.category_reassignment', [
            'green' => ['max' => 3],
            'amber' => ['min' => 4, 'max' => 7],
            'red' => ['min' => 8],
        ]);
        
        $level = $this->determineRiskLevel($reassignments, $thresholds);
        
        return [
            'title' => 'Category Reassignments',
            'status' => $level,
            'level' => $level,
            'value' => $reassignments,
            'description' => "Reassignments this month",
            'threshold' => $thresholds,
        ];
    }

    /**
     * Evaluate payment verification delay risk.
     * 
     * Assesses risk based on count of payments pending for more than 3 days
     * against configured thresholds (default: green ≤2, amber 3-5, red ≥6).
     * 
     * @return array Risk indicator data with delayed payment count
     */
    public function evaluatePaymentDelay(): array
    {
        $delayedPayments = Payment::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(3))
            ->count();
        
        $thresholds = config('director-dashboard.risk_thresholds.payment_delay', [
            'green' => ['max' => 2],
            'amber' => ['min' => 3, 'max' => 5],
            'red' => ['min' => 6],
        ]);
        
        $level = $this->determineRiskLevel($delayedPayments, $thresholds);
        
        return [
            'title' => 'Payment Delays',
            'status' => $level,
            'level' => $level,
            'value' => $delayedPayments,
            'description' => "Payments pending > 3 days",
            'threshold' => $thresholds,
        ];
    }

    /**
     * Determine risk level based on value and thresholds.
     * 
     * Evaluates a numeric value against configured threshold ranges to
     * determine the appropriate risk level (green/amber/red).
     * 
     * @param float $value The value to evaluate
     * @param array $thresholds Threshold configuration with 'green', 'amber', 'red' keys,
     *                          each containing 'min' and/or 'max' values
     * @return string Risk level: 'green', 'amber', or 'red'
     */
    private function determineRiskLevel(float $value, array $thresholds): string
    {
        // Check red threshold
        if (isset($thresholds['red']['min']) && $value >= $thresholds['red']['min']) {
            return 'red';
        }
        if (isset($thresholds['red']['max']) && $value <= $thresholds['red']['max']) {
            return 'red';
        }
        
        // Check amber threshold
        if (isset($thresholds['amber']['min']) && isset($thresholds['amber']['max'])) {
            if ($value >= $thresholds['amber']['min'] && $value <= $thresholds['amber']['max']) {
                return 'amber';
            }
        }
        
        // Default to green
        return 'green';
    }

    /**
     * Get risk level color CSS class.
     * 
     * Converts risk level to Bootstrap-compatible CSS class for UI rendering.
     * 
     * @param string $level Risk level: 'green', 'amber', or 'red'
     * @return string CSS class: 'success', 'warning', 'danger', or 'secondary'
     */
    public function getRiskLevelColor(string $level): string
    {
        return match($level) {
            'green' => 'success',
            'amber' => 'warning',
            'red' => 'danger',
            default => 'secondary',
        };
    }
}
