<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * IMPORTANT:
     * Public auth must always land on /home,
     * because /home decides portal routing using session('public_selected_portal').
     */
    protected $redirectTo = '/home';

    /**
     * Preserve the chosen portal (set on the public landing page) across
     * Laravel's session regeneration during login.
     */
    protected function sendLoginResponse(Request $request)
    {
        $portal = $request->session()->get('public_selected_portal');

        // default AuthenticatesUsers implementation (copied and extended)
        $request->session()->regenerate();

        if ($portal) {
            $request->session()->put('public_selected_portal', $portal);
        }

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? response()->json([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * Log failed login attempts for Super Admin visibility.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        \App\Support\AuditTrail::log('login_failed', null, [
            'portal' => 'public',
            'email' => (string)($request->input('email') ?? ''),
        ]);

        \App\Support\LoginHistory::record(null, $request, false, 'Invalid credentials');

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * After successful login:
     * Always send PUBLIC users to /home.
     * (Staff uses /staff/login, NOT this controller.)
     */
    protected function authenticated(Request $request, $user)
    {
        // Block non-active accounts
        if (($user->account_status ?? 'active') !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Account is not active. Please contact the system administrator.']);
        }

        // If a staff user logs in through public login, force them out (use /staff)
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole([
            'super_admin','accreditation_officer','accounts_payments','registrar','production','it_admin','auditor','director','oversight'
        ])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('staff.entry')->withErrors(['email' => 'Please login via the staff portal.']);
        }

        \App\Support\AuditTrail::log('login_applicant', $user);
        \App\Support\LoginHistory::record($user, $request, true);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
