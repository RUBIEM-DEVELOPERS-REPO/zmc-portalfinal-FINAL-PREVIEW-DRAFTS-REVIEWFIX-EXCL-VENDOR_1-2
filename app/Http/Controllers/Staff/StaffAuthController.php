<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\StaffOTPNotification;

class StaffAuthController extends Controller
{
    public function show(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user(), $request);
        }

        return view('staff.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember')
        )) {
            \App\Support\AuditTrail::log('login_failed', null, [
                'portal' => 'staff',
                'email' => $credentials['email'],
            ]);
            return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Block suspended / pending accounts
        if (($user->account_status ?? 'active') !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->withErrors(['email' => 'Account is not active. Please contact the system administrator.']);
        }

        // Check that user has staff roles
        $userRoles = $this->roleNamesForUser($user->id);
        $staffRoles = array_intersect($userRoles, $this->staffAllowedRoles());

        if (empty($staffRoles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->withErrors(['email' => 'Access denied. Not a staff account.']);
        }

        // Check if this is first-time login after setup
        $isFirstLogin = $user->account_status === 'active' && !$user->last_login_at;
        
        // Generate and Send OTP (only for first-time login or if user hasn't logged in before)
        if ($isFirstLogin) {
            $otp = (string) rand(100000, 999999);
            \Illuminate\Support\Facades\Cache::put('staff_otp:' . $user->id, $otp, now()->addMinutes(10));
            
            // Send OTP via email
            $user->notify(new \App\Notifications\StaffOTPNotification($otp));
            
            // Store user ID in session temporarily for OTP verification
            $request->session()->put('otp_user_id', $user->id);
            
            // Log out user for now, they are only "half-authenticated"
            Auth::logout();

            return redirect()->route('staff.otp.show')->with('info', 'A 6-digit OTP has been sent to your email address.');
        }
        
        // Update last login timestamp for subsequent logins
        if (!$isFirstLogin) {
            $user->update(['last_login_at' => now()]);
        }
    }

    public function showOTP()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('staff.login');
        }
        return view('staff.otp');
    }

    public function verifyOTP(Request $request)
    {
        $data = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('otp_user_id');
        if (!$userId) {
            return redirect()->route('staff.login');
        }

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('staff_otp:' . $userId);

        if (!$cachedOtp || $cachedOtp !== $data['otp']) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        \Illuminate\Support\Facades\Cache::forget('staff_otp:' . $userId);
        $request->session()->forget('otp_user_id');

        $user = User::findOrFail($userId);
        Auth::login($user);

        $request->session()->regenerate();

        $userRoles = $this->roleNamesForUser($user->id);
        $staffRoles = array_values(array_intersect($userRoles, $this->staffAllowedRoles()));
        
        // Use the role the user selected from the role selection page
        $selectedRole = $request->session()->get('staff_selected_role');
        
        // If no selected role or user doesn't have that role, pick the first available
        if (!$selectedRole || !in_array($selectedRole, $staffRoles, true)) {
            $selectedRole = $staffRoles[0];
        }

        $request->session()->put('active_staff_role', $selectedRole);
        
        $loginToken = bin2hex(random_bytes(32));
        \Illuminate\Support\Facades\Cache::put('login_token:' . $loginToken, [
            'user_id' => $user->id,
            'role' => $selectedRole,
        ], now()->addHours(8));

        \App\Support\AuditTrail::log('login_staff', $user, ['role' => $selectedRole]);

        $redirectUrl = $this->getRoleDashboardUrl($selectedRole);
        $separator = str_contains($redirectUrl, '?') ? '&' : '?';
        $redirectUrl .= $separator . '_auth_token=' . $loginToken;

        return response(
            '<html><head><meta http-equiv="refresh" content="0;url=' . e($redirectUrl) . '"></head>'
            . '<body><p>Redirecting…</p><script>window.location.href="' . e($redirectUrl) . '";</script></body></html>'
        )->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function logout(Request $request)
    {
        $token = $request->session()->get('_current_auth_token')
            ?? $request->query('_auth_token');
        if ($token) {
            \Illuminate\Support\Facades\Cache::forget('login_token:' . $token);
        }

        Auth::logout();

        $request->session()->forget(['active_staff_role', 'staff_selected_role', '_current_auth_token']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $url = route('staff.entry');
        return response(
            '<html><head></head><body><script>localStorage.removeItem("_auth_token");window.location.href="' . e($url) . '";</script></body></html>'
        )->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function switchRole(Request $request)
    {
        $role = $request->query('role');
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('staff.login');
        }

        $userRoles = $this->roleNamesForUser($user->id);
        
        if (in_array($role, $userRoles, true) && in_array($role, $this->staffAllowedRoles(), true)) {
            $request->session()->put('active_staff_role', $role);
            \App\Support\AuditTrail::log('role_switch', $user, ['new_role' => $role]);
            return $this->redirectToRoleDashboard($role);
        }

        return redirect()->route('staff.entry')->withErrors(['role' => 'Invalid role selected or access denied.']);
    }

    private function redirectAfterLogin($user, Request $request)
    {
        if (!$user) {
            return redirect()->route('staff.entry');
        }

        $userRoles = $this->roleNamesForUser($user->id);
        $staffRoles = array_values(array_intersect($userRoles, $this->staffAllowedRoles()));

        if (count($staffRoles) === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.entry')
                ->withErrors(['email' => 'Access denied. Not a staff account.']);
        }

        // If active role exists and user still has it -> go there
        $active = $request->session()->get('active_staff_role');
        if ($active && in_array($active, $userRoles, true)) {
            return $this->redirectToRoleDashboard($active);
        }

        // Otherwise force role selection again
        return redirect()->route('staff.entry');
    }

    private function redirectToRoleDashboard(string $role)
    {
        return redirect($this->getRoleDashboardUrl($role));
    }

    private function getRoleDashboardUrl(string $role): string
    {
        return match ($role) {
            'super_admin'            => route('admin.dashboard'),
            'accreditation_officer'  => route('staff.officer.dashboard'),
            'registrar'              => route('staff.registrar.dashboard'),
            'accounts_payments'      => route('staff.accounts.dashboard'),
            'production'             => route('staff.production.dashboard'),
            'it_admin'               => route('staff.it.dashboard'),
            'auditor'                => route('staff.auditor.dashboard'),
            'director'               => route('staff.director.dashboard'),
            default                  => route('staff.entry'),
        };
    }

    /**
     * Staff roles permitted in the staff portal.
     */
    private function staffAllowedRoles(): array
    {
        return [
            'super_admin',
            'accreditation_officer',
            'registrar',
            'accounts_payments',
            'production',
            'it_admin',
            'auditor',
            'director',
        ];
    }

    /**
     * ✅ Reads roles from Spatie tables directly:
     * model_has_roles + roles
     */
    private function roleNamesForUser(int $userId): array
    {
        // Default Spatie table names (works unless you customized config)
        $rolesTable = 'roles';
        $modelHasRolesTable = 'model_has_roles';

        return DB::table($modelHasRolesTable)
            ->join($rolesTable, "{$rolesTable}.id", '=', "{$modelHasRolesTable}.role_id")
            ->where("{$modelHasRolesTable}.model_id", $userId)
            ->where("{$modelHasRolesTable}.model_type", User::class)
            ->pluck("{$rolesTable}.name")
            ->map(fn ($r) => (string) $r)
            ->toArray();
    }
}
