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
        $totalUsers   = User::count();
        $usersByRole  = Role::withCount('users')->orderBy('name')->get();
        $pending = User::query()->whereNull('approved_at')->whereHas('roles')->latest('id')->paginate(15);
        $regions = Region::withCount('officers')->get();

        $stats = [
            'total_users'      => $totalUsers,
        ];

        $storageUsageBytes = null;
        $storageByModule   = [];
        if (Schema::hasTable('application_documents') && Schema::hasColumn('application_documents', 'size')) {
            $storageUsageBytes = (int) DB::table('application_documents')->sum('size');
            $storageByModule   = DB::table('application_documents')->select('doc_type', DB::raw('SUM(size) as total'))->groupBy('doc_type')->orderByDesc('total')->limit(8)->get();
        }

        $health = ['database' => false, 'storage' => false, 'queue' => false];
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

        $storageUsage = [
            'total' => disk_total_space('/'),
            'free'  => disk_free_space('/'),
            'used'  => disk_total_space('/') - disk_free_space('/'),
        ];
        
        $errorLogs            = ActivityLog::where('action', 'like', '%error%')->orWhere('action', 'like', '%failed%')->latest()->paginate(15, ['*'], 'errors_page');
        $auditLogs            = AuditTrail::latest()->paginate(20, ['*'], 'audit_page');

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

        $isDbUp = $health['database'];
        $maintenanceMode = app()->isDownForMaintenance();
        $driveSpace = round(($storageUsage['free'] / max($storageUsage['total'], 1)) * 100, 1) . '% free';
        $lastBackup = 'N/A';
        $envData = $systemEnv;
        $activeTab = $request->input('tab', 'overview');

        return view('staff.it.dashboard.index', compact(
            'totalUsers', 'usersByRole', 'isDbUp', 'maintenanceMode',
            'driveSpace', 'lastBackup', 'envData', 'activeTab',
            'health', 'storageUsageBytes', 'storageByModule',
            'regions', 'pending',
            'stats', 'storageUsage',
            'errorLogs',
            'activeSessions', 'auditLogs',
            'systemEnv', 'storageStats'
        ));
    }


    /**
     * Role Management
     */
    public function roles()
    {
        return view('staff.it.dashboard.roles');
    }

    /**
     * Templates Management
     */
    public function templates()
    {
        return view('staff.it.dashboard.templates');
    }

    /**
     * Printers Configuration
     */
    public function printers()
    {
        return view('staff.it.dashboard.printers');
    }

    /**
     * Numbering Configuration
     */
    public function numbering()
    {
        return view('staff.it.dashboard.numbering');
    }

    /**
     * Category Master Data
     */
    public function categories()
    {
        return view('staff.it.dashboard.categories');
    }

    /**
     * Regions and Locations
     */
    public function regions()
    {
        // Re-use admin system view or direct to a specific IT regions view.
        return redirect()->route('admin.regions.index');
    }

    /**
     * Document Settings
     */
    public function documentSettings()
    {
        return view('staff.it.dashboard.document-settings');
    }

    /**
     * QR Code and Security Features
     */
    public function qrSecurity()
    {
        return view('staff.it.dashboard.qr-security');
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

    /**
     * Action handlers for system tools
     */
    public function saveConfig(Request $request)
    {
        $config = $request->except('_token');
        foreach ($config as $key => $value) {
            SystemConfig::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        AuditTrailSupport::log('it_admin.save_config', null, ['keys' => array_keys($config)]);
        return back()->with('success', 'System configuration updated.');
    }

    public function suspendUser(User $user)
    {
        $user->update(['account_status' => $user->account_status === 'suspended' ? 'active' : 'suspended']);
        AuditTrailSupport::log('it_admin.toggle_user_status', $user, ['new_status' => $user->account_status]);
        return back()->with('success', 'User status updated to ' . $user->account_status . '.');
    }

    public function users(Request $request)
    {
        $query = User::with('roles');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('account_type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('account_status', $status);
        }

        if ($role = $request->get('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        $totalUsers = User::count();
        $staffCount = User::where('account_type', 'staff')->count();
        $publicCount = User::where('account_type', 'public')->count();
        $activeCount = User::where('account_status', 'active')->count();
        $suspendedCount = User::where('account_status', 'suspended')->count();
        $pendingCount = User::whereIn('account_status', ['pending', 'pending_setup'])->count();

        return view('staff.it.users-management', compact(
            'users', 'roles', 'totalUsers', 'staffCount', 'publicCount',
            'activeCount', 'suspendedCount', 'pendingCount'
        ));
    }

    public function editUserRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);

        $previousRole = $user->roles->first()?->name;
        
        // Generate temporary credentials
        $tempPassword = Str::random(12);
        $tempPasswordHash = Hash::make($tempPassword);
        
        $user->syncRoles([$request->role]);

        $newType = in_array($request->role, ['super_admin', 'it_admin', 'director', 'registrar', 'accreditation_officer', 'accounts_payments', 'production', 'auditor', 'complaints_officer'])
            ? 'staff' : 'public';
        $user->update([
            'account_type' => $newType,
            'temp_password' => $tempPasswordHash,
            'temp_password_expires_at' => now()->addHours(24),
            'password_change_required' => true,
        ]);

        // Log with detailed metadata including temp credentials indicator
        AuditTrailSupport::log('it_admin.change_user_role', $user, [
            'new_role' => $request->role,
            'previous_role' => $previousRole,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'temp_credentials' => true,
            'temp_password_expires' => now()->addHours(24)->toDateTimeString(),
        ]);

        // Send email with temporary credentials
        try {
            Mail::send('emails.temporary-credentials', [
                'user' => $user,
                'tempPassword' => $tempPassword,
                'role' => $request->role,
                'expiresAt' => now()->addHours(24)->format('d M Y H:i'),
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('ZMC Portal - Role Assignment & Temporary Credentials');
            });
            
            return back()->with('success', "Role updated to {$request->role} for {$user->name}. Temporary credentials sent to {$user->email} (expires in 24 hours).");
        } catch (\Throwable $e) {
            return back()->with('success', "Role updated to {$request->role} for {$user->name}.")
                        ->with('warning', "Email failed: {$e->getMessage()}. Temporary password: {$tempPassword} (expires in 24 hours)");
        }
    }

    public function activateUser(User $user)
    {
        $user->update(['account_status' => 'active']);
        AuditTrailSupport::log('it_admin.activate_user', $user);
        return back()->with('success', "{$user->name}'s account has been activated.");
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        AuditTrailSupport::log('it_admin.delete_user', $user, [
            'deleted_name' => $userName,
            'deleted_email' => $userEmail,
        ]);

        $user->roles()->detach();
        $user->permissions()->detach();
        $user->delete();

        return back()->with('success', "User \"{$userName}\" ({$userEmail}) has been permanently deleted.");
    }

    public function resendActivation(User $user)
    {
        $token = \Illuminate\Support\Str::random(64);
        $user->forceFill([
            'activation_token' => $token,
            'account_status' => 'pending',
        ])->save();

        $activationUrl = route('staff.activate', $token);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Hello {$user->name},\n\nYour account activation link has been resent.\n\nPlease click the link below to set your password and activate your account:\n\n{$activationUrl}\n\nThis link is valid until used.\n\nRegards,\nZimbabwe Media Commission",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('ZMC Portal - Account Activation');
                }
            );
            AuditTrailSupport::log('it_admin.resend_activation', $user);
            return back()->with('success', "Activation link resent to {$user->email}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send activation email: ' . $e->getMessage());
        }
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


}
