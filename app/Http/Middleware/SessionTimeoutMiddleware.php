<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            $lastActivity = Session::get('last_activity', time());
            
            // Check if session has expired
            if (time() - $lastActivity > $timeout) {
                Auth::logout();
                Session::flush();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Session expired due to inactivity',
                        'timeout' => true
                    ], 401);
                }
                
                return redirect()->route('login')->with('timeout_message', 'Your session has expired due to inactivity. Please log in again.');
            }
            
            // Update last activity timestamp
            Session::put('last_activity', time());
            
            // Calculate remaining time for frontend
            $remainingTime = $timeout - (time() - $lastActivity);
            Session::put('session_remaining', $remainingTime);
        }
        
        return $next($request);
    }
}