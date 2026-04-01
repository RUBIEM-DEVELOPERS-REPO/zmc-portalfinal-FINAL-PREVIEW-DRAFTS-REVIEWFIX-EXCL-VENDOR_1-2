<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

/**
 * EnforceRoleBasedAccess Middleware
 * 
 * Enforces role-based access control for workflow actions.
 * Ensures users can only perform actions allowed for their role.
 */
class EnforceRoleBasedAccess
{
    /**
     * Role-based action permissions
     */
    protected array $permissions = [
        'applicant' => [
            'submit',
            'upload_payment',
            'view_status',
            'withdraw',
        ],
        'accreditation_officer' => [
            'review',
            'approve',
            'return',
            'forward_without_approval',
            'request_correction',
            'production',
            'generate_document',
            'mark_produced',
            'mark_issued',
        ],
        'registrar' => [
            'review',
            'raise_fix_request',
            'approve_media_house',
            'push_to_accounts',
            'view_payment_oversight', // READ-ONLY
        ],
        'accounts' => [
            'verify_payment',
            'reject_payment',
            'view_submissions',
        ],
        'director' => [
            'view_reports',
            'view_analytics',
            'view_oversight',
        ],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $requiredAction
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $requiredAction)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Authentication required.');
        }
        
        // Get active staff role from session
        $activeRole = session('active_staff_role');
        
        // Check if user has permission for this action
        if (!$this->hasPermission($activeRole, $requiredAction)) {
            abort(403, "You do not have permission to perform this action. Required: {$requiredAction}");
        }
        
        // Additional checks for specific actions
        if ($requiredAction === 'verify_payment' && $activeRole === 'registrar') {
            // Registrar cannot verify payments - only view
            abort(403, 'Registrar has read-only access to payment information.');
        }
        
        return $next($request);
    }

    /**
     * Check if role has permission for action
     *
     * @param string|null $role
     * @param string $action
     * @return bool
     */
    protected function hasPermission(?string $role, string $action): bool
    {
        if (!$role || !isset($this->permissions[$role])) {
            return false;
        }
        
        return in_array($action, $this->permissions[$role], true);
    }

    /**
     * Get allowed actions for role
     *
     * @param string $role
     * @return array
     */
    public static function getAllowedActions(string $role): array
    {
        $instance = new self();
        return $instance->permissions[$role] ?? [];
    }
}
