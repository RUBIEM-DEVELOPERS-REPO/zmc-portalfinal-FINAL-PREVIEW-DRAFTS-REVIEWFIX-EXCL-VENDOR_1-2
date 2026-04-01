<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * EnforceWorkflowTransitions Middleware
 * 
 * Validates that all application status transitions follow the defined workflow rules.
 * This middleware intercepts requests that modify application status and validates transitions.
 */
class EnforceWorkflowTransitions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // This middleware is applied at the route level for specific actions
        // The actual validation happens in the service layer (StatusTransitionValidator)
        // This middleware just logs and monitors transitions
        
        $response = $next($request);
        
        // Log workflow transitions for audit trail
        if ($request->route() && $request->route()->hasParameter('application')) {
            $application = $request->route()->parameter('application');
            
            if ($application && method_exists($application, 'getOriginal')) {
                $originalStatus = $application->getOriginal('status');
                $newStatus = $application->status;
                
                if ($originalStatus !== $newStatus) {
                    Log::info('Workflow transition detected', [
                        'application_id' => $application->id,
                        'from_status' => $originalStatus,
                        'to_status' => $newStatus,
                        'user_id' => auth()->id(),
                        'role' => session('active_staff_role'),
                        'route' => $request->route()->getName(),
                    ]);
                }
            }
        }
        
        return $response;
    }
}
