<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\AccreditationRecord;
use Spatie\Permission\Models\Role;
use App\Support\AuditTrail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ItAdminController extends Controller
{
    public function dashboard()
    {
        $pending = User::query()
            ->whereNull('approved_at')
            ->whereHas('roles')
            ->latest('id')
            ->paginate(15);

        $regions = \App\Models\Region::withCount('officers')->get();

        // System overview KPIs
        $totalUsers = User::count();
        $usersByRole = Role::withCount('users')->orderBy('name')->get();

        // Application statistics
        $appStats = [
            'new_accreditation' => Application::where('application_type', 'accreditation')->where('request_type', 'new')->count(),
            'renewal_accreditation' => Application::where('application_type', 'accreditation')->where('request_type', 'renewal')->count(),
            'media_house_registration' => Application::where('application_type', 'registration')->where('request_type', 'new')->count(),
            'media_house_renewal' => Application::where('application_type', 'registration')->where('request_type', 'renewal')->count(),
        ];

        // Approval / rejection metrics (supports decision_status or falls back)
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
        $approvalRatio = round(($approvedCount / $totalDecisions) * 100, 1);

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

        // Payment summary (payments table preferred)
        $paymentSummary = [
            'Paid' => 0,
            'Pending' => 0,
            'Failed' => 0,
            'Refunded' => 0,
            'Revenue' => 0,
        ];
        if (Schema::hasTable('payments')) {
            $statusCounts = DB::table('payments')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
            $paymentSummary['Paid'] = (int)($statusCounts['success'] ?? $statusCounts['paid'] ?? 0);
            $paymentSummary['Pending'] = (int)($statusCounts['pending'] ?? 0);
            $paymentSummary['Failed'] = (int)($statusCounts['failed'] ?? 0);
            $paymentSummary['Refunded'] = (int)($statusCounts['refunded'] ?? 0);
            $paymentSummary['Revenue'] = (float) DB::table('payments')->whereIn('status', ['success','paid'])->sum('amount');
        } else {
            $appPay = Application::select('payment_status', DB::raw('COUNT(*) as total'))
                ->groupBy('payment_status')
                ->pluck('total', 'payment_status')
                ->toArray();
            $paymentSummary['Paid'] = (int)($appPay['paid'] ?? 0);
            $paymentSummary['Pending'] = (int)($appPay['requested'] ?? 0);
            $paymentSummary['Failed'] = (int)($appPay['rejected'] ?? 0);
        }

        // Accreditation trends (monthly last 12 months) - use issued_at if available
        $accreditationTrend = [];
        if (Schema::hasColumn('applications', 'issued_at')) {
            $dateFormat = DB::getDriverName() === 'sqlite' 
                ? "strftime('%Y-%m', issued_at)" 
                : "TO_CHAR(issued_at, 'YYYY-MM')";

            $rows = Application::selectRaw("$dateFormat as ym, COUNT(*) as c")
                ->whereNotNull('issued_at')
                ->where('issued_at', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('ym')
                ->orderBy('ym')
                ->get();
            $map = $rows->pluck('c', 'ym')->toArray();
            for ($i = 11; $i >= 0; $i--) {
                $ym = now()->subMonths($i)->format('Y-m');
                $accreditationTrend[] = ['month' => $ym, 'count' => (int)($map[$ym] ?? 0)];
            }
        }

        // Avg processing time (submitted_at -> decided_at)
        $avgProcessingHours = 0;
        if (Schema::hasColumn('applications', 'submitted_at')) {
            try {
                $isSqlite = DB::getDriverName() === 'sqlite';
                $endTimeExpr = "COALESCE(decided_at, approved_at, rejected_at)";
                
                $diffExpression = $isSqlite 
                    ? "(julianday($endTimeExpr) - julianday(submitted_at)) * 24" 
                    : "EXTRACT(EPOCH FROM ($endTimeExpr - submitted_at)) / 3600";

                $avgProcessingHours = (float) Application::whereNotNull('submitted_at')
                    ->where(function ($q) {
                        $q->whereNotNull('decided_at')->orWhereNotNull('approved_at')->orWhereNotNull('rejected_at');
                    })
                    ->selectRaw("AVG($diffExpression) as avg_h")
                    ->value('avg_h') ?: 0;
            } catch (\Throwable $e) {
                $avgProcessingHours = 0;
            }
        }

        // Uptime / health checks
        $health = [
            'database' => false,
            'storage' => false,
            'queue' => false,
            'payment_callback' => false,
        ];
        try { DB::select('select 1'); $health['database'] = true; } catch (\Throwable $e) {}
        try {
            $disk = Storage::disk(config('filesystems.default', 'public'));
            $p = 'health/probe_' . uniqid() . '.txt';
            $disk->put($p, 'ok');
            $disk->delete($p);
            $health['storage'] = true;
        } catch (\Throwable $e) {}
        try {
            $driver = config('queue.default');
            if ($driver === 'database') $health['queue'] = Schema::hasTable('jobs');
            elseif ($driver === 'redis') { $health['queue'] = (bool) app('redis')->connection()->ping(); }
            else $health['queue'] = true;
        } catch (\Throwable $e) {}
        try { $health['payment_callback'] = Route::has('paynow.callback'); } catch (\Throwable $e) {}

        // Storage usage (application documents if size exists)
        $storageUsageBytes = null;
        $storageByModule = [];
        if (Schema::hasTable('application_documents') && Schema::hasColumn('application_documents', 'size')) {
            $storageUsageBytes = (int) DB::table('application_documents')->sum('size');
            $storageByModule = DB::table('application_documents')
                ->select('doc_type', DB::raw('SUM(size) as total'))
                ->groupBy('doc_type')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
        }

        return view('staff.it.dashboard', compact(
            'pending',
            'regions',
            'totalUsers',
            'usersByRole',
            'appStats',
            'approvedCount',
            'rejectedCount',
            'pendingCount',
            'approvalRatio',
            'approvalTrend',
            'paymentSummary',
            'accreditationTrend',
            'avgProcessingHours',
            'health',
            'storageUsageBytes',
            'storageByModule'
        ));
    }

    public function createUser()
    {
        $roles = Role::orderBy('name')->get();
        $regions = \App\Models\Region::where('is_active', true)->orderBy('name')->get();
        return view('staff.it.create-user', compact('roles', 'regions'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'roles' => ['nullable','array'],
            'roles.*' => ['string'],
            'assigned_regions' => ['nullable','array'],
            'assigned_regions.*' => ['exists:regions,id'],
        ]);

        $activationToken = Str::random(64);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'account_status' => 'pending',
            'account_type' => 'staff',
            'activation_token' => $activationToken,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $user->syncRoles($data['roles'] ?? []);

        if ($request->filled('assigned_regions')) {
            $user->assignedRegions()->sync($data['assigned_regions']);
        }

        $roleNames = implode(', ', $data['roles'] ?? []);
        $activationUrl = route('staff.activate', $activationToken);

        try {
            Mail::raw(
                "Hello {$user->name},\n\n"
                . "Your ZMC Staff account has been created with the role(s): {$roleNames}.\n\n"
                . "Please activate your account by clicking the link below and setting your password:\n\n"
                . "{$activationUrl}\n\n"
                . "This link is valid for one-time use.\n\n"
                . "Regards,\nZimbabwe Media Commission",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('ZMC Staff Account - Activate Your Account');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Activation email failed: ' . $e->getMessage());
        }

        AuditTrail::log('account_created_by_it_admin', $user, ['roles' => $data['roles'] ?? []]);

        return redirect()->route('staff.it.dashboard')->with('success', "Staff account created. Activation link sent to {$user->email}.");
    }

    /** Region Management */
    public function storeRegion(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:regions,code',
            'expires_at' => 'nullable|date|after:now',
        ]);

        \App\Models\Region::create($data + ['is_active' => true]);

        return back()->with('success', 'Region created successfully.');
    }

    public function toggleRegion(\App\Models\Region $region)
    {
        $region->update(['is_active' => !$region->is_active]);
        return back()->with('success', 'Region status updated.');
    }

    /** Admin Account Resets */
    public function listApplicants()
    {
        $applicants = User::where('account_type', 'applicant')->latest('id')->paginate(20);
        return view('staff.it.applicants', compact('applicants'));
    }

    public function resetApplicant(Request $request, User $user)
    {
        $data = $request->validate([
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        if ($request->filled('email')) $user->email = $data['email'];
        if ($request->filled('name'))  $user->name  = $data['name'];
        if ($request->filled('password')) $user->password = Hash::make($data['password']);
        
        $user->save();

        AuditTrail::log('it_admin_reset_account', $user, ['changed' => array_keys($data)]);

        return back()->with('success', 'Applicant account reset successfully.');
    }
}
