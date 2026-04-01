<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class StaffAuthController extends Controller
{
    public function show(Request $request)
    {
        // 1. If we have a pending selection, we show the login form for it.
        // Even if already logged in (e.g. as a public user), we need to re-auth for staff.
        if ($request->session()->has('staff_selected_role')) {
            // NOTE: If they are logged in as a public user, we don't logout yet.
            // We just let the login form show. When they POST the staff login,
            // the login() method will handle the session regeneration.
            return view('staff.login');
        }

        // 2. If already logged in AND NO pending selection, try to go to dashboard
        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user(), $request);
        }

        // 3. Must choose role first
        return redirect()->route('staff.entry');
    }

    public function login(Request $request)
    {
        // Must choose role first
        $selectedRole = $request->session()->get('staff_selected_role');
        if (!$selectedRole) {
            return redirect()->route('staff.entry');
        }

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
                'selected_role' => $selectedRole,
            ]);
            \App\Support\LoginHistory::record(null, $request, false, 'Invalid credentials (Staff)');

            return back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
        }

        $request->session()->regenerate();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('staff.entry');
        }

        // Block suspended / pending accounts (keep this)
        if (($user->account_status ?? 'active') !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->withErrors(['email' => 'Account is not active. Please contact the system administrator.']);
        }

        /**
         * ✅ REMOVED: approval blocking on login
         * You requested approval to be enforced at IT Admin account generation instead.
         */

        // ✅ Pull roles from DB (Spatie tables) — no trait methods needed
        $userRoles = $this->roleNamesForUser($user->id);

        // Check that user is staff
        $staffRoles = array_values(array_intersect($userRoles, $this->staffAllowedRoles()));
        if (count($staffRoles) === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->withErrors(['email' => 'Access denied. Not a staff account.']);
        }

        // ✅ Enforce selected role must be owned by user
        if (!in_array($selectedRole, $userRoles, true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.login')
                ->withErrors(['email' => 'You are not authorized for the selected staff role.']);
        }

        // Set active role for session & clear selection
        $request->session()->put('active_staff_role', $selectedRole);
        $request->session()->forget('staff_selected_role');

        \App\Support\AuditTrail::log('login_staff', $user, ['role' => $selectedRole]);
        \App\Support\LoginHistory::record($user, $request, true);

        return $this->redirectToRoleDashboard($selectedRole);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->forget(['active_staff_role', 'staff_selected_role']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.entry');
    }

    private function redirectAfterLogin($user, Request $request)
    {
        if (!$user) {
            return redirect()->route('staff.entry');
        }

        $userRoles = $this->roleNamesForUser($user->id);
        $staffRoles = array_values(array_intersect($userRoles, $this->staffAllowedRoles()));

        // 1. If user is NOT staff (e.g. an applicant reaching staff routes), log them out
        if (count($staffRoles) === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('staff.entry')
                ->withErrors(['email' => 'Access denied. Not a staff account.']);
        }

        // 2. Check if there's a pending selection (they chose a role at /staff while logged in)
        $selected = $request->session()->get('staff_selected_role');
        if ($selected && in_array($selected, $userRoles, true)) {
            $request->session()->put('active_staff_role', $selected);
            $request->session()->forget('staff_selected_role');
            return $this->redirectToRoleDashboard($selected);
        }

        // 3. If active role exists and user still has it -> go there
        $active = $request->session()->get('active_staff_role');
        if ($active && in_array($active, $userRoles, true)) {
            return $this->redirectToRoleDashboard($active);
        }

        // 4. Fallback: use first staff role if they reached here logged in but without selection
        if (count($staffRoles) > 0) {
            $first = $staffRoles[0];
            $request->session()->put('active_staff_role', $first);
            return $this->redirectToRoleDashboard($first);
        }

        // Otherwise force role selection
        return redirect()->route('staff.entry');
    }

    private function redirectToRoleDashboard(string $role)
    {
        return match ($role) {
            'super_admin'            => redirect()->route('admin.dashboard'),
            'accreditation_officer'  => redirect()->route('staff.officer.dashboard'),
            'registrar'              => redirect()->route('staff.registrar.dashboard'),
            'accounts_payments'      => redirect()->route('staff.accounts.dashboard'),
            'production'             => redirect()->route('staff.production.dashboard'),
            'it_admin'               => redirect()->route('staff.it.dashboard'),
            'auditor'                => redirect()->route('staff.auditor.dashboard'),
            'director'               => redirect()->route('staff.director.dashboard'),
            default                  => redirect()->route('staff.entry'),
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
