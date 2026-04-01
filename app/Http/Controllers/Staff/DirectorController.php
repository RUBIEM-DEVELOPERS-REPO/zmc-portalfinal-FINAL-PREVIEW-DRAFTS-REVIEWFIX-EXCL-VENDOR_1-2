<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\Director\DashboardMetricsService;
use App\Services\Director\AccreditationAnalyticsService;
use App\Services\Director\FinancialAnalyticsService;
use App\Services\Director\ComplianceMonitoringService;
use App\Services\Director\MediaHouseOversightService;
use App\Services\Director\StaffPerformanceService;
use App\Services\Director\RiskIndicatorService;
use App\Services\Director\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DirectorController extends Controller
{
    public function __construct(
        private DashboardMetricsService $metricsService,
        private AccreditationAnalyticsService $accreditationService,
        private FinancialAnalyticsService $financialService,
        private ComplianceMonitoringService $complianceService,
        private MediaHouseOversightService $mediaHouseService,
        private StaffPerformanceService $staffService,
        private RiskIndicatorService $riskService,
        private ReportGenerationService $reportService
    ) {
        $this->middleware('auth');
        // Director can see everything; Registrar can see accreditation performance
        $this->middleware('role:director')->except(['accreditationPerformance']);
        $this->middleware('role:director,registrar')->only(['accreditationPerformance']);
    }
    /**
     * Executive Overview Dashboard
     * Requirements: 1.1-1.10
     */
    /**
     * Executive Overview Dashboard
     * Requirements: 1.1-1.10
     */
    public function dashboard(Request $request)
    {
        $activeTab = $request->get('tab', 'perf');
        
        // KPIs and risk indicators for main dashboard
        $kpis = $this->metricsService->getExecutiveKPIs();
        $riskIndicators = $this->riskService->getAllRiskIndicators();
        $highRiskActivity = $this->complianceService->getHighRiskActions(5);

        // Performance partial data (performance.blade.php)
        $ratios = $this->accreditationService->getApprovalRatioByApplicationType();
        $ratio = [
            'journalist' => $ratios['accreditation'] ?? 0,
            'mass_media' => $ratios['registration'] ?? 0,
        ];
        $categories = $this->accreditationService->getCategoryDistribution();
        
        // Get monthly trends for dashboard - ensure all 12 months have data
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }
        
        $trendsData = $this->accreditationService->getMonthlyTrends(12)->keyBy('month');
        
        // Fill in missing months with zeros
        $monthlyTrends = $months->map(function($month) use ($trendsData) {
            $data = $trendsData->get($month);
            return (object)[
                'month' => $month,
                'submitted' => $data->total_submitted ?? 0,
                'approved' => $data->total_approved ?? 0,
                'returned' => $data->total_returned ?? 0,
            ];
        });

        // Financial partial data (financial.blade.php)
        $aging = $this->financialService->getOutstandingPaymentsAging();
        $waivers = $this->financialService->getWaiverStatistics();
        $revenueTrendData = $this->financialService->getMonthlyRevenueTrend();
        $revenueTrend = $revenueTrendData['current_year'] ?? collect([]);
        $breakdown = [
            'service' => $this->financialService->getRevenueByServiceType()
        ];
        
        // Debug logging
        \Log::info('Director Dashboard Financial Data', [
            'revenue_trend_count' => $revenueTrend->count(),
            'service_breakdown_count' => $breakdown['service']->count(),
            'aging' => $aging,
            'waivers_count' => $waivers['count'],
        ]);

        // Compliance partial data (compliance.blade.php)
        $categoryReassignmentsStat = $this->complianceService->getCategoryReassignments();
        $reopenedApplicationsStat = $this->complianceService->getReopenedApplications();
        $manualOverridesStat = $this->complianceService->getManualOverrides();
        $certificateEditsStat = $this->complianceService->getCertificateEdits();
        $printStatistics = $this->complianceService->getPrintStatistics();
        $excessiveReprints = $this->complianceService->getExcessiveReprints();

        $auditSnapshot = [
            'category_reassignments' => $categoryReassignmentsStat['total'] ?? 0,
            'manual_payment_overrides' => $manualOverridesStat['total'] ?? 0,
            'certificate_edits' => $certificateEditsStat['total'] ?? 0,
            'reopened_applications' => $reopenedApplicationsStat['total'] ?? 0,
        ];

        $reprints = [
            'total' => ($printStatistics['total_prints'] ?? 0) + ($printStatistics['total_reprints'] ?? 0),
            'reprints' => $printStatistics['total_reprints'] ?? 0,
            'top_staff' => $excessiveReprints['by_staff'] ?? collect([]),
        ];
        
        // Debug logging
        \Log::info('Director Dashboard Compliance Data', [
            'audit_snapshot' => $auditSnapshot,
            'print_stats' => $printStatistics,
            'top_staff_count' => $reprints['top_staff']->count(),
        ]);

        // Issuance partial data (issuance.blade.php)
        $issuance = [
            'total_issued' => Application::where('status', 'issued')
                ->whereBetween('issued_at', [now()->startOfMonth(), now()])
                ->count(),
            'unprinted_approvals' => Application::where('status', 'issued')
                ->where(function($q) {
                    $q->whereNull('print_count')->orWhere('print_count', 0);
                })
                ->count(),
            'prints_vs_reprints' => [
                'prints' => $printStatistics['total_prints'] ?? 0,
                'reprints' => $printStatistics['total_reprints'] ?? 0,
            ],
        ];

        // Media house partial data (media_oversight.blade.php)
        $mediaHouses = $this->mediaHouseService->getMediaHouseStatusCounts();
        $averageStaffPerHouse = $this->mediaHouseService->getAverageStaffPerHouse();
        $housesExceedingThresholds = $this->mediaHouseService->getHousesExceedingThresholds();
        $accreditationsNearingExpiry = $this->mediaHouseService->getAccreditationsNearingExpiry();
        
        // Regional distribution (placeholder - no regional data in current schema)
        $regional = collect([]);
        
        // Debug logging
        \Log::info('Director Dashboard Entity & Issuance Data', [
            'media_houses' => $mediaHouses,
            'avg_staff' => $averageStaffPerHouse,
            'issuance' => $issuance,
            'houses_exceeding' => $housesExceedingThresholds->count(),
        ]);
        $regional = collect([]);

        // Staff performance partial data (staff_performance.blade.php)
        $staffPerformance = $this->staffService->getApplicationsProcessedPerOfficer()->map(function($user) {
            $user->processed_applications_count = $user->processed_count;
            return $user;
        });
        
        $applicationsProcessed = $staffPerformance; // for backward compatibility if needed
        $averageReviewTime = $this->staffService->getAverageReviewTimePerRegistrar();
        $approvalDistribution = $this->staffService->getApprovalDistributionPerOfficer();
 
        // Trend Analytics (Simplified for Director Overview)
        $trendRange = request()->get('trend_range', '12_months');
        $trendCutoff = now()->subMonths(12);
        $currentRangeLabel = 'Last 12 Months';

        switch ($trendRange) {
            case '30_days': $trendCutoff = now()->subDays(30); $currentRangeLabel = 'Last 30 Days'; break;
            case '90_days': $trendCutoff = now()->subDays(90); $currentRangeLabel = 'Last 90 Days'; break;
            case '6_months': $trendCutoff = now()->subMonths(6); $currentRangeLabel = 'Last 6 Months'; break;
            case 'this_year': $trendCutoff = now()->startOfYear(); $currentRangeLabel = 'This Year (' . date('Y') . ')'; break;
            case 'all_time': $trendCutoff = now()->subYears(10); $currentRangeLabel = 'All Time'; break;
        }

        $accreditationTrends = [];
        $registrationTrends = [];
        $trendLabels = [];

        try {
            $trends = Application::selectRaw("strftime('%Y-%m', created_at) as month, 
                SUM(CASE WHEN application_type = 'accreditation' THEN 1 ELSE 0 END) as acc_count,
                SUM(CASE WHEN application_type = 'registration' THEN 1 ELSE 0 END) as reg_count")
                ->where('created_at', '>=', $trendCutoff)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($trends as $t) {
                $trendLabels[] = date('M Y', strtotime($t->month . '-01'));
                $accreditationTrends[] = (int) $t->acc_count;
                $registrationTrends[] = (int) $t->reg_count;
            }
        } catch (\Exception $e) {}

        return view('staff.director.dashboard', compact(
            'kpis',
            'riskIndicators',
            'highRiskActivity',
            'activeTab',
            // Performance partial
            'ratio',
            'categories',
            'monthlyTrends',
            // Financial partial
            'aging',
            'waivers',
            'revenueTrend',
            'breakdown',
            // Compliance partial
            'auditSnapshot',
            'reprints',
            'printStatistics',
            // Issuance partial
            'issuance',
            // Media house partial
            'mediaHouses',
            'regional',
            'averageStaffPerHouse',
            'housesExceedingThresholds',
            'accreditationsNearingExpiry',
            // Staff performance partial
            'staffPerformance',
            'applicationsProcessed',
            'averageReviewTime',
            'approvalDistribution',
            // Trends
            'trendLabels', 'accreditationTrends', 'registrationTrends', 'currentRangeLabel'
        ));
    }

    /**
     * Accreditation Performance Report
     * Requirements: 2.1-2.6
     */
    public function accreditationPerformance()
    {
        // Get all monthly trends (combined)
        $monthlyTrendsData = $this->accreditationService->getMonthlyTrends(12);
        $monthlyTrends = $monthlyTrendsData; // Raw collection for table
        
        // Get separate trends for accreditations and registrations
        // Ensure we have data for all 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }
        
        $monthFormat = DB::getDriverName() === 'pgsql' ? "TO_CHAR(created_at, 'YYYY-MM')" : "strftime('%Y-%m', created_at)";

        // Accreditation trends (excluding registrations)
        $accreditationData = Application::selectRaw("$monthFormat as month")
            ->selectRaw('COUNT(*) as total_submitted')
            ->selectRaw("SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) as total_approved")
            ->where('application_type', '!=', 'registration')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy('month');
        
        // Registration trends
        $registrationData = Application::selectRaw("$monthFormat as month")
            ->selectRaw('COUNT(*) as total_submitted')
            ->selectRaw("SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) as total_approved")
            ->where('application_type', 'registration')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy('month');
        
        // Fill in missing months with zeros
        $accreditationTrends = $months->map(function($month) use ($accreditationData) {
            $data = $accreditationData->get($month);
            return (object) [
                'month' => $month,
                'total_submitted' => $data ? $data->total_submitted : 0,
                'total_approved' => $data ? $data->total_approved : 0,
            ];
        });
        
        $registrationTrends = $months->map(function($month) use ($registrationData) {
            $data = $registrationData->get($month);
            return (object) [
                'month' => $month,
                'total_submitted' => $data ? $data->total_submitted : 0,
                'total_approved' => $data ? $data->total_approved : 0,
            ];
        });
        
        $processingTimeByStage = $this->accreditationService->getProcessingTimeByStage();
        $approvalRatioByCategory = $this->accreditationService->getApprovalRatioByCategory();
        $categoryDistribution = $this->accreditationService->getCategoryDistribution();
        
        // Additional detailed data for report page
        $efficiency = [
            'officer' => $processingTimeByStage['officer'] ?? 0,
            'registrar' => $processingTimeByStage['registrar'] ?? 0,
            'accounts' => $processingTimeByStage['accounts'] ?? 0,
        ];
        
        $categories = $categoryDistribution;
        
        // Get media house registrations count
        $mediaHouseRegistrations = Application::where('application_type', 'registration')
            ->whereIn('status', ['issued', 'pending_payment', 'officer_review', 'registrar_review'])
            ->count();
        
        return view('staff.director.reports.accreditation', compact(
            'monthlyTrends',
            'accreditationTrends',
            'registrationTrends',
            'months',
            'efficiency',
            'categories',
            'approvalRatioByCategory',
            'mediaHouseRegistrations'
        ));
    }

    /**
     * Financial Performance Report
     * Requirements: 3.1-3.8
     */
    public function financialOverview()
    {
        $monthlyRevenueTrendData = $this->financialService->getMonthlyRevenueTrend();
        $monthlyRevenueTrend = $monthlyRevenueTrendData['current_year'] ?? collect([]);
        $previousYearTrend = $monthlyRevenueTrendData['previous_year'] ?? collect([]);
        
        $revenueByService = $this->financialService->getRevenueByServiceType();
        $revenueByType = $this->financialService->getRevenueByApplicantType();
        $revenueByResidency = $this->financialService->getRevenueByResidency();
        $revenueByPaymentMethod = $this->financialService->getRevenueByPaymentMethod();
        $waiverStatistics = $this->financialService->getWaiverStatistics();
        $aging = $this->financialService->getOutstandingPaymentsAging();
        
        // Transform waivers data to match view expectations
        $waivers = (object) [
            'count' => $waiverStatistics['count'],
            'waived_amount' => $waiverStatistics['total_value'],
        ];
        
        return view('staff.director.reports.financial', compact(
            'monthlyRevenueTrend',
            'previousYearTrend',
            'revenueByService',
            'revenueByType',
            'revenueByResidency',
            'revenueByPaymentMethod',
            'waivers',
            'aging'
        ));
    }

    /**
     * Compliance and Risk Monitoring
     * Requirements: 4.1-4.12
     */
    public function complianceRisk()
    {
        $categoryReassignments = $this->complianceService->getCategoryReassignments();
        $reopenedApplications = $this->complianceService->getReopenedApplications();
        $manualOverrides = $this->complianceService->getManualOverrides();
        $certificateEdits = $this->complianceService->getCertificateEdits();
        $excessiveReprints = $this->complianceService->getExcessiveReprints();
        $printStatistics = $this->complianceService->getPrintStatistics();
        $suspiciousActivity = $this->complianceService->getSuspiciousActivityAlerts();
        
        // Transform data to match view expectations
        $risks = [
            'category_reassignments' => $categoryReassignments['total'],
            'certificate_edits' => $certificateEdits['total'],
            'reprints_above_threshold' => Application::where('print_count', '>', 1)->count(),
        ];
        
        $suspicious = $suspiciousActivity;
        
        return view('staff.director.reports.compliance', compact(
            'categoryReassignments',
            'reopenedApplications',
            'manualOverrides',
            'certificateEdits',
            'excessiveReprints',
            'printStatistics',
            'suspiciousActivity',
            'risks',
            'suspicious'
        ));
    }

    /**
     * Media House Oversight
     * Requirements: 5.1-5.7
     */
    public function mediaHouseOversight()
    {
        $statusCounts = $this->mediaHouseService->getMediaHouseStatusCounts();
        $averageStaffPerHouse = $this->mediaHouseService->getAverageStaffPerHouse();
        $housesExceedingThresholds = $this->mediaHouseService->getHousesExceedingThresholds();
        $accreditationsNearingExpiry = $this->mediaHouseService->getAccreditationsNearingExpiry();
        $highRiskNonRenewals = $this->mediaHouseService->getHighRiskNonRenewals();
        
        // Get detailed status breakdown
        $statusBreakdown = Application::where('application_type', 'registration')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        return view('staff.director.reports.mediahouses', compact(
            'statusCounts',
            'statusBreakdown',
            'averageStaffPerHouse',
            'housesExceedingThresholds',
            'accreditationsNearingExpiry',
            'highRiskNonRenewals'
        ));
    }

    /**
     * Staff Performance Metrics
     * Requirements: 7.1-7.6
     */
    public function staffPerformance()
    {
        $applicationsProcessed = $this->staffService->getApplicationsProcessedPerOfficer();
        $averageReviewTime = $this->staffService->getAverageReviewTimePerRegistrar();
        $paymentVerificationTurnaround = $this->staffService->getPaymentVerificationTurnaround();
        $approvalDistribution = $this->staffService->getApprovalDistributionPerOfficer();
        $reassignmentFrequency = $this->staffService->getReassignmentFrequencyPerStaff();
        
        // Add performance variable for the view
        $performance = $applicationsProcessed;
        
        return view('staff.director.reports.staff', compact(
            'applicationsProcessed',
            'performance',
            'averageReviewTime',
            'paymentVerificationTurnaround',
            'approvalDistribution',
            'reassignmentFrequency'
        ));
    }

    /**
     * Issuance and Print Oversight
     * Requirements: 8.1-8.5
     */
    public function issuanceOversight()
    {
        $printStatistics = $this->complianceService->getPrintStatistics();
        $printsByStaff = $this->staffService->getPrintActionsByStaff();
        
        return view('staff.director.reports.issuance', compact(
            'printStatistics',
            'printsByStaff'
        ));
    }

    /**
     * Reports and Downloads Page
     * Requirements: 10.1-10.8, 12.1
     */
    public function reportsDownloads()
    {
        return view('staff.director.reports.downloads');
    }

    /**
     * Generate Monthly Accreditation Report
     * Requirements: 10.1, 10.6, 10.7, 10.8
     */
    public function generateMonthlyAccreditationReport(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $month = $request->input('month', now()->format('Y-m'));
        
        return $this->reportService->generateMonthlyAccreditationReport($format, [
            'month' => $month
        ]);
    }

    /**
     * Generate Revenue and Financial Report
     * Requirements: 10.2, 10.6, 10.7, 10.8
     */
    public function generateRevenueFinancialReport(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        return $this->reportService->generateRevenueFinancialReport($format, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    /**
     * Generate Compliance and Audit Report
     * Requirements: 10.3, 10.6, 10.7, 10.8
     */
    public function generateComplianceAuditReport(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $month = $request->input('month', now()->format('Y-m'));
        
        return $this->reportService->generateComplianceAuditReport($format, [
            'month' => $month
        ]);
    }

    /**
     * Generate Media House Status Report
     * Requirements: 10.4, 10.6, 10.7, 10.8
     */
    public function generateMediaHouseStatusReport(Request $request)
    {
        $format = $request->input('format', 'pdf');
        
        return $this->reportService->generateMediaHouseStatusReport($format, []);
    }

    /**
     * Generate Operational Performance Report
     * Requirements: 10.5, 10.6, 10.7, 10.8
     */
    public function generateOperationalPerformanceReport(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $month = $request->input('month', now()->format('Y-m'));
        
        return $this->reportService->generateOperationalPerformanceReport($format, [
            'month' => $month
        ]);
    }

    /**
     * Geographic Distribution
     * Requirements: 9.1-9.5
     */
    public function geographicDistribution()
    {
        // Placeholder - geographic data not available in current schema
        return view('staff.director.reports.geographic', [
            'accreditationsByRegion' => collect([]),
            'revenueByRegion' => collect([]),
            'processingTimeByRegion' => collect([]),
            'mediaHousesByRegion' => collect([]),
        ]);
    }
}
