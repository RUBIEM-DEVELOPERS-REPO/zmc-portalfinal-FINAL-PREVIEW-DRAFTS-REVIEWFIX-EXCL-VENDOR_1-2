<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $availableYears = range(2024, now()->year);
        rsort($availableYears);

        $isCurrentYear = ($year == now()->year);
        $yearStart = \Carbon\Carbon::create($year)->startOfYear();
        $yearEnd = \Carbon\Carbon::create($year)->endOfYear();

        // Daily counts for the selected year
        if ($isCurrentYear) {
            $from = Carbon::now()->startOfDay()->subDays(29);
        } else {
            $from = $yearStart;
        }
        $to = $isCurrentYear ? Carbon::now() : $yearEnd;

        $dailyApplications = Application::query()
            ->selectRaw("date(created_at) as d, application_type, count(*) as c")
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('d', 'application_type')
            ->orderBy('d')
            ->get();

        $dailyPublicUsers = User::query()
            ->selectRaw("date(created_at) as d, count(*) as c")
            ->whereBetween('created_at', [$from, $to])
            ->where('account_type', 'public')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $statusBreakdown = Application::selectRaw('status, count(*) as c')
            ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))
            ->groupBy('status')
            ->orderByDesc('c')
            ->get();

        $typeBreakdown = Application::selectRaw('application_type, count(*) as c')
            ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))
            ->groupBy('application_type')
            ->orderByDesc('c')
            ->get();

        $regionBreakdown = Application::selectRaw('collection_region, count(*) as c')
            ->whereNotNull('collection_region')
            ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))
            ->groupBy('collection_region')
            ->orderByDesc('c')
            ->get();

        $totals = [
            'public_users' => User::where('account_type', 'public')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'staff_users' => User::where('account_type', 'staff')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'accreditation' => Application::where('application_type', 'accreditation')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'registration' => Application::where('application_type', 'registration')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
        ];

        return view('admin.analytics.index', compact(
            'year', 'availableYears',
            'from', 'to',
            'dailyApplications',
            'dailyPublicUsers',
            'statusBreakdown',
            'typeBreakdown',
            'regionBreakdown',
            'totals'
        ));
    }
}
