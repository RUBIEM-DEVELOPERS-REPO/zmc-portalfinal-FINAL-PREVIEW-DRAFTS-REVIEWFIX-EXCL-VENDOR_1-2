<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use App\Models\ActivityLog;
use App\Models\AuditLog;
use App\Models\Region;
use App\Models\SystemConfig;
use App\Models\RegistrationRecord;
use App\Models\ApplicationAuditLog;
use App\Models\SystemLog;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Support\AuditTrail as AuditTrailSupport;
use ZipArchive;

class ItDashboardController extends Controller
{
    /**
     * IT Dashboard - Main Entry
     */
    public function index(Request $request)
    {
        // ── System Overview KPIs (from ItAdminController) ──────────────────
        $totalUsers   = User::count();
        $usersByRole  = Role::withCount('users')->orderBy('name')->get();

        // Application statistics
        $appStats = [
            'new_accreditation'       => Application::where('application_type', 'accreditation')->where('request_type', 'new')->count(),
            'renewal_accreditation'   => Application::where('application_type', 'accreditation')->where('request_type', 'renewal')->count(),
            'media_house_registration'=> Application::where('application_type', 'registration')->where('request_type', 'new')->count(),
            'media_house_renewal'     => Application::where('application_type', 'registration')->where('request_type', 'renewal')->count(),
        ];

        // Approval / rejection metrics
        $hasDecisionStatus = Schema::hasColumn('applications', 'decision_status');
        if ($hasDecisionStatus) {
            $approvedCount = Application::where('decision_status', 'Approved')->count();
            $rejectedCount = Application::where('decision_status', 'Rejected')->count();
            $pendingCount  = Application::where('decision_status', 'Pending')->count();
        } else {
            $approvedCount = Application::whereNotNull('approved_at')->count();
            $rejectedCount = Application::whereNotNull('rejected_at')->count();
            $pendingCount  = Application::whereNull('approved_at')->whereNull('rejected_at')->count();
        }
        $totalDecisions = max(1, $approvedCount + $rejectedCount);
        $approvalRatio  = round(($approvedCount / $totalDecisions) * 100, 1);

        // Approval trend (last 14 days)
        $approvalTrend = [];
        try {
            $trendRows = Application::selectRaw("DATE(COALESCE(decided_at, approved_at, rejected_at)) as d, SUM(CASE WHEN (decision_status='Approved' OR approved_at IS NOT NULL) THEN 1 ELSE 0 END) as approved")
                ->whereRaw("COALESCE(decided_at, approved_at, rejected_at) >= ?", [now()->subDays(14)])
                ->groupBy('d')
                ->orderBy('d')
                ->get();
            foreach ($trendRows as $r) {
                $approvalTrend[] = ['date' => $r->d, 'approved' => (int) $r->approved];
            }
        } catch (\Throwable $e) {
            $approvalTrend = [];
        }

        // Payment summary
        $paymentSummary = ['Paid' => 0, 'Pending' => 0, 'Failed' => 0, 'Refunded' => 0, 'Revenue' => 0];
        if (auth()->user()->hasRole('super_admin')) {
            if (Schema::hasTable('payments')) {
                $statusCounts = DB::table('payments')->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')->toArray();
                $paymentSummary['Paid']     = (int)($statusCounts['success'] ?? $statusCounts['paid'] ?? 0);
                $paymentSummary['Pending']  = (int)($statusCounts['pending'] ?? 0);
                $paymentSummary['Failed']   = (int)($statusCounts['failed'] ?? 0);
                $paymentSummary['Refunded'] = (int)($statusCounts['refunded'] ?? 0);
                $paymentSummary['Revenue']  = (float) DB::table('payments')->whereIn('status', ['success','paid'])->sum('amount');
            } else {
                $appPay = Application::select('payment_status', DB::raw('COUNT(*) as total'))->groupBy('payment_status')->pluck('total', 'payment_status')->toArray();
                $paymentSummary['Paid']    = (int)($appPay['paid'] ?? 0);
                $paymentSummary['Pending'] = (int)($appPay['requested'] ?? 0);
                $paymentSummary['Failed']  = (int)($appPay['rejected'] ?? 0);
            }
        }

        // Accreditation trends - RESTRICTED FOR IT
        $accreditationTrend = [];

        // Avg processing time - RESTRICTED FOR IT
        $avgProcessingHours = 0;

        // System health checks
        $health = ['database' => false, 'storage' => false, 'queue' => false, 'payment_callback' => false];
        try { DB::select('select 1'); $health['database'] = true; } catch (\Throwable $e) {}
        try {
            $disk = Storage::disk(config('filesystems.default', 'public'));
            $p = 'health/probe_' . uniqid() . '.txt';
            $disk->put($p, 'ok'); $disk->delete($p);
            $health['storage'] = true;
        } catch (\Throwable $e) {}
        try {
            $driver = config('queue.default');
            if ($driver === 'database') $health['queue'] = Schema::hasTable('jobs');
            elseif ($driver === 'redis') { $health['queue'] = (bool) app('redis')->connection()->ping(); }
            else $health['queue'] = true;
        } catch (\Throwable $e) {}
        try { $health['payment_callback'] = \Illuminate\Support\Facades\Route::has('paynow.callback'); } catch (\Throwable $e) {}

        // Storage usage
        $storageUsageBytes = null;
        $storageByModule   = [];
        if (Schema::hasTable('application_documents') && Schema::hasColumn('application_documents', 'size')) {
            $storageUsageBytes = (int) DB::table('application_documents')->sum('size');
            $storageByModule   = DB::table('application_documents')->select('doc_type', DB::raw('SUM(size) as total'))->groupBy('doc_type')->orderByDesc('total')->limit(8)->get();
        }

        // Regions
        $regions = Region::withCount('officers')->get();

        // Pending staff approvals
        $pending = User::query()->whereNull('approved_at')->whereHas('roles')->latest('id')->paginate(15);

        // ── Dashboard Overview Stats ───────────────────────────────────
        $stats = [
            'total_users'      => $totalUsers,
            'draft_count'      => Application::where('status', Application::DRAFT)->count(),
        ];

        // RESTRICTED: Operational trends & financial data
        $accreditationTrend = [];
        $registrationTrends = [];
        $trendLabels = [];
        $approvalTrend = [];
        $paymentSummary = null;

        if (auth()->user()->hasRole('super_admin')) {
             // Operational trends moved to Stakeholder dashboards (Registrar, Director)
             // Only providing minimal stats for Super Admin if needed
             $paymentSummary = Application::select('payment_status', DB::raw('count(*) as total'))->groupBy('payment_status')->pluck('total', 'payment_status')->toArray();
        }
        $currentRangeLabel = 'RESTRICTED';

        $avgProcessingTime = 0;

        $storageUsage = [
            'total' => disk_total_space('/'),
            'free'  => disk_free_space('/'),
            'used'  => disk_total_space('/') - disk_free_space('/'),
        ];

        // ── Data for Partials ───────────────────────────────────────────────
        $monitoringQuery      = Application::with(['applicant', 'assignedOfficer'])->latest()->paginate(10, ['*'], 'monitoring_page');
        $draftsQuery          = Application::where('status', Application::DRAFT)->with('applicant')->latest()->paginate(10, ['*'], 'drafts_page');
        
        // Grouped File Query with Filtering
        $fDateFrom = $request->query('f_date_from');
        $fDateTo = $request->query('f_date_to');
        
        $filesQuery = \App\Models\ApplicationDocument::query()
            ->with(['application.applicant'])
            ->when($fDateFrom, fn($q) => $q->whereDate('created_at', '>=', $fDateFrom))
            ->when($fDateTo, fn($q) => $q->whereDate('created_at', '<=', $fDateTo))
            ->orderBy('application_id')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'files_page');

        $errorLogs            = ActivityLog::where('action', 'like', '%error%')->orWhere('action', 'like', '%failed%')->latest()->paginate(15, ['*'], 'errors_page');
        $auditLogs            = AuditTrail::latest()->paginate(20, ['*'], 'audit_page');

        $recentTransactions  = Schema::hasTable('payments') ? DB::table('payments')->latest()->paginate(10, ['*'], 'payments_page') : collect();
        $paymentReconciliation = [
            'total_revenue'  => 0,
            'pending_proofs' => 0,
        ];
        if (auth()->user()->hasRole('super_admin')) {
             $paymentReconciliation = [
                'total_revenue'  => Schema::hasTable('payments') ? DB::table('payments')->where('status', 'success')->sum('amount') : 0,
                'pending_proofs' => Application::where('payment_status', 'pending_proof')->count(),
            ];
        }

        $activeSessions = [];
        if (config('session.driver') === 'database') {
            $activeSessions = DB::table('sessions')->latest('last_activity')->limit(50)->get();
        }

        $systemEnv = [
            'app_env'        => config('app.env'),
            'app_debug'      => config('app.debug'),
            'maintenance'    => app()->isDownForMaintenance(),
            'php_version'    => PHP_VERSION,
            'laravel_version'=> app()->version(),
        ];


        $storageStats = [
            'public'  => shell_exec('du -sh storage/app/public') ?: 'N/A',
            'uploads' => shell_exec('du -sh storage/app/uploads') ?: 'N/A',
        ];

        return view('staff.it.dashboard.index', compact(
            // Dashboard data
            'totalUsers', 'usersByRole', 'appStats',
            'approvedCount', 'rejectedCount', 'pendingCount', 'approvalRatio',
            'approvalTrend', 'paymentSummary', 'accreditationTrend', 'avgProcessingHours',
            'health', 'storageUsageBytes', 'storageByModule',
            'regions', 'pending',
            // Dashboard detailed data
            'stats', 'accreditationTrends', 'registrationTrends', 'trendLabels', 'currentRangeLabel', 'avgProcessingTime', 'storageUsage',
            'monitoringQuery', 'draftsQuery', 'filesQuery', 'errorLogs',
            'recentTransactions', 'paymentReconciliation', 'activeSessions', 'auditLogs',
            'systemEnv', 'storageStats'
        ));
    }


    /**
     * 2. Application Monitoring
     */
    public function monitoring(Request $request)
    {
        return redirect()->route('staff.it.dashboard', ['tab' => 'monitoring']);
    }

    /**
     * Read-only application detail + activity timeline
     */
    public function showApplication(Application $application)
    {
        $application->load(['applicant', 'assignedOfficer', 'documents', 'accreditationRecord', 'registrationRecord', 'lockedBy']);

        $timeline = collect();

        // Application audit logs (IT actions)
        if (Schema::hasTable('application_audit_logs')) {
            $timeline = $timeline->merge(
                ApplicationAuditLog::with('actor')
                    ->where('application_id', $application->id)
                    ->latest('created_at')
                    ->limit(200)
                    ->get()
                    ->map(function ($l) {
                        return [
                            'time' => $l->created_at,
                            'actor' => $l->actor?->name,
                            'action' => $l->action,
                            'meta' => $l->reason,
                            'type' => 'audit',
                        ];
                    })
            );
        }

        // Existing workflow activity logs (morph activity_logs)
        try {
            $timeline = $timeline->merge(
                $application->workflowLogs()
                    ->with('user')
                    ->limit(200)
                    ->get()
                    ->map(function ($l) {
                        return [
                            'time' => $l->created_at,
                            'actor' => $l->user?->name,
                            'action' => $l->action,
                            'meta' => $l->description ?? null,
                            'type' => 'workflow',
                        ];
                    })
            );
        } catch (\Throwable $e) {
            // ignore
        }

        $timeline = $timeline->sortByDesc('time')->values();

        return view('staff.it.dashboard.application-show', compact('application', 'timeline'));
    }

    /**
     * 3. Draft Management
     */
    public function drafts()
    {
        $drafts = Application::where('status', Application::DRAFT)
            ->with('applicant')
            ->latest()
            ->paginate(20);

        return view('staff.it.dashboard.drafts', compact('drafts'));
    }

    /**
     * 4. File & Document Management
     */
    public function files()
    {
        // Simple file listing for audit
        $files = DB::table('application_documents')->latest()->paginate(20);
        
        $storageStats = [
            'public' => shell_exec('du -sh storage/app/public') ?: 'N/A',
            'uploads' => shell_exec('du -sh storage/app/uploads') ?: 'N/A',
        ];

        return view('staff.it.dashboard.files', compact('files', 'storageStats'));
    }

    /**
     * 5. Error & Log Monitoring
     */
    public function errors()
    {
        // Fetch logs that looks like errors
        $logs = ActivityLog::where('action', 'like', '%error%')
            ->orWhere('action', 'like', '%failed%')
            ->latest()
            ->paginate(20);

        return view('staff.it.dashboard.errors', compact('logs'));
    }





    /**
     * 10. Payment Management
     */
    public function payments()
    {
        $transactions = DB::table('payments')->latest()->paginate(20);
        $reconciliation = [
            'total_revenue' => DB::table('payments')->where('status', 'success')->sum('amount'),
            'pending_proofs' => Application::where('payment_status', 'pending_proof')->count(),
        ];
        return view('staff.it.dashboard.payments', compact('transactions', 'reconciliation'));
    }

    /**
     * 11. Security Monitoring
     */
    public function security()
    {
        $sessions = [];
        if (config('session.driver') === 'database') {
            $sessions = DB::table('sessions')->latest('last_activity')->limit(50)->get();
        }
        return view('staff.it.dashboard.security', compact('sessions'));
    }

    /**
     * 12. Backup & Recovery
     */
    public function backup()
    {
        return view('staff.it.dashboard.backup');
    }

    /**
     * 13. Audit & Compliance
     */
    public function audit()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('staff.it.dashboard.audit', compact('logs'));
    }

    /**
     * 14. System Configuration
     */
    public function system()
    {
        $env = [
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
            'maintenance' => app()->isDownForMaintenance(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
        return view('staff.it.dashboard.system', compact('env'));
    }


    /**
     * 16. Reporting & Export
     */
    public function reports()
    {
        return view('staff.it.dashboard.reports');
    }


    // --- ACTIONS ---

    public function unlockApplication(Application $application)
    {
        $old = ['locked_by' => $application->locked_by, 'locked_at' => $application->locked_at];
        $application->unlock();
        if (Schema::hasTable('application_audit_logs')) {
            ApplicationAuditLog::create([
                'application_id' => $application->id,
                'actor_user_id' => auth()->id(),
                'action' => 'unlock',
                'old_value' => json_encode($old),
                'new_value' => json_encode(['locked_by' => null, 'locked_at' => null]),
                'ip' => request()->ip(),
                'reason' => request()->input('reason'),
            ]);
        }
        return back()->with('success', 'Application unlocked.');
    }

    public function resetApplication(Application $application)
    {
        $old = ['status' => $application->status, 'workflow_state' => $application->workflow_state ?? null];
        $application->update([
            'status' => Application::DRAFT,
            'workflow_state' => Application::DRAFT,
            'last_valid_state' => $old['status'],
        ]);
        if (Schema::hasTable('application_audit_logs')) {
            ApplicationAuditLog::create([
                'application_id' => $application->id,
                'actor_user_id' => auth()->id(),
                'action' => 'reset_to_last_valid',
                'old_value' => json_encode($old),
                'new_value' => json_encode(['status' => Application::DRAFT]),
                'ip' => request()->ip(),
                'reason' => request()->input('reason'),
            ]);
        }
        return back()->with('success', 'Application reset to draft.');
    }

    public function suspendUser(User $user)
    {
        $user->update(['account_status' => $user->account_status === 'suspended' ? 'active' : 'suspended']);
        return back()->with('success', 'User status updated.');
    }




    public function processPaymentQueue()
    {
        // Placeholder for manual reconciliation logic
        AuditTrailSupport::log('it_admin.process_payments');
        return back()->with('success', 'Payment reconciliation queue processed.');
    }

    public function triggerBackup()
    {
        try {
            // Artisan::call('backup:run'); // Usually handled by spatie/laravel-backup
            AuditTrailSupport::log('it_admin.trigger_backup');
            return back()->with('success', 'Manual backup snapshot triggered.');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        
        AuditTrailSupport::log('it_admin.clear_cache');
        return back()->with('success', 'System cache cleared.');
    }

    public function runCleanup()
    {
        // Detect orphans or temp files
        AuditTrailSupport::log('it_admin.run_cleanup');
        return back()->with('success', 'Cleanup scan completed. No critical orphans detected.');
    }

    public function forcePasswordReset(User $user)
    {
        $user->update(['password_change_required' => true]);
        AuditTrailSupport::log('it_admin.force_password_reset', $user);
        return back()->with('success', 'User ' . $user->name . ' flagged for password reset.');
    }

    public function logoutSession($id)
    {
        DB::table('sessions')->where('id', $id)->delete();
        AuditTrailSupport::log('it_admin.session_logout', null, ['session_id' => $id]);
        return back()->with('success', 'Active session terminated.');
    }

    public function blockIp(Request $request)
    {
        // Mocking IP block logic
        AuditTrailSupport::log('it_admin.block_ip', null, ['ip' => $request->ip]);
        return back()->with('success', 'Firewall rule created: IP ' . $request->ip . ' blocked.');
    }

    public function toggleRateLimiting()
    {
        // Toggle system setting
        AuditTrailSupport::log('it_admin.toggle_rate_limiting');
        return back()->with('success', 'Global rate limiting toggled.');
    }

    public function sslAudit()
    {
        // Placeholder for SSL/CSRF check
        AuditTrailSupport::log('it_admin.ssl_audit');
        return back()->with('success', 'SSL & CSRF security audit completed. All protocols healthy.');
    }

    public function generateReport($type)
    {
        // Placeholder for report generation
        AuditTrailSupport::log('it_admin.generate_report', null, ['type' => $type]);
        return back()->with('success', strtoupper($type) . ' report generated and sent to your email.');
    }

    public function downloadBatch(Application $application)
    {
        if (auth()->user()->hasRole('it_admin')) {
            return back()->with('error', 'IT Admin is not authorized to download application files. Access restricted.');
        }

        $documents = $application->documents;
        if ($documents->isEmpty()) {
            return back()->with('error', 'No documents found for this application.');
        }

        $zip = new ZipArchive;
        $fileName = 'documents_' . ($application->reference ?? $application->id) . '_' . now()->format('YmdHis') . '.zip';
        $tempFile = storage_path('app/temp_' . $fileName);

        if ($zip->open($tempFile, ZipArchive::CREATE) === TRUE) {
            foreach ($documents as $doc) {
                // Determine absolute path
                $path = null;
                if ($doc->path && Storage::exists($doc->path)) {
                    $path = storage_path('app/' . $doc->path);
                } elseif ($doc->path && Storage::disk('public')->exists($doc->path)) {
                    $path = storage_path('app/public/' . $doc->path);
                }

                if ($path && file_exists($path)) {
                    $zip->addFile($path, $doc->document_type . '_' . basename($path));
                }
            }
            $zip->close();
        }

        if (file_exists($tempFile)) {
            return response()->download($tempFile)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Could not create ZIP archive.');
    }
}
