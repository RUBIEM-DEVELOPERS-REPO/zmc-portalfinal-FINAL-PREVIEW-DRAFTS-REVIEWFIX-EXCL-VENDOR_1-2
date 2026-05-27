<?php

namespace App\Services\Director;

use App\Repositories\Director\PaymentRepository;
use App\Models\Payment;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Financial Analytics Service
 * 
 * Provides revenue analysis, payment breakdowns, waiver statistics, and
 * outstanding payment aging analysis. Supports year-over-year comparisons
 * and drill-down functionality.
 * 
 * @package App\Services\Director
 */
class FinancialAnalyticsService
{
    /**
     * Create a new FinancialAnalyticsService instance.
     * 
     * @param PaymentRepository $paymentRepo Repository for payment data queries
     */
    public function __construct(
        private PaymentRepository $paymentRepo
    ) {}

    /**
     * Get monthly revenue trend with year-over-year comparison.
     * 
     * Returns revenue data for the specified number of months, including
     * current year and previous year data for comparison. Missing months
     * are filled with zero values.
     * 
     * @param int $months Number of months to retrieve (default: 12)
     * @return array Associative array with 'current_year' and 'previous_year' keys,
     *               each containing collections of monthly revenue data
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
        
        $dateExpr = DB::getDriverName() === 'sqlite' 
            ? "strftime('%Y-%m', confirmed_at)" 
            : "TO_CHAR(confirmed_at, 'YYYY-MM')";

        $previousYearData = Payment::select(
            DB::raw("$dateExpr as month"),
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
     * Get revenue breakdown by service type.
     * 
     * Returns revenue totals and transaction counts grouped by service type
     * (registration, accreditation, renewal, etc.). Results are cached for 2 hours.
     * 
     * @return Collection Collection of service type revenue data with service_type,
     *                    total_revenue, and transaction_count fields
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
     * Get revenue breakdown by applicant type.
     * 
     * Returns revenue totals and transaction counts grouped by applicant category
     * (individual, organization, media house, etc.).
     * 
     * @return Collection Collection of applicant type revenue data with applicant_category,
     *                    total_revenue, and transaction_count fields
     */
    public function getRevenueByApplicantType(): Collection
    {
        return $this->paymentRepo->getRevenueByApplicantCategory();
    }

    /**
     * Get revenue breakdown by residency type.
     * 
     * Returns revenue totals and transaction counts grouped by residency
     * (resident, non-resident, etc.).
     * 
     * @return Collection Collection of residency revenue data with residency,
     *                    total_revenue, and transaction_count fields
     */
    public function getRevenueByResidency(): Collection
    {
        return $this->paymentRepo->getRevenueByResidency();
    }

    /**
     * Get revenue breakdown by payment method.
     * 
     * Returns revenue totals and transaction counts grouped by payment method
     * (bank_transfer, mobile_money, waiver, etc.).
     * 
     * @return Collection Collection of payment method revenue data with method,
     *                    total_revenue, and transaction_count fields
     */
    public function getRevenueByPaymentMethod(): Collection
    {
        return $this->paymentRepo->getRevenueByPaymentMethod();
    }

    /**
     * Get waiver statistics.
     * 
     * Returns comprehensive waiver data including total count, total value,
     * breakdowns by approver, and breakdowns by accreditation category.
     * 
     * @return array Associative array containing:
     *               - count: Total number of approved waivers
     *               - total_value: Sum of all waived amounts
     *               - by_approver: Collection of waivers grouped by approving staff
     *               - by_category: Collection of waivers grouped by accreditation category
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
     * Get outstanding payments with aging analysis.
     * 
     * Categorizes pending payments into aging buckets based on how long
     * they have been outstanding (0-30 days, 30-60 days, 60+ days).
     * 
     * @return array Associative array with keys '0_30', '30_60', '60_plus',
     *               each containing count of payments in that aging bucket
     */
    public function getOutstandingPaymentsAging(): array
    {
        return $this->paymentRepo->getOutstandingPaymentsAging();
    }

    /**
     * Get drill-down payment details.
     * 
     * Retrieves up to 100 payments matching the specified filters,
     * with related application and payer data for detailed analysis.
     * 
     * @param array $filters Associative array of filter criteria:
     *                       - service_type: Filter by service type
     *                       - payment_method: Filter by payment method
     *                       - date_from: Start date for confirmed_at filter
     *                       - date_to: End date for confirmed_at filter
     * @return Collection Collection of Payment models with application and payer relations
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
