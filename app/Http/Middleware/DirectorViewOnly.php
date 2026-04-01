<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to enforce view-only access for Director role
 * 
 * Directors have oversight access and can:
 * - View all dashboard data
 * - Generate and download reports
 * 
 * Directors CANNOT:
 * - Edit application data
 * - Approve/reject applications
 * - Assign/reassign applications
 * - Generate certificates or cards
 * - Print or reprint documents
 * - Modify payment records
 * - Grant waivers
 * - Perform any operational actions
 */
class DirectorViewOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only apply to authenticated users with director role
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('director')) {
            return $next($request);
        }

        // Allow GET requests (viewing data)
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Allow POST requests only for report generation endpoints
        if ($request->isMethod('POST')) {
            $allowedPaths = [
                'staff/director/generate/monthly-accreditation',
                'staff/director/generate/revenue-financial',
                'staff/director/generate/compliance-audit',
                'staff/director/generate/mediahouse-status',
                'staff/director/generate/operational-performance',
            ];

            $path = $request->path();
            foreach ($allowedPaths as $allowedPath) {
                if (str_contains($path, $allowedPath)) {
                    return $next($request);
                }
            }
        }

        // Block all other non-GET requests (POST, PUT, PATCH, DELETE)
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'Directors have view-only access and cannot perform operational actions.',
            ], 403);
        }

        abort(403, 'Directors have view-only access and cannot perform operational actions.');
    }
}
