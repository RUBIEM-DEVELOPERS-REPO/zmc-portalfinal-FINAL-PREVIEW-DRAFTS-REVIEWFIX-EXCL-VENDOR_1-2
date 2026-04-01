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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * We treat anything created via signup as PUBLIC and anything created by IT Admin/Super Admin as STAFF.
     * (Stored in users.account_type)
     */
    public function index()
    {
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
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
            'new_users_week' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),

            // Applications
            'total_applications' => Application::count(),
            'applications_today' => Application::whereDate('created_at', Carbon::today())->count(),
            'mediahouse_registrations' => Application::where('application_type', 'registration')->count(),
            'journalist_accreditations' => Application::where('application_type', 'accreditation')->count(),
            'pending_applications' => Application::whereIn('status', $pendingStatuses)->count(),
            'approved_applications' => Application::whereIn('status', $approvedStatuses)->count(),
            'rejected_applications' => Application::whereIn('status', $rejectedStatuses)->count(),

            'audit_entries' => AuditTrail::count(),
        ];

        // Applications by stage (Officer/Accounts/Registrar/Production)
        $applicationsByStage = [
            'Officer' => Application::whereIn('status', [Application::SUBMITTED, Application::OFFICER_REVIEW, Application::CORRECTION_REQUESTED])->count(),
            'Accounts' => Application::whereIn('status', [Application::OFFICER_APPROVED, Application::ACCOUNTS_REVIEW])->count(),
            'Registrar' => Application::whereIn('status', [Application::PAID_CONFIRMED, Application::REGISTRAR_REVIEW])->count(),
            'Production' => Application::whereIn('status', [Application::REGISTRAR_APPROVED, Application::PRODUCTION_QUEUE, Application::CARD_GENERATED, Application::CERT_GENERATED, Application::PRINTED])->count(),
        ];

        // Average turnaround (Issued) in hours, last 30 days
        $avgTurnaroundHours = (float) Application::query()
            ->where('status', Application::ISSUED)
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('AVG((julianday(updated_at) - julianday(created_at)) * 24) as avg_hours')
            ->value('avg_hours');

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
            $q = Application::query()->where('status', $status)->where('updated_at', '<', $cut);
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
        // 1) Public Users: self-registered during Media Practitioner Accreditation
        // 2) Staff Users: created by Superadmin or IT Admin
        $staffCreatedIds = AuditLog::query()
            ->whereIn('action', ['account_created_by_superadmin', 'account_created_by_it_admin'])
            ->where('model_type', User::class)
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
            ->where('account_type', 'public')
            ->when(!empty($staffCreatedIds), fn ($q) => $q->whereNotIn('id', $staffCreatedIds))
            ->latest()
            ->take(8)
            ->get();

        // Back-compat
        $recentUsers = User::with('roles')->latest()->take(10)->get();



        $recentAudit = AuditTrail::latest()->take(10)->get();

        $activityStream = AuditLog::latest()->take(15)->get();
        
        $roles = Role::withCount('users')->get();
        
        $applicationsByStatus = Application::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        $applicationsByType = Application::selectRaw('application_type, count(*) as count')
            ->groupBy('application_type')
            ->pluck('count', 'application_type')
            ->toArray();

        $recentApplications = Application::with('applicant')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'applicationsByStage',
            'avgTurnaroundHours',
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
     * JSON endpoint used by the admin dashboard for real-time counters.
     */
    public function stats()
    {
        $pendingStatuses = [
            Application::SUBMITTED,
            Application::OFFICER_REVIEW,
            Application::REGISTRAR_REVIEW,
            Application::ACCOUNTS_REVIEW,
            Application::PRODUCTION_QUEUE,
        ];

        return response()->json([
            'total_users' => User::count(),
            'staff_users' => User::where('account_type', 'staff')->count(),
            'public_users' => User::where('account_type', 'public')->count(),
            'mediahouse_registrations' => Application::where('application_type', 'registration')->count(),
            'journalist_accreditations' => Application::where('application_type', 'accreditation')->count(),
            'pending_applications' => Application::whereIn('status', $pendingStatuses)->count(),
        ]);
    }
}
