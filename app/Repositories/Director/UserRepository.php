<?php

namespace App\Repositories\Director;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * User Repository
 * 
 * Provides data access methods for staff performance queries with SQLite-compatible
 * implementations. Supports application counts, processing times, and action counts
 * for performance monitoring.
 * 
 * @package App\Repositories\Director
 */
class UserRepository
{
    /**
     * Get staff with application counts.
     * 
     * Retrieves staff members (officers, registrars, accounts) with counts
     * of applications they processed in the current month.
     * 
     * SQLite Note: Uses withCount() which generates a subquery with COUNT().
     * This is fully compatible with SQLite.
     * 
     * @return Collection Collection of User models with processed_count field,
     *                    ordered by count descending
     */
    public function getStaffWithApplicationCounts(): Collection
    {
        return User::whereHas('roles', function($query) {
            $query->whereIn('name', ['accreditation_officer', 'registrar', 'accounts']);
        })
        ->withCount([
            'assignedApplications as processed_count' => function($q) {
                $q->whereNotNull('last_action_at')
                  ->where('last_action_at', '>=', now()->startOfMonth());
            }
        ])
        ->orderBy('processed_count', 'desc')
        ->get();
    }

    /**
     * Get staff with processing times.
     * 
     * Retrieves staff members (officers, registrars) with their assigned
     * applications from the current month, including timestamp data for
     * processing time calculations.
     * 
     * Note: Processing time calculations are performed in the service layer
     * using PHP for better precision and SQLite compatibility.
     * 
     * @return Collection Collection of User models with assignedApplications relation
     *                    containing assigned_at and last_action_at timestamps
     */
    public function getStaffWithProcessingTimes(): Collection
    {
        return User::whereHas('roles', function($query) {
            $query->whereIn('name', ['accreditation_officer', 'registrar']);
        })
        ->with(['assignedApplications' => function($q) {
            $q->whereNotNull('assigned_at')
              ->whereNotNull('last_action_at')
              ->where('last_action_at', '>=', now()->startOfMonth())
              ->select('id', 'assigned_officer_id', 'assigned_at', 'last_action_at');
        }])
        ->get();
    }

    /**
     * Get staff with action counts.
     * 
     * Retrieves staff members who performed a specific action in the current
     * month, with counts of how many times they performed that action.
     * 
     * SQLite Note: Uses withCount() with query constraints which generates
     * a subquery. This is fully compatible with SQLite.
     * 
     * @param string $action Action type to count (e.g., 'registrar_reassign_category')
     * @return Collection Collection of User models with action_count field,
     *                    ordered by count descending
     */
    public function getStaffWithActionCounts(string $action): Collection
    {
        return User::whereHas('activityLogs', function($query) use ($action) {
            $query->where('action', $action)
                  ->where('created_at', '>=', now()->startOfMonth());
        })
        ->withCount([
            'activityLogs as action_count' => function($q) use ($action) {
                $q->where('action', $action)
                  ->where('created_at', '>=', now()->startOfMonth());
            }
        ])
        ->orderBy('action_count', 'desc')
        ->get();
    }
}
