<?php

namespace App\Repositories\Director;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApplicationRepository
{
    /**
     * Get applications by status
     * 
     * @param string|array $status
     * @return Collection
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
     * Get applications submitted in date range
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function getSubmittedInRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Application::whereBetween('submitted_at', [$startDate, $endDate])
            ->whereNotNull('submitted_at')
            ->get();
    }

    /**
     * Get applications issued in date range
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function getIssuedInRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Application::whereBetween('issued_at', [$startDate, $endDate])
            ->whereNotNull('issued_at')
            ->where('status', 'issued')
            ->get();
    }

    /**
     * Get monthly application counts
     * 
     * @param int $months
     * @return Collection
     */
    public function getMonthlyApplicationCounts(int $months = 12): Collection
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        
        $monthFormat = DB::getDriverName() === 'pgsql' ? "TO_CHAR(created_at, 'YYYY-MM')" : "strftime('%Y-%m', created_at)";

        return Application::select(
            DB::raw("$monthFormat as month"),
            DB::raw("COUNT(*) as total_submitted"),
            DB::raw("SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) as total_approved"),
            DB::raw("SUM(CASE WHEN status LIKE '%rejected%' THEN 1 ELSE 0 END) as total_returned")
        )
        ->where('created_at', '>=', $startDate)
        ->whereNotNull('created_at')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();
    }

    /**
     * Get average processing time in hours
     * 
     * @param string|null $stage
     * @return float
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
     * Get applications by category
     * 
     * @return Collection
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
     * Get applications with excessive prints
     * 
     * @param int $threshold
     * @return Collection
     */
    public function getWithExcessivePrints(int $threshold = 1): Collection
    {
        return Application::where('print_count', '>', $threshold)
            ->with(['applicant:id,name,email', 'printLogs'])
            ->orderBy('print_count', 'desc')
            ->get();
    }

    /**
     * Get applications nearing expiry
     * 
     * @param int $days
     * @return Collection
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
