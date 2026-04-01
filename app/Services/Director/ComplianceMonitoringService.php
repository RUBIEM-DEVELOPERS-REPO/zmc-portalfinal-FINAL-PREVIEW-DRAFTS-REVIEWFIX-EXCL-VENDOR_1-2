<?php

namespace App\Services\Director;

use App\Repositories\Director\ActivityLogRepository;
use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\PrintLog;
use App\Models\DocumentVersion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Compliance Monitoring Service
 * 
 * Provides audit trail analysis, compliance violation tracking, and suspicious
 * activity detection. Monitors category reassignments, manual overrides,
 * certificate edits, and excessive reprints.
 * 
 * @package App\Services\Director
 */
class ComplianceMonitoringService
{
    /**
     * Create a new ComplianceMonitoringService instance.
     * 
     * @param ActivityLogRepository $activityLogRepo Repository for activity log queries
     */
    public function __construct(
        private ActivityLogRepository $activityLogRepo
    ) {}

    /**
     * Get category reassignment statistics.
     * 
     * Returns total count and staff-level breakdown of category reassignments
     * performed since the start of the current month.
     * 
     * @return array Associative array containing:
     *               - total: Total count of category reassignments
     *               - by_staff: Collection of reassignments grouped by staff member
     */
    public function getCategoryReassignments(): array
    {
        $reassignments = $this->activityLogRepo->getByAction(
            'registrar_reassign_category',
            now()->startOfMonth(),
            now()
        );
        
        $byStaff = $this->activityLogRepo->getActionCountsByStaff('registrar_reassign_category');
        
        return [
            'total' => $reassignments->count(),
            'by_staff' => $byStaff,
        ];
    }

    /**
     * Get reopened applications statistics.
     * 
     * Returns total count and staff-level breakdown of applications that were
     * reopened after being closed, since the start of the current month.
     * 
     * @return array Associative array containing:
     *               - total: Total count of reopened applications
     *               - by_staff: Collection of reopenings grouped by staff member
     */
    public function getReopenedApplications(): array
    {
        $reopened = $this->activityLogRepo->getByAction(
            'application_reopened',
            now()->startOfMonth(),
            now()
        );
        
        $byStaff = $this->activityLogRepo->getActionCountsByStaff('application_reopened');
        
        return [
            'total' => $reopened->count(),
            'by_staff' => $byStaff,
        ];
    }

    /**
     * Get manual override statistics.
     * 
     * Returns total count and staff-level breakdown of manual payment overrides
     * and system overrides performed since the start of the current month.
     * 
     * @return array Associative array containing:
     *               - total: Total count of manual overrides
     *               - by_staff: Collection of overrides grouped by staff member
     */
    public function getManualOverrides(): array
    {
        $overrides = $this->activityLogRepo->getByAction(
            ['manual_payment_override', 'system_override'],
            now()->startOfMonth(),
            now()
        );
        
        $byStaff = ActivityLog::whereIn('action', ['manual_payment_override', 'system_override'])
            ->where('created_at', '>=', now()->startOfMonth())
            ->select('user_id', 'user_role', DB::raw('COUNT(*) as action_count'))
            ->groupBy('user_id', 'user_role')
            ->with('user:id,name,email')
            ->orderBy('action_count', 'desc')
            ->get();
        
        return [
            'total' => $overrides->count(),
            'by_staff' => $byStaff,
        ];
    }

    /**
     * Get certificate edit statistics.
     * 
     * Returns total count, staff-level breakdown, and most frequently edited
     * fields for certificate edits performed after approval.
     * 
     * @return array Associative array containing:
     *               - total: Total count of certificate edits
     *               - by_staff: Collection of edits grouped by staff member
     *               - most_edited_fields: Collection of most frequently edited fields
     */
    public function getCertificateEdits(): array
    {
        $edits = $this->activityLogRepo->getByAction(
            'certificate_edit_after_approval',
            now()->startOfMonth(),
            now()
        );
        
        $byStaff = $this->activityLogRepo->getActionCountsByStaff('certificate_edit_after_approval');
        
        // Get most edited fields from DocumentVersion model if it exists
        $mostEditedFields = collect([]);
        if (class_exists(DocumentVersion::class)) {
            $mostEditedFields = DocumentVersion::where('edited_at', '>=', now()->startOfMonth())
                ->select('field_name', DB::raw('COUNT(*) as edit_count'))
                ->groupBy('field_name')
                ->orderBy('edit_count', 'desc')
                ->limit(10)
                ->get();
        }
        
        return [
            'total' => $edits->count(),
            'by_staff' => $byStaff,
            'most_edited_fields' => $mostEditedFields,
        ];
    }

    /**
     * Get excessive reprint statistics.
     * 
     * Returns applications and staff members with excessive reprint activity,
     * based on configured threshold (default: 2 reprints).
     * 
     * @return array Associative array containing:
     *               - by_applicant: Collection of applications exceeding reprint threshold
     *               - by_staff: Collection of staff members with high reprint counts
     */
    public function getExcessiveReprints(): array
    {
        $threshold = config('director-dashboard.excessive_print_threshold', 2);
        
        $byApplicant = Application::where('print_count', '>', $threshold)
            ->with('applicant:id,name,email')
            ->select('id', 'application_number', 'applicant_id', 'print_count')
            ->orderBy('print_count', 'desc')
            ->limit(20)
            ->get();
        
        $byStaff = collect([]);
        if (class_exists(PrintLog::class)) {
            $byStaff = PrintLog::where('print_type', 'reprint')
                ->where('printed_at', '>=', now()->startOfMonth())
                ->select('user_id', DB::raw('COUNT(*) as reprint_count'))
                ->groupBy('user_id')
                ->with('user:id,name,email')
                ->orderBy('reprint_count', 'desc')
                ->get();
        }
        
        return [
            'by_applicant' => $byApplicant,
            'by_staff' => $byStaff,
        ];
    }

    /**
     * Get print vs reprint statistics.
     * 
     * Returns counts of initial prints versus reprints for the current month.
     * Returns zeros if PrintLog model is not available.
     * 
     * @return array Associative array containing:
     *               - total_prints: Count of initial certificate prints
     *               - total_reprints: Count of certificate reprints
     */
    public function getPrintStatistics(): array
    {
        if (!class_exists(PrintLog::class)) {
            return [
                'total_prints' => 0,
                'total_reprints' => 0,
            ];
        }
        
        $totalPrints = PrintLog::where('print_type', 'initial')
            ->where('printed_at', '>=', now()->startOfMonth())
            ->count();
        
        $totalReprints = PrintLog::where('print_type', 'reprint')
            ->where('printed_at', '>=', now()->startOfMonth())
            ->count();
        
        return [
            'total_prints' => $totalPrints,
            'total_reprints' => $totalReprints,
        ];
    }

    /**
     * Get suspicious activity alerts.
     * 
     * Returns counts of various suspicious activity patterns including failed
     * logins, repeated reassignments, high waiver frequency, and system overrides.
     * 
     * @return array Associative array containing:
     *               - failed_logins: Count of failed login attempts
     *               - repeated_reassignments: Count of users with >5 reassignments
     *               - high_waiver_frequency: Count of waivers approved
     *               - system_overrides: Count of system override actions
     */
    public function getSuspiciousActivityAlerts(): array
    {
        return $this->activityLogRepo->getSuspiciousActivityPatterns();
    }

    /**
     * Get drill-down audit trail.
     * 
     * Retrieves up to 100 activity log entries for a specific event type,
     * with optional filtering by user and date range.
     * 
     * @param string $eventType Activity log action type to filter by
     * @param array $filters Associative array of filter criteria:
     *                       - user_id: Filter by specific user
     *                       - date_from: Start date for created_at filter
     *                       - date_to: End date for created_at filter
     * @return Collection Collection of ActivityLog models with user and entity relations
     */
    public function getDrillDownAuditTrail(string $eventType, array $filters): Collection
    {
        $query = ActivityLog::where('action', $eventType)
            ->with(['user:id,name,email', 'entity']);
        
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
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
     * Get high-risk actions for dashboard display.
     * 
     * Returns the most recent high-risk actions from the past 7 days,
     * limited to the specified number of results.
     * 
     * @param int $limit Maximum number of results to return (default: 5)
     * @return Collection Collection of high-risk ActivityLog entries
     */
    public function getHighRiskActions(int $limit = 5): Collection
    {
        return $this->activityLogRepo->getHighRiskActions(now()->subDays(7))
            ->take($limit);
    }
}
