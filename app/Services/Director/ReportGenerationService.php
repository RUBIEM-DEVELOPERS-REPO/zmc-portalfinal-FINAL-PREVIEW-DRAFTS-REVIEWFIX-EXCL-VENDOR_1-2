<?php

namespace App\Services\Director;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Report Generation Service
 * 
 * Provides PDF and Excel report generation for all dashboard sections.
 * Supports 5 report types: monthly accreditation, revenue/financial,
 * compliance/audit, media house status, and operational performance.
 * 
 * @package App\Services\Director
 */
class ReportGenerationService
{
    /**
     * Create a new ReportGenerationService instance.
     * 
     * @param DashboardMetricsService $metricsService Service for executive KPIs
     * @param AccreditationAnalyticsService $accreditationService Service for accreditation analytics
     * @param FinancialAnalyticsService $financialService Service for financial analytics
     * @param ComplianceMonitoringService $complianceService Service for compliance monitoring
     * @param MediaHouseOversightService $mediaHouseService Service for media house oversight
     * @param StaffPerformanceService $staffService Service for staff performance metrics
     */
    public function __construct(
        private DashboardMetricsService $metricsService,
        private AccreditationAnalyticsService $accreditationService,
        private FinancialAnalyticsService $financialService,
        private ComplianceMonitoringService $complianceService,
        private MediaHouseOversightService $mediaHouseService,
        private StaffPerformanceService $staffService
    ) {}

    /**
     * Generate monthly accreditation report.
     * 
     * Creates a comprehensive report of accreditation metrics including monthly trends,
     * processing times, approval ratios, and category distribution.
     * 
     * @param string $format Report format: 'pdf' or 'excel'
     * @param array $params Report parameters:
     *                      - month: Month in Y-m format (default: current month)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File download response
     */
    public function generateMonthlyAccreditationReport(string $format, array $params)
    {
        $month = Carbon::createFromFormat('Y-m', $params['month'] ?? now()->format('Y-m'));
        
        $data = [
            'month' => $month->format('F Y'),
            'generated_at' => now()->format('d M Y H:i'),
            'monthly_trends' => $this->accreditationService->getMonthlyTrends(1),
            'processing_time' => $this->accreditationService->getProcessingTimeByStage(),
            'approval_ratio_category' => $this->accreditationService->getApprovalRatioByCategory(),
            'approval_ratio_residency' => $this->accreditationService->getApprovalRatioByResidency(),
            'category_distribution' => $this->accreditationService->getCategoryDistribution(),
        ];
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('staff.director.pdf.monthly-accreditation', $data);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('monthly-accreditation-report-' . $month->format('Y-m') . '.pdf');
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new \App\Exports\Director\MonthlyAccreditationExport($data),
                'monthly-accreditation-report-' . $month->format('Y-m') . '.xlsx'
            );
        }
    }

    /**
     * Generate revenue and financial report.
     * 
     * Creates a comprehensive financial report including revenue trends, payment
     * breakdowns, waiver statistics, and outstanding payment aging.
     * 
     * @param string $format Report format: 'pdf' or 'excel'
     * @param array $params Report parameters:
     *                      - start_date: Start date for report period (default: start of month)
     *                      - end_date: End date for report period (default: end of month)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File download response
     */
    public function generateRevenueFinancialReport(string $format, array $params)
    {
        $startDate = Carbon::parse($params['start_date'] ?? now()->startOfMonth());
        $endDate = Carbon::parse($params['end_date'] ?? now()->endOfMonth());
        
        $data = [
            'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
            'generated_at' => now()->format('d M Y H:i'),
            'revenue_trend' => $this->financialService->getMonthlyRevenueTrend(12),
            'revenue_by_service' => $this->financialService->getRevenueByServiceType(),
            'revenue_by_applicant' => $this->financialService->getRevenueByApplicantType(),
            'revenue_by_residency' => $this->financialService->getRevenueByResidency(),
            'revenue_by_method' => $this->financialService->getRevenueByPaymentMethod(),
            'waiver_statistics' => $this->financialService->getWaiverStatistics(),
            'outstanding_aging' => $this->financialService->getOutstandingPaymentsAging(),
        ];
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('staff.director.pdf.revenue-financial', $data);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('revenue-financial-report-' . now()->format('Y-m-d') . '.pdf');
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new \App\Exports\Director\RevenueFinancialExport($data),
                'revenue-financial-report-' . now()->format('Y-m-d') . '.xlsx'
            );
        }
    }

    /**
     * Generate compliance and audit report.
     * 
     * Creates a comprehensive compliance report including category reassignments,
     * reopened applications, manual overrides, certificate edits, excessive reprints,
     * and suspicious activity alerts.
     * 
     * @param string $format Report format: 'pdf' or 'excel'
     * @param array $params Report parameters:
     *                      - month: Month in Y-m format (default: current month)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File download response
     */
    public function generateComplianceAuditReport(string $format, array $params)
    {
        $month = Carbon::createFromFormat('Y-m', $params['month'] ?? now()->format('Y-m'));
        
        $data = [
            'month' => $month->format('F Y'),
            'generated_at' => now()->format('d M Y H:i'),
            'category_reassignments' => $this->complianceService->getCategoryReassignments(),
            'reopened_applications' => $this->complianceService->getReopenedApplications(),
            'manual_overrides' => $this->complianceService->getManualOverrides(),
            'certificate_edits' => $this->complianceService->getCertificateEdits(),
            'excessive_reprints' => $this->complianceService->getExcessiveReprints(),
            'print_statistics' => $this->complianceService->getPrintStatistics(),
            'suspicious_activity' => $this->complianceService->getSuspiciousActivityAlerts(),
        ];
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('staff.director.pdf.compliance-audit', $data);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('compliance-audit-report-' . $month->format('Y-m') . '.pdf');
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new \App\Exports\Director\ComplianceAuditExport($data),
                'compliance-audit-report-' . $month->format('Y-m') . '.xlsx'
            );
        }
    }

    /**
     * Generate media house status report.
     * 
     * Creates a comprehensive media house report including status counts, average
     * staff per house, houses exceeding thresholds, accreditations nearing expiry,
     * and high-risk non-renewals.
     * 
     * @param string $format Report format: 'pdf' or 'excel'
     * @param array $params Report parameters (currently unused, reserved for future filtering)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File download response
     */
    public function generateMediaHouseStatusReport(string $format, array $params)
    {
        $data = [
            'generated_at' => now()->format('d M Y H:i'),
            'status_counts' => $this->mediaHouseService->getMediaHouseStatusCounts(),
            'average_staff' => $this->mediaHouseService->getAverageStaffPerHouse(),
            'exceeding_thresholds' => $this->mediaHouseService->getHousesExceedingThresholds(),
            'nearing_expiry' => $this->mediaHouseService->getAccreditationsNearingExpiry(),
            'high_risk_renewals' => $this->mediaHouseService->getHighRiskNonRenewals(),
        ];
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('staff.director.pdf.mediahouse-status', $data);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('mediahouse-status-report-' . now()->format('Y-m-d') . '.pdf');
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new \App\Exports\Director\MediaHouseStatusExport($data),
                'mediahouse-status-report-' . now()->format('Y-m-d') . '.xlsx'
            );
        }
    }

    /**
     * Generate operational performance report.
     * 
     * Creates a comprehensive staff performance report including applications processed,
     * review times, payment turnaround, approval distribution, reassignment frequency,
     * and processing time by stage.
     * 
     * @param string $format Report format: 'pdf' or 'excel'
     * @param array $params Report parameters:
     *                      - month: Month in Y-m format (default: current month)
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File download response
     */
    public function generateOperationalPerformanceReport(string $format, array $params)
    {
        $month = Carbon::createFromFormat('Y-m', $params['month'] ?? now()->format('Y-m'));
        
        $data = [
            'month' => $month->format('F Y'),
            'generated_at' => now()->format('d M Y H:i'),
            'applications_processed' => $this->staffService->getApplicationsProcessedPerOfficer(),
            'review_times' => $this->staffService->getAverageReviewTimePerRegistrar(),
            'payment_turnaround' => $this->staffService->getPaymentVerificationTurnaround(),
            'approval_distribution' => $this->staffService->getApprovalDistributionPerOfficer(),
            'reassignment_frequency' => $this->staffService->getReassignmentFrequencyPerStaff(),
            'processing_time_by_stage' => $this->accreditationService->getProcessingTimeByStage(),
        ];
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('staff.director.pdf.operational-performance', $data);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('operational-performance-report-' . $month->format('Y-m') . '.pdf');
        }
        
        if ($format === 'excel') {
            return Excel::download(
                new \App\Exports\Director\OperationalPerformanceExport($data),
                'operational-performance-report-' . $month->format('Y-m') . '.xlsx'
            );
        }
    }
}
