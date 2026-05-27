<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        $token = $request->query('_auth_token')
            ?? $request->input('_auth_token')
            ?? $request->header('X-Auth-Token');

        if ($token) {
            $data = Cache::get('login_token:' . $token);

            if ($data && is_array($data) && isset($data['user_id'])) {
                Auth::loginUsingId($data['user_id']);

                if ($request->hasSession() && $request->session()->isStarted()) {
                    if (isset($data['role'])) {
                        $request->session()->put('active_staff_role', $data['role']);
                    }
                    $request->session()->put('_current_auth_token', $token);
                }

                Cache::put('login_token:' . $token, $data, now()->addHours(8));
                $request->attributes->set('_token_authenticated', true);
            }
        }

        return $next($request);
    }
}
