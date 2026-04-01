<?php

namespace App\Services\Director;

use App\Repositories\Director\UserRepository;
use App\Models\User;
use App\Models\Application;
use App\Models\ActivityLog;
use App\Models\PrintLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Staff Performance Service
 * 
 * Provides staff productivity metrics, processing time analysis, approval
 * distribution tracking, and activity monitoring for performance evaluation.
 * 
 * @package App\Services\Director
 */
class StaffPerformanceService
{
    /**
     * Create a new StaffPerformanceService instance.
     * 
     * @param UserRepository $userRepo Repository for user data queries
     */
    public function __construct(
        private UserRepository $userRepo
    ) {}

    /**
     * Get applications processed per officer.
     * 
     * Returns accreditation officers and registrars with counts of applications
     * they processed in the current month, sorted by count descending.
     * 
     * @return Collection Collection of User models with processed_count field
     */
    public function getApplicationsProcessedPerOfficer(): Collection
    {
        return User::role(['accreditation_officer', 'registrar'])
        ->withCount([
            'processedApplications as processed_count' => function($q) {
                $q->where('updated_at', '>=', now()->startOfMonth());
            }
        ])
        ->orderBy('processed_count', 'desc')
        ->get(['id', 'name', 'email']);
    }

    /**
     * Get average review time per registrar.
     * 
     * Calculates average hours spent in registrar review stage for each
     * registrar, based on applications processed in the current month.
     * 
     * @return Collection Collection of User models with avg_review_hours field,
     *                    sorted by review time descending
     */
    public function getAverageReviewTimePerRegistrar(): Collection
    {
        $registrars = User::whereHas('roles', function($query) {
            $query->where('name', 'registrar');
        })
        ->with(['assignedApplications' => function($q) {
            $q->whereNotNull('assigned_at')
              ->whereNotNull('registrar_approved_at')
              ->where('registrar_approved_at', '>=', now()->startOfMonth())
              ->select('id', 'assigned_officer_id', 'assigned_at', 'registrar_approved_at');
        }])
        ->get(['id', 'name', 'email']);
        
        return $registrars->map(function($registrar) {
            $apps = $registrar->assignedApplications;
            
            if ($apps->isEmpty()) {
                $registrar->avg_review_hours = 0;
                return $registrar;
            }
            
            $totalHours = 0;
            foreach ($apps as $app) {
                $totalHours += Carbon::parse($app->assigned_at)
                    ->diffInHours(Carbon::parse($app->registrar_approved_at));
            }
            
            $registrar->avg_review_hours = round($totalHours / $apps->count(), 1);
            unset($registrar->assignedApplications);
            
            return $registrar;
        })->sortByDesc('avg_review_hours');
    }

    /**
     * Get payment verification turnaround per staff.
     * 
     * Returns accounts staff with counts of payments they verified (applications
     * issued) in the current month, sorted by count descending.
     * 
     * @return Collection Collection of User models with verified_count field
     */
    public function getPaymentVerificationTurnaround(): Collection
    {
        return User::whereHas('roles', function($query) {
            $query->where('name', 'accounts');
        })
        ->withCount([
            'assignedApplications as verified_count' => function($q) {
                $q->where('status', 'issued')
                  ->where('issued_at', '>=', now()->startOfMonth());
            }
        ])
        ->orderBy('verified_count', 'desc')
        ->get(['id', 'name', 'email']);
    }

    /**
     * Get approval distribution per officer.
     * 
     * Returns accreditation officers with counts of approved and rejected
     * applications, including calculated approval rate percentage.
     * 
     * @return Collection Collection of User models with total_reviewed, approved_count,
     *                    rejected_count, and approval_rate fields, sorted by total reviewed
     */
    public function getApprovalDistributionPerOfficer(): Collection
    {
        return User::whereHas('roles', function($query) {
            $query->where('name', 'accreditation_officer');
        })
        ->withCount([
            'assignedApplications as total_reviewed' => function($q) {
                $q->whereIn('status', ['officer_approved', 'officer_rejected'])
                  ->where('last_action_at', '>=', now()->startOfMonth());
            },
            'assignedApplications as approved_count' => function($q) {
                $q->where('status', 'officer_approved')
                  ->where('last_action_at', '>=', now()->startOfMonth());
            },
            'assignedApplications as rejected_count' => function($q) {
                $q->where('status', 'officer_rejected')
                  ->where('last_action_at', '>=', now()->startOfMonth());
            }
        ])
        ->get(['id', 'name', 'email'])
        ->map(function($officer) {
            $officer->approval_rate = $officer->total_reviewed > 0
                ? round(($officer->approved_count / $officer->total_reviewed) * 100, 1)
                : 0;
            return $officer;
        })
        ->sortByDesc('total_reviewed');
    }

    /**
     * Get category reassignment frequency per staff.
     * 
     * Returns staff members who performed category reassignments in the current
     * month, with counts sorted by frequency descending.
     * 
     * @return Collection Collection of ActivityLog aggregates with user_id, user_role,
     *                    reassignment_count, and user relation
     */
    public function getReassignmentFrequencyPerStaff(): Collection
    {
        return ActivityLog::where('action', 'registrar_reassign_category')
            ->where('created_at', '>=', now()->startOfMonth())
            ->select('user_id', 'user_role', DB::raw('COUNT(*) as reassignment_count'))
            ->groupBy('user_id', 'user_role')
            ->with('user:id,name,email')
            ->orderBy('reassignment_count', 'desc')
            ->get();
    }

    /**
     * Get drill-down staff activity logs.
     * 
     * Retrieves up to 100 activity log entries for a specific staff member,
     * with optional filtering by action type and date range.
     * 
     * @param int $userId User ID of the staff member
     * @param array $filters Associative array of filter criteria:
     *                       - action_type: Filter by specific action
     *                       - date_from: Start date for created_at filter
     *                       - date_to: End date for created_at filter
     * @return Collection Collection of ActivityLog models with entity relation
     */
    public function getDrillDownStaffActivity(int $userId, array $filters): Collection
    {
        $query = ActivityLog::where('user_id', $userId)
            ->with(['entity']);
        
        if (isset($filters['action_type'])) {
            $query->where('action', $filters['action_type']);
        }
        
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['date_from']),
                Carbon::parse($filters['date_to'])
            ]);
        }
        
        return $query->orderBy('created_at', 'desc')->limit(100)->get();
    }

    /**
     * Get print actions by staff.
     * 
     * Returns staff members with counts of print and reprint actions performed
     * in the current month. Returns empty collection if PrintLog model unavailable.
     * 
     * @return Collection Collection of PrintLog aggregates with user_id, print_type,
     *                    print_count, and user relation, sorted by count descending
     */
    public function getPrintActionsByStaff(): Collection
    {
        if (!class_exists(PrintLog::class)) {
            return collect([]);
        }
        
        return PrintLog::where('printed_at', '>=', now()->startOfMonth())
            ->select('user_id', 'print_type', DB::raw('COUNT(*) as print_count'))
            ->groupBy('user_id', 'print_type')
            ->with('user:id,name,email')
            ->orderBy('print_count', 'desc')
            ->get();
    }
}
