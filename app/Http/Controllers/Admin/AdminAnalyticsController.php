<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin|it_admin|director|pr_officer|research_training_standards']);
    }

    public function index()
    {
        // Last 30 days daily counts
        $from = Carbon::now()->startOfDay()->subDays(29);

        $dailyApplications = Application::query()
            ->selectRaw("date(created_at) as d, application_type, count(*) as c")
            ->where('created_at', '>=', $from)
            ->groupBy('d', 'application_type')
            ->orderBy('d')
            ->get();

        $dailyPublicUsers = User::query()
            ->selectRaw("date(created_at) as d, count(*) as c")
            ->where('created_at', '>=', $from)
            ->where('account_type', 'public')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $statusBreakdown = Application::selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->orderByDesc('c')
            ->get();

        $typeBreakdown = Application::selectRaw('application_type, count(*) as c')
            ->groupBy('application_type')
            ->orderByDesc('c')
            ->get();

        $regionBreakdown = Application::selectRaw('collection_region, count(*) as c')
            ->whereNotNull('collection_region')
            ->groupBy('collection_region')
            ->orderByDesc('c')
            ->get();

        $totals = [
            'public_users' => User::where('account_type', 'public')->count(),
            'staff_users' => User::where('account_type', 'staff')->count(),
            'accreditation' => Application::where('application_type', 'accreditation')->count(),
            'registration' => Application::where('application_type', 'registration')->count(),
        ];

        return view('admin.analytics.index', compact(
            'from',
            'dailyApplications',
            'dailyPublicUsers',
            'statusBreakdown',
            'typeBreakdown',
            'regionBreakdown',
            'totals'
        ));
    }
}
