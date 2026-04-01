<?php

namespace App\Services\Director;

use App\Repositories\Director\PaymentRepository;
use App\Models\Payment;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FinancialAnalyticsService
{
    public function __construct(
        private PaymentRepository $paymentRepo
    ) {}

    /**
     * Get monthly revenue trend with year-over-year comparison
     * 
     * @param int $months
     * @return array
     */
    public function getMonthlyRevenueTrend(int $months = 12): array
    {
        // Get all months we need
        $allMonths = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $allMonths->push(now()->subMonths($i)->format('Y-m'));
        }
        
        // Get current year data
        $currentYearData = $this->paymentRepo->getMonthlyRevenueTrend($months)->keyBy('month');
        
        // Fill in missing months with zeros
        $currentYearTrend = $allMonths->map(function($month) use ($currentYearData) {
            $data = $currentYearData->get($month);
            return (object)[
                'month' => $month,
                'total_revenue' => $data ? $data->total_revenue : 0,
                'transaction_count' => $data ? $data->transaction_count : 0,
            ];
        });
        
        // Get previous year data for comparison
        $previousYearStart = now()->subMonths($months)->subYear()->startOfMonth();
        $previousYearEnd = now()->subYear()->endOfMonth();
        
        $monthFormat = DB::getDriverName() === 'pgsql' ? "TO_CHAR(confirmed_at, 'YYYY-MM')" : "strftime('%Y-%m', confirmed_at)";

        $previousYearData = Payment::select(
            DB::raw("$monthFormat as month"),
            DB::raw('SUM(amount) as total_revenue')
        )
        ->where('status', 'paid')
        ->whereBetween('confirmed_at', [$previousYearStart, $previousYearEnd])
        ->whereNotNull('confirmed_at')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get()
        ->keyBy('month');
        
        // Fill in missing months for previous year
        $previousYearMonths = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $previousYearMonths->push(now()->subMonths($i)->subYear()->format('Y-m'));
        }
        
        $previousYearTrend = $previousYearMonths->map(function($month) use ($previousYearData) {
            $data = $previousYearData->get($month);
            return (object)[
                'month' => $month,
                'total_revenue' => $data ? $data->total_revenue : 0,
            ];
        });
        
        return [
            'current_year' => $currentYearTrend,
            'previous_year' => $previousYearTrend,
        ];
    }

    /**
     * Get revenue breakdown by service type
     * 
     * @return Collection
     */
    public function getRevenueByServiceType(): Collection
    {
        return Cache::remember('director.charts.revenue_by_service', 7200, function() {
            return $this->paymentRepo->getRevenueByServiceType(
                now()->startOfYear(),
                now()
            );
        });
    }

    /**
     * Get revenue breakdown by applicant type
     * 
     * @return Collection
     */
    public function getRevenueByApplicantType(): Collection
    {
        return $this->paymentRepo->getRevenueByApplicantCategory();
    }

    /**
     * Get revenue breakdown by residency type
     * 
     * @return Collection
     */
    public function getRevenueByResidency(): Collection
    {
        return $this->paymentRepo->getRevenueByResidency();
    }

    /**
     * Get revenue breakdown by payment method
     * 
     * @return Collection
     */
    public function getRevenueByPaymentMethod(): Collection
    {
        return $this->paymentRepo->getRevenueByPaymentMethod();
    }

    /**
     * Get waiver statistics
     * 
     * @return array ['count' => int, 'total_value' => float, 'by_approver' => Collection, 'by_category' => Collection]
     */
    public function getWaiverStatistics(): array
    {
        $waivers = Payment::where('method', 'waiver')
            ->where('status', 'paid')
            ->whereNotNull('confirmed_at')
            ->get();
        
        $byApprover = Application::where('waiver_status', 'approved')
            ->whereNotNull('waiver_approved_by')
            ->select(
                'waiver_approved_by',
                DB::raw('COUNT(*) as waiver_count')
            )
            ->groupBy('waiver_approved_by')
            ->with('waiverApprovedBy:id,name')
            ->get();
        
        $byCategory = Application::where('waiver_status', 'approved')
            ->whereNotNull('accreditation_category_code')
            ->select(
                'accreditation_category_code',
                DB::raw('COUNT(*) as waiver_count')
            )
            ->groupBy('accreditation_category_code')
            ->get();
        
        return [
            'count' => $waivers->count(),
            'total_value' => $waivers->sum('amount'),
            'by_approver' => $byApprover,
            'by_category' => $byCategory,
        ];
    }

    /**
     * Get outstanding payments with aging analysis
     * 
     * @return array ['0_30' => int, '30_60' => int, '60_plus' => int]
     */
    public function getOutstandingPaymentsAging(): array
    {
        return $this->paymentRepo->getOutstandingPaymentsAging();
    }

    /**
     * Get drill-down payment details
     * 
     * @param array $filters
     * @return Collection
     */
    public function getDrillDownPayments(array $filters): Collection
    {
        $query = Payment::with(['application', 'payer:id,name,email']);
        
        if (isset($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }
        
        if (isset($filters['payment_method'])) {
            $query->where('method', $filters['payment_method']);
        }
        
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('confirmed_at', [
                Carbon::parse($filters['date_from']),
                Carbon::parse($filters['date_to'])
            ]);
        }
        
        return $query->orderBy('confirmed_at', 'desc')->limit(100)->get();
    }
}
