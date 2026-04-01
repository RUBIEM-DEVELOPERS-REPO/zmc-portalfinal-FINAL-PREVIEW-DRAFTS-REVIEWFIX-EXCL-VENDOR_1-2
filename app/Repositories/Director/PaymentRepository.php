<?php

namespace App\Repositories\Director;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentRepository
{
    /**
     * Get payments in date range
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string|null $status
     * @return Collection
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
     * Get revenue by service type
     * 
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return Collection
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
     * Get revenue by applicant category
     * 
     * @return Collection
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
     * Get revenue by payment method
     * 
     * @return Collection
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
     * Get monthly revenue trend
     * 
     * @param int $months
     * @return Collection
     */
    public function getMonthlyRevenueTrend(int $months = 12): Collection
    {
        $startDate = now()->subMonths($months)->startOfMonth();
        
        $monthFormat = DB::getDriverName() === 'pgsql' ? "TO_CHAR(confirmed_at, 'YYYY-MM')" : "strftime('%Y-%m', confirmed_at)";

        return Payment::select(
            DB::raw("$monthFormat as month"),
            DB::raw('SUM(amount) as total_revenue'),
            DB::raw('COUNT(*) as transaction_count')
        )
        ->where('status', 'paid')
        ->where('confirmed_at', '>=', $startDate)
        ->whereNotNull('confirmed_at')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();
    }

    /**
     * Get outstanding payments with aging
     * 
     * @return array
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
     * Get revenue by residency type
     * 
     * @return Collection
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
