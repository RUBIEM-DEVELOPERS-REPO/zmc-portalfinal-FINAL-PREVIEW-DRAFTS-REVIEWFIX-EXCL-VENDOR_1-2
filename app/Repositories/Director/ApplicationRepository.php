<?php

namespace App\Repositories\Director;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Application Repository
 * 
 * Provides data access methods for application queries with SQLite-compatible
 * implementations. Uses strftime() for date operations and PHP-based calculations
 * for complex aggregations.
 * 
 * @package App\Repositories\Director
 */
class ApplicationRepository
{
    /**
     * Get applications by status.
     * 
     * Retrieves applications matching a single status or array of statuses.
     * 
     * @param string|array $status Single status string or array of status values
     * @return Collection Collection of Application models
     */
    public function getByStatus($status): Collection
    {
        $query = Application::query();
        
        if (is_array($status)) {
            $query->whereIn('status', $status);
        } else {
            $query->where('status', $status);
        }
        
        return $query->get();
    }

    /**
     * Get applications submitted in date range.
     * 
     * Retrieves applications where submitted_at falls within the specified
     * date range (inclusive).
     * 
     * @param Carbon $startDate Start of date range
     * @param Carbon $endDate End of date range
     * @return Collection Collection of Application models
     */
    public function getSubmittedInRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Application::whereBetween('submitted_at', [$startDate, $endDate])
            ->whereNotNull('submitted_at')
            ->get();
    }

    /**
     * Get applications issued in date range.
     * 
     * Retrieves applications with status 'issued' where issued_at falls
     * within the specified date range (inclusive).
     * 
     * @param Carbon $startDate Start of date range
     * @param Carbon $endDate End of date range
     * @return Collection Collection of Application models
     */
    public function getIssuedInRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Application::whereBetween('issued_at', [$startDate, $endDate])
            ->whereNotNull('issued_at')
            ->where('status', 'issued')
            ->get();
    }

    /**
     * Get monthly application counts (SQLite compatible using strftime).
     * 
     * Aggregates application counts by month for the specified number of months,
     * including total submitted, approved, and rejected counts per month.
     * 
     * SQLite Note: Uses strftime('%Y-%m', created_at) for month extraction,
     * which is SQLite-specific. MySQL would use DATE_FORMAT().
     * 
     * @param int $months Number of months to retrieve (default: 12)
     * @return Collection Collection with month, total_submitted, total_approved,
     *                    and total_rejected fields, ordered by month descending
     */
    public function getMonthlyApplicationCounts(int $months = 12, ?int $year = null): Collection
    {
        $query = Application::select(
            DB::raw("strftime('%Y-%m', created_at) as month"),
            DB::raw("COUNT(*) as total_submitted"),
            DB::raw("SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) as total_approved"),
            DB::raw("SUM(CASE WHEN status LIKE '%rejected%' THEN 1 ELSE 0 END) as total_rejected")
        );

        if ($year) {
            $query->whereYear('created_at', $year);
        } else {
            $startDate = now()->subMonths($months)->startOfMonth();
            $query->where('created_at', '>=', $startDate);
        }

        return $query->whereNotNull('created_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get average processing time in hours (calculated in PHP for SQLite compatibility).
     * 
     * Calculates average hours between submission and issuance for completed
     * applications. Uses PHP-based calculation instead of database functions
     * for better SQLite compatibility.
     * 
     * SQLite Note: While SQLite supports julianday() for date arithmetic,
     * this implementation uses PHP's Carbon for consistency and precision.
     * 
     * @param string|null $stage Optional stage filter (currently unused, reserved for future)
     * @return float Average processing time in hours (0.0 if no applications)
     */
    public function getAverageProcessingTime(?string $stage = null): float
    {
        $query = Application::whereNotNull('submitted_at')
            ->whereNotNull('issued_at')
            ->where('status', 'issued');
        
        if ($stage) {
            // Filter by stage if needed
            $query->where('current_stage', $stage);
        }
        
        $applications = $query->select('submitted_at', 'issued_at')->get();
        
        if ($applications->isEmpty()) {
            return 0.0;
        }
        
        $totalHours = 0;
        foreach ($applications as $app) {
            $totalHours += Carbon::parse($app->submitted_at)
                ->diffInHours(Carbon::parse($app->issued_at));
        }
        
        return $totalHours / $applications->count();
    }

    /**
     * Get applications by category.
     * 
     * Aggregates application counts grouped by accreditation category code.
     * 
     * @return Collection Collection with accreditation_category_code and count fields
     */
    public function getByCategory(): Collection
    {
        return Application::select(
            'accreditation_category_code',
            DB::raw('COUNT(*) as count')
        )
        ->whereNotNull('accreditation_category_code')
        ->groupBy('accreditation_category_code')
        ->get();
    }

    /**
     * Get applications with excessive prints.
     * 
     * Retrieves applications where print_count exceeds the specified threshold,
     * with related applicant and print log data.
     * 
     * @param int $threshold Minimum print count to include (default: 1)
     * @return Collection Collection of Application models with applicant and printLogs relations,
     *                    ordered by print_count descending
     */
    public function getWithExcessivePrints(int $threshold = 1): Collection
    {
        return Application::where('print_count', '>', $threshold)
            ->with(['applicant:id,name,email', 'printLogs'])
            ->orderBy('print_count', 'desc')
            ->get();
    }

    /**
     * Get applications nearing expiry.
     * 
     * Retrieves issued applications with expiry dates within the specified
     * number of days from now, with related applicant data.
     * 
     * @param int $days Number of days ahead to check for expiry (default: 30)
     * @return Collection Collection of Application models with applicant relation,
     *                    ordered by expiry_date ascending
     */
    public function getNearingExpiry(int $days = 30): Collection
    {
        $expiryDate = now()->addDays($days);
        
        return Application::where('status', 'issued')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>=', now())
            ->with(['applicant:id,name,email'])
            ->orderBy('expiry_date', 'asc')
            ->get();
    }
}
