<?php

namespace App\Services\Director;

use App\Repositories\Director\ApplicationRepository;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Accreditation Analytics Service
 * 
 * Provides trend analysis, processing time calculations, and approval ratio
 * statistics for accreditation applications. Supports drill-down functionality
 * and Chart.js data formatting.
 * 
 * @package App\Services\Director
 */
class AccreditationAnalyticsService
{
    /**
     * Create a new AccreditationAnalyticsService instance.
     * 
     * @param ApplicationRepository $applicationRepo Repository for application data queries
     */
    public function __construct(
        private ApplicationRepository $applicationRepo
    ) {}

    /**
     * Get monthly trends for submitted, approved, rejected applications.
     * 
     * Returns monthly aggregated counts for the specified number of months.
     * Results are cached for 2 hours to optimize performance.
     * 
     * @param int $months Number of months to retrieve (default: 12)
     * @return Collection Collection of monthly trend data with month, total_submitted,
     *                    total_approved, and total_rejected counts
     */
    public function getMonthlyTrends(int $months = 12, ?int $year = null): Collection
    {
        $cacheKey = "director.charts.monthly_trends_{$months}" . ($year ? "_{$year}" : "");
        return Cache::remember($cacheKey, 7200, function() use ($months, $year) {
            return $this->applicationRepo->getMonthlyApplicationCounts($months, $year);
        });
    }

    /**
     * Get average processing time by stage.
     * 
     * Calculates average hours spent in each processing stage (officer review,
     * registrar review, accounts verification) for applications from the last 3 months.
     * 
     * @return array Associative array with keys 'officer', 'registrar', 'accounts',
     *               each containing average hours as float (rounded to 1 decimal)
     */
    public function getProcessingTimeByStage(): array
    {
        // Get applications that went through each stage
        $officerApps = Application::whereNotNull('submitted_at')
            ->whereNotNull('assigned_at')
            ->where('created_at', '>=', now()->subMonths(3))
            ->select('submitted_at', 'assigned_at')
            ->get();
        
        $registrarApps = Application::whereNotNull('assigned_at')
            ->whereNotNull('registrar_approved_at')
            ->where('created_at', '>=', now()->subMonths(3))
            ->select('assigned_at', 'registrar_approved_at')
            ->get();
        
        $accountsApps = Application::whereNotNull('registrar_approved_at')
            ->whereNotNull('issued_at')
            ->where('created_at', '>=', now()->subMonths(3))
            ->select('registrar_approved_at', 'issued_at')
            ->get();
        
        return [
            'officer' => $this->calculateAverageHours($officerApps, 'submitted_at', 'assigned_at'),
            'registrar' => $this->calculateAverageHours($registrarApps, 'assigned_at', 'registrar_approved_at'),
            'accounts' => $this->calculateAverageHours($accountsApps, 'registrar_approved_at', 'issued_at'),
        ];
    }

    /**
     * Calculate average hours between two timestamps.
     * 
     * Helper method to compute average time difference across a collection of items.
     * 
     * @param Collection $items Collection of objects with timestamp fields
     * @param string $startField Name of the start timestamp field
     * @param string $endField Name of the end timestamp field
     * @return float Average hours between timestamps (rounded to 1 decimal, 0.0 if empty)
     */
    private function calculateAverageHours(Collection $items, string $startField, string $endField): float
    {
        if ($items->isEmpty()) {
            return 0.0;
        }
        
        $totalHours = 0;
        foreach ($items as $item) {
            $totalHours += Carbon::parse($item->$startField)
                ->diffInHours(Carbon::parse($item->$endField));
        }
        
        return round($totalHours / $items->count(), 1);
    }

    /**
     * Get approval-to-rejection ratio by category.
     * 
     * Calculates approval rates for each accreditation category, including
     * total applications, approved count, rejected count, and approval percentage.
     * 
     * @return Collection Collection of category statistics with accreditation_category_code,
     *                    total, approved, rejected, and approval_rate fields
     */
    public function getApprovalRatioByCategory(): Collection
    {
        return Application::select(
            'accreditation_category_code',
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) as approved"),
            DB::raw("SUM(CASE WHEN status LIKE '%rejected%' THEN 1 ELSE 0 END) as rejected")
        )
        ->whereIn('status', ['issued', 'officer_rejected', 'registrar_rejected'])
        ->whereNotNull('accreditation_category_code')
        ->groupBy('accreditation_category_code')
        ->get()
        ->map(function($item) {
            $item->approval_rate = $item->total > 0 
                ? round(($item->approved / $item->total) * 100, 1) 
                : 0;
            return $item;
        });
    }

    /**
     * Get approval-to-rejection ratio by application type.
     * 
     * Calculates approval rates for each application type (registration, accreditation, etc.),
     * returning a key-value map of application type to approval percentage.
     * 
     * @return Collection Collection keyed by application_type with approval rate percentages
     */
    public function getApprovalRatioByApplicationType(): Collection
    {
        return Application::select(
            'application_type',
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) as approved"),
            DB::raw("SUM(CASE WHEN status LIKE '%rejected%' THEN 1 ELSE 0 END) as rejected")
        )
        ->whereIn('status', ['issued', 'officer_rejected', 'registrar_rejected'])
        ->groupBy('application_type')
        ->get()
        ->mapWithKeys(function($item) {
            $rate = $item->total > 0 
                ? round(($item->approved / $item->total) * 100, 1) 
                : 0;
            return [$item->application_type => $rate];
        });
    }

    /**
     * Get approval-to-rejection ratio by residency type
     */

    /**
     * Get category distribution with trends.
     * 
     * Returns the distribution of applications across accreditation categories,
     * including counts, percentages, and category names.
     * 
     * @return Collection Collection of category distribution data with accreditation_category_code,
     *                    count, percentage, and category_name fields
     */
    public function getCategoryDistribution(): Collection
    {
        $total = Application::whereNotNull('accreditation_category_code')->count();
        
        return Application::select(
            'accreditation_category_code',
            DB::raw('COUNT(*) as count')
        )
        ->whereNotNull('accreditation_category_code')
        ->groupBy('accreditation_category_code')
        ->get()
        ->map(function($item) use ($total) {
            $item->percentage = $total > 0 
                ? round(($item->count / $total) * 100, 1) 
                : 0;
            $item->category_name = $this->getCategoryName($item->accreditation_category_code);
            return $item;
        });
    }

    /**
     * Get category name from code.
     * 
     * Translates accreditation category codes to human-readable names.
     * 
     * @param string $code Accreditation category code
     * @return string Human-readable category name (returns code if not found)
     */
    private function getCategoryName(string $code): string
    {
        $categories = Application::accreditationCategories();
        return $categories[$code] ?? $code;
    }

    /**
     * Get detailed applications for drill-down.
     * 
     * Retrieves up to 100 applications matching the specified filters,
     * with related applicant and officer data for detailed analysis.
     * 
     * @param array $filters Associative array of filter criteria:
     *                       - status: Filter by application status
     *                       - category: Filter by accreditation category code
     *                       - residency: Filter by residency type
     *                       - date_from: Start date for created_at filter
     *                       - date_to: End date for created_at filter
     * @return Collection Collection of Application models with applicant and assignedOfficer relations
     */
    public function getDrillDownApplications(array $filters): Collection
    {
        $query = Application::with(['applicant:id,name,email', 'assignedOfficer:id,name']);
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['category'])) {
            $query->where('accreditation_category_code', $filters['category']);
        }
        
        if (isset($filters['residency'])) {
            $query->where('residency_type', $filters['residency']);
        }
        
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['date_from']),
                Carbon::parse($filters['date_to'])
            ]);
        }
        
        return $query->orderBy('created_at', 'desc')->limit(100)->get();
    }

    /**
     * Get chart data for monthly trends.
     * 
     * Formats monthly trend data for Chart.js line chart rendering,
     * with separate datasets for submitted, approved, and rejected applications.
     * 
     * @return array Chart.js compatible format with 'months', 'submitted', 'approved',
     *               and 'rejected' arrays
     */
    public function getMonthlyTrendsChartData(): array
    {
        $trends = $this->getMonthlyTrends(12);
        
        return [
            'months' => $trends->pluck('month')->map(function($month) {
                return Carbon::createFromFormat('Y-m', $month)->format('M Y');
            })->toArray(),
            'submitted' => $trends->pluck('total_submitted')->toArray(),
            'approved' => $trends->pluck('total_approved')->toArray(),
            'returned' => $trends->pluck('total_returned')->toArray(),
        ];
    }
}
