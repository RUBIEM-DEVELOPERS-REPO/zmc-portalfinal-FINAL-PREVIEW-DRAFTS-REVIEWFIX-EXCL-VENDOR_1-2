<?php

namespace App\Repositories\Director;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Payment Repository
 * 
 * Provides data access methods for payment and revenue queries with SQLite-compatible
 * implementations. Uses strftime() for date operations and standard aggregations.
 * 
 * @package App\Repositories\Director
 */
class PaymentRepository
{
    /**
     * Get payments in date range.
     * 
     * Retrieves payments where confirmed_at falls within the specified
     * date range, with optional status filtering.
     * 
     * @param Carbon $startDate Start of date range
     * @param Carbon $endDate End of date range
     * @param string|null $status Optional status filter (e.g., 'paid', 'pending')
     * @return Collection Collection of Payment models
     */
    public function getInRange(Carbon $startDate, Carbon $endDate, ?string $status = null): Collection
    {
        $query = Payment::whereBetween('confirmed_at', [$startDate, $endDate])
            ->whereNotNull('confirmed_at');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->get();
    }

    /**
     * Get revenue by service type.
     * 
     * Aggregates total revenue and transaction counts grouped by service type
     * (registration, accreditation, renewal, etc.) for paid payments.
     * 
     * SQLite Note: Uses standard SUM() and COUNT() aggregations which are
     * fully compatible with SQLite.
     * 
     * @param Carbon|null $startDate Optional start date for filtering
     * @param Carbon|null $endDate Optional end date for filtering
     * @return Collection Collection with service_type, total_revenue, and
     *                    transaction_count fields, ordered by revenue descending
     */
    public function getRevenueByServiceType(?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = Payment::select(
            'service_type',
            DB::raw('SUM(amount) as total_revenue'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->where('status', 'paid')
        ->whereNotNull('confirmed_at');
        
        if ($startDate && $endDate) {
            $query->whereBetween('confirmed_at', [$startDate, $endDate]);
        }
        
        return $query->groupBy('service_type')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    /**
     * Get revenue by applicant category.
     * 
     * Aggregates total revenue and transaction counts grouped by applicant
     * category (individual, organization, media house, etc.) for paid payments.
     * 
     * @return Collection Collection with applicant_category, total_revenue, and
     *                    transaction_count fields, ordered by revenue descending
     */
    public function getRevenueByApplicantCategory(): Collection
    {
        return Payment::select(
            'applicant_category',
            DB::raw('SUM(amount) as total_revenue'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->where('status', 'paid')
        ->whereNotNull('confirmed_at')
        ->whereNotNull('applicant_category')
        ->groupBy('applicant_category')
        ->orderBy('total_revenue', 'desc')
        ->get();
    }

    /**
     * Get revenue by payment method.
     * 
     * Aggregates total revenue and transaction counts grouped by payment method
     * (bank_transfer, mobile_money, waiver, etc.) for paid payments.
     * 
     * @return Collection Collection with method, total_revenue, and transaction_count
     *                    fields, ordered by revenue descending
     */
    public function getRevenueByPaymentMethod(): Collection
    {
        return Payment::select(
            'method',
            DB::raw('SUM(amount) as total_revenue'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->where('status', 'paid')
        ->whereNotNull('confirmed_at')
        ->whereNotNull('method')
        ->groupBy('method')
        ->orderBy('total_revenue', 'desc')
        ->get();
    }

    /**
     * Get monthly revenue trend (SQLite compatible using strftime).
     * 
     * Aggregates revenue totals and transaction counts by month for the
     * specified number of months.
     * 
     * SQLite Note: Uses strftime('%Y-%m', confirmed_at) for month extraction.
     * This is SQLite-specific; MySQL would use DATE_FORMAT(confirmed_at, '%Y-%m').
     * 
     * @param int $months Number of months to retrieve (default: 12)
     * @return Collection Collection with month, total_revenue, and transaction_count
     *                    fields, ordered by month descending
     */
    public function getMonthlyRevenueTrend(int $months = 12, ?int $year = null): Collection
    {
        $query = Payment::select(
            DB::raw("strftime('%Y-%m', confirmed_at) as month"),
            DB::raw('SUM(amount) as total_revenue'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->where('status', 'paid');

        if ($year) {
            $query->whereYear('confirmed_at', $year);
        } else {
            $startDate = now()->subMonths($months)->startOfMonth();
            $query->where('confirmed_at', '>=', $startDate);
        }

        return $query->whereNotNull('confirmed_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get outstanding payments with aging (SQLite compatible).
     * 
     * Categorizes pending payments into aging buckets based on how long
     * they have been outstanding: 0-30 days, 30-60 days, and 60+ days.
     * 
     * SQLite Note: Uses standard date comparison operators which work
     * consistently across SQLite and MySQL. Carbon handles date arithmetic.
     * 
     * @return array Associative array with keys:
     *               - '0_30': Count of payments pending 0-30 days
     *               - '30_60': Count of payments pending 30-60 days
     *               - '60_plus': Count of payments pending 60+ days
     */
    public function getOutstandingPaymentsAging(): array
    {
        $now = now();
        
        return [
            '0_30' => Payment::where('status', 'pending')
                ->where('created_at', '>=', $now->copy()->subDays(30))
                ->count(),
            '30_60' => Payment::where('status', 'pending')
                ->where('created_at', '<', $now->copy()->subDays(30))
                ->where('created_at', '>=', $now->copy()->subDays(60))
                ->count(),
            '60_plus' => Payment::where('status', 'pending')
                ->where('created_at', '<', $now->copy()->subDays(60))
                ->count(),
        ];
    }

    /**
     * Get revenue by residency type.
     * 
     * Aggregates total revenue and transaction counts grouped by residency
     * type (resident, non-resident, etc.) for paid payments.
     * 
     * @return Collection Collection with residency, total_revenue, and
     *                    transaction_count fields, ordered by revenue descending
     */
    public function getRevenueByResidency(): Collection
    {
        return Payment::select(
            'residency',
            DB::raw('SUM(amount) as total_revenue'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->where('status', 'paid')
        ->whereNotNull('confirmed_at')
        ->whereNotNull('residency')
        ->groupBy('residency')
        ->orderBy('total_revenue', 'desc')
        ->get();
    }
}
