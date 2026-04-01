<?php

namespace App\Services\Director;

use App\Repositories\Director\ApplicationRepository;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccreditationAnalyticsService
{
    public function __construct(
        private ApplicationRepository $applicationRepo
    ) {}

    /**
     * Get monthly trends for submitted, approved, rejected applications
     * 
     * @param int $months Number of months to retrieve
     * @return Collection
     */
    public function getMonthlyTrends(int $months = 12): Collection
    {
        return Cache::remember("director.charts.monthly_trends_{$months}", 7200, function() use ($months) {
            return $this->applicationRepo->getMonthlyApplicationCounts($months);
        });
    }

    /**
     * Get average processing time by stage
     * 
     * @return array ['officer' => float, 'registrar' => float, 'accounts' => float]
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
     * Calculate average hours between two timestamps
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
     * Get approval-to-rejection ratio by category
     * 
     * @return Collection
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
     * Get approval-to-rejection ratio by application type
     * 
     * @return Collection
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
     * Get category distribution with trends
     * 
     * @return Collection
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
     * Get category name from code
     */
    private function getCategoryName(string $code): string
    {
        $categories = Application::accreditationCategories();
        return $categories[$code] ?? $code;
    }

    /**
     * Get detailed applications for drill-down
     * 
     * @param array $filters
     * @return Collection
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
     * Get chart data for monthly trends
     * 
     * @return array Chart.js compatible format
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
