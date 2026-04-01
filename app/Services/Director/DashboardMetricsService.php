<?php

namespace App\Services\Director;

use App\Repositories\Director\ApplicationRepository;
use App\Repositories\Director\PaymentRepository;
use App\Repositories\Director\ActivityLogRepository;
use App\Models\Application;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Dashboard Metrics Service
 * 
 * Provides executive-level KPI calculations for the Director/CEO dashboard.
 * All metrics are cached for 1 hour to optimize performance.
 * 
 * @package App\Services\Director
 */
class DashboardMetricsService
{
    /**
     * Create a new DashboardMetricsService instance.
     * 
     * @param ApplicationRepository $applicationRepo Repository for application data queries
     * @param PaymentRepository $paymentRepo Repository for payment data queries
     * @param ActivityLogRepository $activityLogRepo Repository for activity log queries
     */
    public function __construct(
        private ApplicationRepository $applicationRepo,
        private PaymentRepository $paymentRepo,
        private ActivityLogRepository $activityLogRepo
    ) {}

    /**
     * Get all top-level KPIs for executive overview.
     * 
     * Returns a comprehensive array of 11 key performance indicators including
     * accreditation counts, revenue metrics, pipeline status, and compliance flags.
     * Results are cached for 1 hour to optimize dashboard performance.
     * 
     * @return array Associative array containing:
     *               - total_active_accreditations: Current active accreditation count
     *               - issued_this_month: Accreditations issued in current month
     *               - issued_this_year: Accreditations issued year-to-date
     *               - revenue_mtd: Revenue collected month-to-date
     *               - revenue_ytd: Revenue collected year-to-date
     *               - outstanding_revenue: Total pending payment amount
     *               - applications_in_pipeline: Applications currently in review
     *               - avg_processing_time: Average processing time in days
     *               - approval_rate: Overall approval rate as percentage
     *               - compliance_flags_active: Count of active compliance flags
     *               - total_media_houses: Total registered media houses
     */
    public function getExecutiveKPIs(?int $year = null): array
    {
        $cacheKey = 'director.kpis.executive_overview' . ($year ? '_' . $year : '');
        return Cache::remember($cacheKey, 3600, function() use ($year) {
            return [
                'total_active_accreditations' => $this->getTotalActiveAccreditations($year),
                'issued_this_month' => $this->getIssuedThisMonth($year),
                'issued_this_year' => $this->getIssuedYearToDate($year),
                'revenue_mtd' => $this->getRevenueMTD($year),
                'revenue_ytd' => $this->getRevenueYTD($year),
                'outstanding_revenue' => $this->getOutstandingRevenue(), // Usually always current
                'applications_in_pipeline' => $this->getApplicationsInPipeline(), // Usually always current
                'avg_processing_time' => $this->getAverageProcessingTime(),
                'approval_rate' => $this->getApprovalRate($year),
                'compliance_flags_active' => $this->getActiveComplianceFlags($year),
                'total_media_houses' => $this->getTotalMediaHouses($year),
            ];
        });
    }

    /**
     * Get total active accreditations.
     * 
     * Counts all issued accreditations that are either non-expiring or
     * have not yet reached their expiry date.
     * 
     * @return int Count of active accreditations
     */
    public function getTotalActiveAccreditations(?int $year = null): int
    {
        $query = Application::where('status', 'issued');
        
        if ($year && $year < now()->year) {
            $endOfYear = Carbon::create($year, 12, 31, 23, 59, 59);
            $query->where('issued_at', '<=', $endOfYear)
                  ->where(function($q) use ($endOfYear) {
                      $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', $endOfYear->copy()->startOfYear());
                  });
        } else {
            $query->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            });
        }
        
        return $query->count();
    }

    /**
     * Get accreditations issued in current month.
     * 
     * Counts all accreditations with status 'issued' where the issued_at
     * timestamp falls within the current calendar month.
     * 
     * @return int Count of accreditations issued this month
     */
    public function getIssuedThisMonth(?int $year = null): int
    {
        $year = $year ?: now()->year;
        $month = now()->month;

        return Application::where('status', 'issued')
            ->whereYear('issued_at', $year)
            ->whereMonth('issued_at', $month)
            ->count();
    }

    /**
     * Get accreditations issued year-to-date.
     * 
     * Counts all accreditations with status 'issued' where the issued_at
     * timestamp falls between the start of the current year and now.
     * 
     * @return int Count of accreditations issued year-to-date
     */
    public function getIssuedYearToDate(?int $year = null): int
    {
        $year = $year ?: now()->year;
        
        return Application::where('status', 'issued')
            ->whereYear('issued_at', $year)
            ->count();
    }

    /**
     * Get revenue collected month-to-date.
     * 
     * Sums all payment amounts with status 'paid' where the confirmed_at
     * timestamp falls within the current calendar month.
     * 
     * @return float Total revenue collected this month (0.0 if no payments)
     */
    public function getRevenueMTD(?int $year = null): float
    {
        $year = $year ?: now()->year;
        $month = (int)now()->format('m');

        return Payment::where('status', 'paid')
            ->whereYear('confirmed_at', $year)
            ->whereMonth('confirmed_at', $month)
            ->sum('amount') ?? 0.0;
    }

    /**
     * Get revenue collected year-to-date.
     * 
     * Sums all payment amounts with status 'paid' where the confirmed_at
     * timestamp falls between the start of the current year and now.
     * 
     * @return float Total revenue collected year-to-date (0.0 if no payments)
     */
    public function getRevenueYTD(?int $year = null): float
    {
        $year = $year ?: now()->year;

        return Payment::where('status', 'paid')
            ->whereYear('confirmed_at', $year)
            ->sum('amount') ?? 0.0;
    }

    /**
     * Get outstanding revenue amount.
     * 
     * Sums all payment amounts with status 'pending', representing
     * revenue that has been invoiced but not yet collected.
     * 
     * @return float Total outstanding revenue (0.0 if no pending payments)
     */
    public function getOutstandingRevenue(): float
    {
        return Payment::where('status', 'pending')
            ->sum('amount') ?? 0.0;
    }

    /**
     * Get count of applications in pipeline.
     * 
     * Counts all applications currently in review stages (submitted,
     * officer_review, registrar_review, accounts_review).
     * 
     * @return int Count of applications currently being processed
     */
    public function getApplicationsInPipeline(): int
    {
        return Application::whereIn('status', [
            'submitted',
            'officer_review',
            'registrar_review',
            'accounts_review'
        ])->count();
    }

    /**
     * Get average processing time in days.
     * 
     * Calculates the average time from submission to issuance across
     * all completed applications. Converts hours to days for readability.
     * 
     * @return float Average processing time in days (rounded to 1 decimal)
     */
    public function getAverageProcessingTime(): float
    {
        $avgHours = $this->applicationRepo->getAverageProcessingTime();
        return round($avgHours / 24, 1); // Convert hours to days
    }

    /**
     * Get approval rate as percentage.
     * 
     * Calculates the percentage of applications that were approved (issued)
     * versus rejected. Only includes applications with final status.
     * 
     * @return float Approval rate as percentage (0.0-100.0, rounded to 1 decimal)
     */
    public function getApprovalRate(?int $year = null): float
    {
        $query = Application::whereIn('status', [
            'issued',
            'officer_rejected',
            'registrar_rejected'
        ]);

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $total = $query->count();
        
        if ($total === 0) {
            return 0.0;
        }
        
        $approvedQuery = Application::where('status', 'issued');
        if ($year) {
            $approvedQuery->whereYear('created_at', $year);
        }
        $approved = $approvedQuery->count();
        
        return round(($approved / $total) * 100, 1);
    }

    /**
     * Get count of active compliance flags.
     * 
     * Counts high-risk actions logged since the start of the current month,
     * indicating potential compliance issues requiring attention.
     * 
     * @return int Count of active compliance flags
     */
    public function getActiveComplianceFlags(?int $year = null): int
    {
        $startDate = $year ? Carbon::create($year, 1, 1, 0, 0, 0) : now()->startOfMonth();
        $highRiskActions = $this->activityLogRepo->getHighRiskActions($startDate);
        
        if ($year) {
            $highRiskActions = $highRiskActions->filter(function($log) use ($year) {
                return Carbon::parse($log->created_at)->year == $year;
            });
        }
        
        return $highRiskActions->count();
    }

    /**
     * Get total registered media houses.
     * 
     * Counts all registration-type applications with status 'issued',
     * representing active media house registrations.
     * 
     * @return int Count of registered media houses
     */
    public function getTotalMediaHouses(?int $year = null): int
    {
        $query = Application::where('application_type', 'registration')
            ->where('status', 'issued');
            
        if ($year) {
            $query->whereYear('issued_at', $year);
        }

        return $query->count();
    }
}
