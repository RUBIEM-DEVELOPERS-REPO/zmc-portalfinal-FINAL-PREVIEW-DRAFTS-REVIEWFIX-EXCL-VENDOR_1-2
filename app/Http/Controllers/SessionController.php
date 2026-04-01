<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    /**
     * Extend the current session
     */
    public function extend(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Update last activity timestamp
        Session::put('last_activity', time());
        
        // Calculate new expiry time
        $timeout = config('session.lifetime') * 60;
        $newExpiry = time() + $timeout;
        
        // Log session extension for audit purposes
        if (class_exists('\App\Models\ActivityLog')) {
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'session_extended',
                    'description' => 'User extended their session',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Exception $e) {
                // Don't fail if logging fails
                \Log::warning('Failed to log session extension: ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Session extended successfully',
            'expires_at' => $newExpiry,
            'remaining_time' => $timeout
        ]);
    }
    
    /**
     * Get current session status
     */
    public function status(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'remaining_time' => 0
            ]);
        }
        
        $timeout = config('session.lifetime') * 60;
        $lastActivity = Session::get('last_activity', time());
        $remainingTime = $timeout - (time() - $lastActivity);
        
        return response()->json([
            'authenticated' => true,
            'remaining_time' => max(0, $remainingTime),
            'expires_at' => time() + $remainingTime,
            'warning_threshold' => 300 // 5 minutes
        ]);
    }
    
    /**
     * Force logout due to timeout
     */
    public function timeoutLogout(Request $request)
    {
        if (Auth::check()) {
            // Log timeout logout for audit purposes
            if (class_exists('\App\Models\ActivityLog')) {
                try {
                    \App\Models\ActivityLog::create([
                        'user_id' => Auth::id(),
                        'action' => 'session_timeout_logout',
                        'description' => 'User logged out due to session timeout',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to log timeout logout: ' . $e->getMessage());
                }
            }
            
            Auth::logout();
        }
        
        Session::flush();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out due to session timeout'
            ]);
        }
        
        return redirect()->route('login')->with('timeout_message', 'Your session has expired due to inactivity.');
    }
}