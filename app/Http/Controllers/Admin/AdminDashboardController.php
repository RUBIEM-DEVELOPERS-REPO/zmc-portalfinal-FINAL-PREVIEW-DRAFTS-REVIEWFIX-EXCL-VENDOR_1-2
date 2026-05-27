<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use App\Models\AuditTrail;
use App\Models\AuditLog;
use App\Models\SystemConfig;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * We treat anything created via signup as PUBLIC and anything created by IT Admin/Super Admin as STAFF.
     * (Stored in users.account_type)
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $availableYears = range(2024, now()->year);
        rsort($availableYears);

        $isCurrentYear = ($year == now()->year);
        $yearStart = \Carbon\Carbon::create($year)->startOfYear();
        $yearEnd = \Carbon\Carbon::create($year)->endOfYear();

        $workflowCfg = SystemConfig::getValue('workflow', config('zmc.workflow'));
        $slaHours = $workflowCfg['sla_hours'] ?? config('zmc.workflow.sla_hours');

        $pendingStatuses = [
            Application::SUBMITTED,
            Application::OFFICER_REVIEW,
            Application::REGISTRAR_REVIEW,
            Application::ACCOUNTS_REVIEW,
            Application::PRODUCTION_QUEUE,
        ];

        $rejectedStatuses = [
            Application::OFFICER_REJECTED,
            Application::REGISTRAR_REJECTED,
        ];

        // "Approved" in this workflow means it reached an output stage or issuance.
        $approvedStatuses = [
            Application::OFFICER_APPROVED,
            Application::REGISTRAR_APPROVED,
            Application::PAID_CONFIRMED,
            Application::CARD_GENERATED,
            Application::CERT_GENERATED,
            Application::PRINTED,
            Application::ISSUED,
        ];

        $stats = [
            'total_users' => User::count(),
            'staff_users' => User::where('account_type', 'staff')->count(),
            'public_users' => User::where('account_type', 'public')->count(),
            'new_users_today' => $isCurrentYear ? User::whereDate('created_at', Carbon::today())->count() : 0,
            'new_users_week' => $isCurrentYear ? User::where('created_at', '>=', Carbon::now()->subWeek())->count() : 0,
            'roles' => Role::count(),
            'permissions' => Permission::count(),

            // Applications
            'total_applications' => Application::when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'applications_today' => $isCurrentYear ? Application::whereDate('created_at', Carbon::today())->count() : 0,
            'mediahouse_registrations' => Application::where('application_type', 'registration')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'journalist_accreditations' => Application::where('application_type', 'accreditation')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'pending_applications' => Application::whereIn('status', $pendingStatuses)
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'approved_applications' => Application::whereIn('status', $approvedStatuses)
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'rejected_applications' => Application::whereIn('status', $rejectedStatuses)
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),

            'audit_entries' => AuditTrail::when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
        ];

        // Applications by stage (Officer/Accounts/Registrar/Production)
        $applicationsByStage = [
            'Officer' => Application::whereIn('status', [Application::SUBMITTED, Application::OFFICER_REVIEW, Application::CORRECTION_REQUESTED])
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'Accounts' => Application::whereIn('status', [Application::OFFICER_APPROVED, Application::ACCOUNTS_REVIEW])
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'Registrar' => Application::whereIn('status', [Application::PAID_CONFIRMED, Application::REGISTRAR_REVIEW])
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'Production' => Application::whereIn('status', [Application::REGISTRAR_APPROVED, Application::PRODUCTION_QUEUE, Application::CARD_GENERATED, Application::CERT_GENERATED, Application::PRINTED])
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
        ];


        // System health quick stats
        $failedJobs = 0;
        try {
            $failedJobs = (int) DB::table('failed_jobs')->count();
        } catch (\Throwable $e) {
            $failedJobs = 0;
        }

        // Alerts
        $now = Carbon::now();
        $slaBreaches = 0;
        $slaBreachSamples = [];
        $slaMap = [
            'submitted' => Application::SUBMITTED,
            'officer_review' => Application::OFFICER_REVIEW,
            'accounts_review' => Application::ACCOUNTS_REVIEW,
            'registrar_review' => Application::REGISTRAR_REVIEW,
            'production_queue' => Application::PRODUCTION_QUEUE,
        ];
        foreach ($slaMap as $key => $status) {
            $hours = (int)($slaHours[$key] ?? 0);
            if ($hours <= 0) continue;
            $cut = $now->copy()->subHours($hours);
            $q = Application::where(function($q) use ($status, $cut) {
                $q->where('status', '=', $status)->where('updated_at', '<', $cut);
            });
            $slaBreaches += $q->count();
            $sample = $q->orderBy('updated_at')->limit(3)->get();
            foreach ($sample as $a) {
                $slaBreachSamples[] = $a;
            }
        }

        $stuckApplications = Application::whereNotIn('status', [Application::ISSUED, Application::OFFICER_REJECTED, Application::REGISTRAR_REJECTED])
            ->where('updated_at', '<', Carbon::now()->subDays(7))
            ->count();

        $failedPayments = Application::whereIn('payment_status', ['failed', 'reversed'])->count();

        $alerts = compact('slaBreaches', 'stuckApplications', 'failedPayments');


        // Recent users split into two groups on the Superadmin dashboard:
        // 1) Public Users: self-registered during Journalist Accreditation
        // 2) Staff Users: created by Superadmin or IT Admin
        $staffCreatedIds = AuditLog::whereIn('action', ['account_created_by_superadmin', 'account_created_by_it_admin'])
            ->where(function($q) {
                $q->where('model_type', '=', User::class);
            })
            ->whereNotNull('model_id')
            ->orderByDesc('created_at')
            ->pluck('model_id')
            ->unique()
            ->values()
            ->take(200)
            ->toArray();

        $recentStaffUsers = User::with('roles')
            ->where(function ($q) use ($staffCreatedIds) {
                $q->where('account_type', 'staff');
                if (!empty($staffCreatedIds)) {
                    $q->orWhereIn('id', $staffCreatedIds);
                }
            })
            ->latest()
            ->take(8)
            ->get();

        $recentPublicUsers = User::with('roles')
            ->where(function($q) {
                $q->where('account_type', '=', 'public');
            })
            ->when(!empty($staffCreatedIds), function($q) use ($staffCreatedIds) {
                return $q->whereNotIn('id', $staffCreatedIds);
            })
            ->latest()
            ->take(8)
            ->get();

        // Back-compat
        $recentUsers = User::with('roles')->latest()->take(10)->get();



        $recentAudit = AuditTrail::when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))
            ->latest()->take(10)->get();

        $activityStream = AuditLog::when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))
            ->latest()->take(15)->get();
        
        $roles = Role::withCount('users')->get();
        
        $applicationsByStatus = Application::selectRaw('status, count(*) as count')
            ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        $applicationsByType = Application::selectRaw('application_type, count(*) as count')
            ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))
            ->groupBy('application_type')
            ->pluck('count', 'application_type')
            ->toArray();

        return view('admin.dashboard', compact(
            'year', 'availableYears',
            'stats',
            'applicationsByStage',
            'failedJobs',
            'alerts',
            'activityStream',
            'recentUsers',
            'recentStaffUsers',
            'recentPublicUsers',
            'recentAudit',
            'roles',
            'applicationsByStatus',
            'applicationsByType',
            'recentApplications'
        ));
    }

    /**
     * Unified JSON endpoint for all live dashboard data (counters + graphs).
     */
    public function refresh(Request $request)
    {
        $year = $request->get('year', now()->year);
        $isCurrentYear = ($year == now()->year);
        $yearStart = \Carbon\Carbon::create($year)->startOfYear();
        $yearEnd = \Carbon\Carbon::create($year)->endOfYear();

        $pendingStatuses = [
            Application::SUBMITTED,
            Application::OFFICER_REVIEW,
            Application::REGISTRAR_REVIEW,
            Application::ACCOUNTS_REVIEW,
            Application::PRODUCTION_QUEUE,
        ];

        // 1) Counters
        $stats = [
            'total_users' => User::count(),
            'staff_users' => User::where('account_type', 'staff')->count(),
            'public_users' => User::where('account_type', 'public')->count(),
            'mediahouse_registrations' => Application::where('application_type', 'registration')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'journalist_accreditations' => Application::where('application_type', 'accreditation')
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'pending_applications' => Application::whereIn('status', $pendingStatuses)
                ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            'applications_today' => $isCurrentYear ? Application::whereDate('created_at', \Carbon\Carbon::today())->count() : 0,
            'stages' => [
                'Officer' => Application::whereIn('status', [Application::SUBMITTED, Application::OFFICER_REVIEW, Application::CORRECTION_REQUESTED])
                    ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
                'Accounts' => Application::whereIn('status', [Application::OFFICER_APPROVED, Application::ACCOUNTS_REVIEW])
                    ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
                'Registrar' => Application::whereIn('status', [Application::PAID_CONFIRMED, Application::REGISTRAR_REVIEW])
                    ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
                'Production' => Application::whereIn('status', [Application::REGISTRAR_APPROVED, Application::PRODUCTION_QUEUE, Application::CARD_GENERATED, Application::CERT_GENERATED, Application::PRINTED])
                    ->when(!$isCurrentYear, fn($q) => $q->whereBetween('created_at', [$yearStart, $yearEnd]))->count(),
            ]
        ];

        // 2) Trend Data
        $from = $isCurrentYear ? now()->subDays(29)->startOfDay() : $yearStart;
        $to = $isCurrentYear ? now() : $yearEnd;

        $dailyApplications = Application::query()
            ->selectRaw("date(created_at) as d, application_type, count(*) as c")
            ->whereIn('application_type', ['accreditation', 'registration'])
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('d', 'application_type')
            ->orderBy('d')
            ->get();

        $dailyPublicUsers = User::query()
            ->selectRaw("date(created_at) as d, count(*) as c")
            ->where(function($q) {
                $q->where('account_type', '=', 'public');
            })
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $statusBreakdown = Application::selectRaw('status, count(*) as c')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->get();

        $days = $isCurrentYear ? 30 : $from->diffInDays($to) + 1;
        $labels = collect(range(0, $days - 1))
            ->map(fn($i) => $from->copy()->addDays($i)->format('Y-m-d'))
            ->values();

        $appMap = $dailyApplications->groupBy('d')->map(fn($rows) => $rows->pluck('c', 'application_type')->toArray())->toArray();
        $publicUsersMap = $dailyPublicUsers->pluck('c', 'd')->toArray();

        $trend = [
            'labels' => $labels->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->values()->all(),
            'datasets' => [
                'accreditation' => $labels->map(fn($d) => (int)($appMap[$d]['accreditation'] ?? 0))->values()->all(),
                'registration' => $labels->map(fn($d) => (int)($appMap[$d]['registration'] ?? 0))->values()->all(),
                'public_users' => $labels->map(fn($d) => (int)($publicUsersMap[$d] ?? 0))->values()->all(),
            ],
            'status_breakdown' => [
                'labels' => $statusBreakdown->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s ?? 'unknown')))->values()->all(),
                'counts' => $statusBreakdown->pluck('c')->map(fn($n) => (int) $n)->values()->all(),
            ]
        ];

        return response()->json([
            'stats' => $stats,
            'trend' => $trend,
        ]);
    }
}
