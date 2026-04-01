<?php

namespace App\Repositories\Director;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Activity Log Repository
 * 
 * Provides data access methods for activity log and audit trail queries with
 * SQLite-compatible implementations. Supports filtering by action type, user,
 * and date range for compliance monitoring.
 * 
 * @package App\Repositories\Director
 */
class ActivityLogRepository
{
    /**
     * Get logs by action type.
     * 
     * Retrieves activity logs matching a single action or array of actions,
     * with optional date range filtering and related user data.
     * 
     * @param string|array $action Single action string or array of action types
     * @param Carbon|null $startDate Optional start date for filtering
     * @param Carbon|null $endDate Optional end date for filtering
     * @return Collection Collection of ActivityLog models with user relation,
     *                    ordered by created_at descending
     */
    public function getByAction($action, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = ActivityLog::query();
        
        if (is_array($action)) {
            $query->whereIn('action', $action);
        } else {
            $query->where('action', $action);
        }
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get logs by user.
     * 
     * Retrieves activity logs for a specific user with optional filtering
     * by action type and date range.
     * 
     * @param int $userId User ID to filter by
     * @param array $filters Optional filters:
     *                       - action: Filter by specific action type
     *                       - date_from: Start date for created_at filter
     *                       - date_to: End date for created_at filter
     * @return Collection Collection of ActivityLog models ordered by created_at descending
     */
    public function getByUser(int $userId, array $filters = []): Collection
    {
        $query = ActivityLog::where('user_id', $userId);
        
        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['date_from']),
                Carbon::parse($filters['date_to'])
            ]);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get high-risk actions.
     * 
     * Retrieves activity logs for predefined high-risk action types including
     * category reassignments, manual overrides, certificate edits, application
     * reopenings, system overrides, and excessive reprints.
     * 
     * @param Carbon|null $startDate Optional start date for filtering (default: no filter)
     * @return Collection Collection of ActivityLog models with user relation,
     *                    ordered by created_at descending
     */
    public function getHighRiskActions(?Carbon $startDate = null): Collection
    {
        $highRiskActions = [
            'registrar_reassign_category',
            'manual_payment_override',
            'certificate_edit_after_approval',
            'application_reopened',
            'system_override',
            'excessive_reprint'
        ];
        
        $query = ActivityLog::whereIn('action', $highRiskActions);
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        return $query->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get action counts by staff member.
     * 
     * Aggregates counts of a specific action type grouped by staff member
     * for the current month, with related user data.
     * 
     * SQLite Note: Uses standard GROUP BY and COUNT() which are fully
     * compatible with SQLite.
     * 
     * @param string $action Action type to count
     * @return Collection Collection with user_id, user_role, action_count, and
     *                    user relation, ordered by count descending
     */
    public function getActionCountsByStaff(string $action): Collection
    {
        return ActivityLog::select(
            'user_id',
            'user_role',
            DB::raw('COUNT(*) as action_count')
        )
        ->where('action', $action)
        ->where('created_at', '>=', now()->startOfMonth())
        ->groupBy('user_id', 'user_role')
        ->with('user:id,name,email')
        ->orderBy('action_count', 'desc')
        ->get();
    }

    /**
     * Get suspicious activity patterns.
     * 
     * Analyzes activity logs to detect suspicious patterns including failed
     * logins, repeated reassignments, high waiver frequency, and system overrides.
     * 
     * SQLite Note: Uses HAVING clause with COUNT() aggregation which is fully
     * supported by SQLite. The subquery for repeated_reassignments counts users
     * with more than 5 reassignments in the current month.
     * 
     * @return array Associative array containing:
     *               - failed_logins: Count of failed login attempts this month
     *               - repeated_reassignments: Count of users with >5 reassignments
     *               - high_waiver_frequency: Count of waivers approved this month
     *               - system_overrides: Count of system override actions this month
     */
    public function getSuspiciousActivityPatterns(): array
    {
        $startOfMonth = now()->startOfMonth();
        
        return [
            'failed_logins' => ActivityLog::where('action', 'failed_login')
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
            
            'repeated_reassignments' => ActivityLog::where('action', 'registrar_reassign_category')
                ->where('created_at', '>=', $startOfMonth)
                ->select('user_id', DB::raw('COUNT(*) as count'))
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 5')
                ->count(),
            
            'high_waiver_frequency' => ActivityLog::where('action', 'waiver_approved')
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
            
            'system_overrides' => ActivityLog::where('action', 'system_override')
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
        ];
    }
}
