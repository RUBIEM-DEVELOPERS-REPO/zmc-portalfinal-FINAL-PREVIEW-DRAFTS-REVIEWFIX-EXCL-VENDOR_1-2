# Implementation Plan: Director/CEO Dashboard Enhancement

## Overview

This implementation plan breaks down the Director/CEO Dashboard enhancement into discrete coding tasks. The feature adds comprehensive strategic oversight capabilities with 11 KPIs, 8 report sections, risk indicators, drill-down navigation, and export functionality. All implementation uses PHP/Laravel with SQLite-compatible queries, Chart.js visualizations, and strict view-only access control.

## Tasks

- [x] 1. Foundation and Configuration Setup
  - [x] 1.1 Create configuration file for dashboard settings
    - Create `config/director-dashboard.php` with risk thresholds, cache TTL, chart colors, and operational thresholds
    - _Requirements: 6.1-6.8, 14.6_
  
  - [x] 1.2 Create database migration for performance indexes
    - Add indexes on applications table: status, application_type, accreditation_category_code, residency_type, media_house_id
    - Add indexes on payments table: status, service_type, applicant_category, payment_method
    - Add indexes on activity_logs table: action, user_id, created_at
    - Add indexes on print_logs table: application_id, print_type, printed_at
    - _Requirements: 13.1-13.4, 14.3-14.4_
  
  - [x] 1.3 Create directory structure for services, repositories, and exports
    - Create `app/Services/Director/` directory
    - Create `app/Repositories/Director/` directory
    - Create `app/Exports/Director/` directory
    - Create `resources/views/staff/director/reports/` directory
    - Create `resources/views/staff/director/partials/` directory
    - Create `resources/views/staff/director/pdf/` directory
    - _Requirements: 12.1_

- [x] 2. Repository Layer Implementation
  - [x] 2.1 Create ApplicationRepository with SQLite-compatible queries
    - Implement getByStatus(), getSubmittedInRange(), getIssuedInRange()
    - Implement getMonthlyApplicationCounts() using strftime() for SQLite
    - Implement getAverageProcessingTime() with PHP-based calculation
    - Implement getByCategory(), getWithExcessivePrints(), getNearingExpiry()
    - _Requirements: 1.1-1.10, 2.1-2.6, 13.1-13.4_
  
  - [x] 2.2 Create PaymentRepository with revenue aggregation methods
    - Implement getInRange(), getRevenueByServiceType(), getRevenueByApplicantCategory()
    - Implement getRevenueByPaymentMethod(), getMonthlyRevenueTrend() using strftime()
    - Implement getOutstandingPaymentsAging() with date-based bucketing
    - _Requirements: 3.1-3.8, 13.1-13.4_
  
  - [x] 2.3 Create ActivityLogRepository for audit trail queries
    - Implement getByAction(), getByUser(), getHighRiskActions()
    - Implement getActionCountsByStaff(), getSuspiciousActivityPatterns()
    - _Requirements: 4.1-4.12, 15.1-15.4_
  
  - [x] 2.4 Create UserRepository for staff performance queries
    - Implement getStaffWithApplicationCounts(), getStaffWithProcessingTimes()
    - Implement getStaffWithActionCounts()
    - _Requirements: 7.1-7.6_

- [x] 3. Service Layer - Core Metrics
  - [x] 3.1 Create DashboardMetricsService for executive KPIs
    - Implement getExecutiveKPIs() aggregating all 11 top-level metrics
    - Implement getTotalActiveAccreditations(), getIssuedThisMonth(), getIssuedYearToDate()
    - Implement getRevenueMTD(), getRevenueYTD(), getOutstandingRevenue()
    - Implement getApplicationsInPipeline(), getAverageProcessingTime()
    - Implement getApprovalRate(), getActiveComplianceFlags(), getTotalMediaHouses()
    - Add caching with 1-hour TTL for KPI data
    - _Requirements: 1.1-1.10, 14.1-14.6_
  
  - [ ]* 3.2 Write property test for DashboardMetricsService
    - **Property 1: KPI non-negativity** - All KPI values must be non-negative numbers
    - **Validates: Requirements 1.1-1.10**
  
  - [ ]* 3.3 Write unit tests for DashboardMetricsService edge cases
    - Test behavior with zero applications, zero payments, empty database
    - Test date boundary conditions (month/year transitions)
    - _Requirements: 1.1-1.10_

- [x] 4. Service Layer - Accreditation Analytics
  - [x] 4.1 Create AccreditationAnalyticsService for trend analysis
    - Implement getMonthlyTrends() for 12-month application volumes
    - Implement getProcessingTimeByStage() calculating officer, registrar, accounts averages
    - Implement getApprovalRatioByCategory() and getApprovalRatioByResidency()
    - Implement getCategoryDistribution() with trend indicators
    - Implement getDrillDownApplications() with filtering support
    - Implement getMonthlyTrendsChartData() in Chart.js format
    - _Requirements: 2.1-2.6, 13.1-13.4_
  
  - [ ]* 4.2 Write property test for AccreditationAnalyticsService
    - **Property 2: Approval ratio bounds** - Approval ratios must be between 0 and 100 percent
    - **Validates: Requirements 2.3, 2.4**
  
  - [ ]* 4.3 Write unit tests for trend calculation edge cases
    - Test with single month data, test with no approved applications
    - _Requirements: 2.1-2.6_

- [x] 5. Service Layer - Financial Analytics
  - [x] 5.1 Create FinancialAnalyticsService for revenue analysis
    - Implement getMonthlyRevenueTrend() with year-over-year comparison
    - Implement getRevenueByServiceType(), getRevenueByApplicantType()
    - Implement getRevenueByResidency(), getRevenueByPaymentMethod()
    - Implement getWaiverStatistics() with count, value, and breakdowns
    - Implement getOutstandingPaymentsAging() with 0-30, 30-60, 60+ buckets
    - Implement getDrillDownPayments() with filtering
    - _Requirements: 3.1-3.8, 13.1-13.4_
  
  - [ ]* 5.2 Write property test for FinancialAnalyticsService
    - **Property 3: Revenue sum consistency** - Sum of revenue breakdowns must equal total revenue
    - **Validates: Requirements 3.2-3.5**
  
  - [ ]* 5.3 Write unit tests for waiver and aging calculations
    - Test waiver statistics with zero waivers, test aging buckets
    - _Requirements: 3.6, 3.7_

- [ ] 6. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 7. Service Layer - Compliance and Risk
  - [x] 7.1 Create ComplianceMonitoringService for audit tracking
    - Implement getCategoryReassignments() with staff attribution
    - Implement getReopenedApplications() with staff attribution
    - Implement getManualOverrides() with staff attribution
    - Implement getCertificateEdits() with most-edited fields
    - Implement getExcessiveReprints() by applicant and staff
    - Implement getPrintStatistics() for print vs reprint ratio
    - Implement getSuspiciousActivityAlerts() for 4 alert types
    - Implement getDrillDownAuditTrail() with event type filtering
    - _Requirements: 4.1-4.12, 15.1-15.4_
  
  - [ ]* 7.2 Write property test for ComplianceMonitoringService
    - **Property 4: Audit trail immutability** - Audit data must never be modified, only read
    - **Validates: Requirements 15.1-15.4**
  
  - [x] 7.3 Create RiskIndicatorService for threshold evaluation
    - Implement getAllRiskIndicators() returning all 7 risk assessments
    - Implement evaluateExcessiveWaivers() with green/amber/red thresholds
    - Implement evaluateRejectionSpike() with percentage thresholds
    - Implement evaluateProcessingTimeSLA() with day thresholds
    - Implement evaluateRevenueDrop() with percentage thresholds
    - Implement evaluateReprintFrequency() with count thresholds
    - Implement evaluateCategoryReassignment() with count thresholds
    - Implement evaluatePaymentDelay() with day thresholds
    - Implement getRiskLevelColor() returning CSS classes
    - _Requirements: 6.1-6.8, 12.4_
  
  - [ ]* 7.4 Write property test for RiskIndicatorService
    - **Property 5: Risk level consistency** - Risk levels must match configured thresholds
    - **Validates: Requirements 6.1-6.8**

- [x] 8. Service Layer - Media House and Staff
  - [x] 8.1 Create MediaHouseOversightService for organizational monitoring
    - Implement getMediaHouseStatusCounts() for active/suspended/new counts
    - Implement getAverageStaffPerHouse() calculation
    - Implement getHousesExceedingThresholds() using config threshold
    - Implement getAccreditationsNearingExpiry() with 30-day window
    - Implement getHighRiskNonRenewals() identification
    - Implement getDrillDownMediaHouseDetails() with staff list
    - _Requirements: 5.1-5.7_
  
  - [ ]* 8.2 Write property test for MediaHouseOversightService
    - **Property 6: Average staff calculation** - Average must be between min and max staff counts
    - **Validates: Requirements 5.3**
  
  - [x] 8.3 Create StaffPerformanceService for productivity metrics
    - Implement getApplicationsProcessedPerOfficer() with counts
    - Implement getAverageReviewTimePerRegistrar() with time calculations
    - Implement getPaymentVerificationTurnaround() per staff
    - Implement getApprovalDistributionPerOfficer() with percentages
    - Implement getReassignmentFrequencyPerStaff() with counts
    - Implement getDrillDownStaffActivity() with filtering
    - _Requirements: 7.1-7.6_
  
  - [ ]* 8.4 Write property test for StaffPerformanceService
    - **Property 7: Processing time non-negativity** - All time calculations must be non-negative
    - **Validates: Requirements 7.2, 7.3**

- [x] 9. Service Layer - Report Generation
  - [x] 9.1 Create ReportGenerationService for PDF and Excel exports
    - Implement generateMonthlyAccreditationReport() supporting PDF and Excel
    - Implement generateRevenueFinancialReport() supporting PDF and Excel
    - Implement generateComplianceAuditReport() supporting PDF and Excel
    - Implement generateMediaHouseStatusReport() supporting PDF and Excel
    - Implement generateOperationalPerformanceReport() supporting PDF and Excel
    - Add timestamp and data period to all reports
    - _Requirements: 10.1-10.8_
  
  - [x] 9.2 Create Laravel Excel export classes
    - Create MonthlyAccreditationExport in `app/Exports/Director/`
    - Create RevenueFinancialExport in `app/Exports/Director/`
    - Create ComplianceAuditExport in `app/Exports/Director/`
    - Create MediaHouseStatusExport in `app/Exports/Director/`
    - Create OperationalPerformanceExport in `app/Exports/Director/`
    - _Requirements: 10.7_
  
  - [ ]* 9.3 Write unit tests for report generation
    - Test PDF generation, test Excel generation, test timestamp inclusion
    - _Requirements: 10.6-10.8_

- [ ] 10. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 11. Controller Enhancement
  - [x] 11.1 Enhance DirectorController with dependency injection
    - Add constructor with all 8 service class dependencies
    - Add middleware for staff.portal and role:director
    - _Requirements: 11.1-11.9, 12.1_
  
  - [x] 11.2 Implement dashboard() method for executive overview
    - Call metricsService.getExecutiveKPIs()
    - Call riskService.getAllRiskIndicators()
    - Call complianceService.getHighRiskActions()
    - Return view with KPIs, risk indicators, and high-risk activity
    - _Requirements: 1.1-1.10, 6.1-6.8_
  
  - [x] 11.3 Implement accreditationPerformance() method
    - Call accreditationService methods for trends, processing times, ratios, distribution
    - Return view with chart data
    - _Requirements: 2.1-2.6_
  
  - [x] 11.4 Implement financialOverview() method
    - Call financialService methods for revenue trends and breakdowns
    - Return view with financial data
    - _Requirements: 3.1-3.8_
  
  - [x] 11.5 Implement complianceRisk() method
    - Call complianceService methods for reassignments, overrides, edits, reprints, alerts
    - Return view with compliance data
    - _Requirements: 4.1-4.12_
  
  - [x] 11.6 Implement mediaHouseOversight() method
    - Call mediaHouseService methods for status, thresholds, expiry, renewals
    - Return view with media house data
    - _Requirements: 5.1-5.7_
  
  - [x] 11.7 Implement staffPerformance() method
    - Call staffService methods for processing, review times, approvals, reassignments
    - Return view with staff metrics
    - _Requirements: 7.1-7.6_
  
  - [x] 11.8 Implement issuanceOversight() method
    - Call accreditationService and staffService for issuance and print data
    - Return view with issuance metrics
    - _Requirements: 8.1-8.5_
  
  - [x] 11.9 Implement geographicDistribution() method
    - Call accreditationService, financialService, mediaHouseService for regional data
    - Return view with geographic breakdowns
    - _Requirements: 9.1-9.5_
  
  - [x] 11.10 Implement reportsDownloads() method
    - Return view with report generation form
    - _Requirements: 10.1-10.8, 12.1_
  
  - [x] 11.11 Implement report generation methods (5 methods)
    - Implement generateMonthlyAccreditationReport() with format and month parameters
    - Implement generateRevenueFinancialReport() with format and date range parameters
    - Implement generateComplianceAuditReport() with format and month parameters
    - Implement generateMediaHouseStatusReport() with format parameter
    - Implement generateOperationalPerformanceReport() with format and date range parameters
    - _Requirements: 10.1-10.8_
  
  - [ ]* 11.12 Write integration tests for controller methods
    - Test each controller method returns correct view with expected data
    - Test view-only access control prevents operational actions
    - _Requirements: 11.1-11.9_

- [x] 12. Route Definitions
  - [x] 12.1 Add routes to web.php for director dashboard
    - Add GET /staff/director/dashboard route to dashboard() method
    - Add GET /staff/director/accreditation route to accreditationPerformance()
    - Add GET /staff/director/financial route to financialOverview()
    - Add GET /staff/director/compliance route to complianceRisk()
    - Add GET /staff/director/mediahouses route to mediaHouseOversight()
    - Add GET /staff/director/staff route to staffPerformance()
    - Add GET /staff/director/issuance route to issuanceOversight()
    - Add GET /staff/director/geographic route to geographicDistribution()
    - Add GET /staff/director/reports route to reportsDownloads()
    - Add POST /staff/director/reports/accreditation route to generateMonthlyAccreditationReport()
    - Add POST /staff/director/reports/financial route to generateRevenueFinancialReport()
    - Add POST /staff/director/reports/compliance route to generateComplianceAuditReport()
    - Add POST /staff/director/reports/mediahouse route to generateMediaHouseStatusReport()
    - Add POST /staff/director/reports/operational route to generateOperationalPerformanceReport()
    - Group all routes with staff.portal and role:director middleware
    - _Requirements: 11.1-11.9, 12.1-12.2_

- [x] 13. View Components - Reusable Partials
  - [x] 13.1 Create kpi-card.blade.php partial component
    - Accept parameters: title, value, icon, trend, color
    - Display KPI with icon, value, and optional trend indicator
    - _Requirements: 1.1-1.10, 12.3_
  
  - [x] 13.2 Create risk-indicator.blade.php partial component
    - Accept parameters: title, status, level (green/amber/red), value, threshold
    - Display risk indicator with color-coded badge
    - _Requirements: 6.1-6.8, 12.4_
  
  - [x] 13.3 Create chart-container.blade.php partial component
    - Accept parameters: chartId, chartType, title, height
    - Provide canvas element for Chart.js rendering
    - _Requirements: 2.1-2.6, 3.1-3.8, 12.3_
  
  - [x] 13.4 Create drill-down-modal.blade.php partial component
    - Accept parameters: modalId, title, tableHeaders, dataUrl
    - Provide Bootstrap modal with AJAX-loaded table for drill-down
    - _Requirements: 2.6, 3.8, 4.12, 5.7, 7.6, 8.5, 9.5, 12.5_

- [x] 14. View Implementation - Main Dashboard
  - [x] 14.1 Enhance dashboard.blade.php for executive overview
    - Display 11 KPI cards using kpi-card partial
    - Display 7 risk indicators using risk-indicator partial
    - Display recent high-risk activity table (5 most recent)
    - Add AJAX polling for real-time KPI updates
    - _Requirements: 1.1-1.10, 6.1-6.8, 14.1-14.3_
  
  - [ ]* 14.2 Write browser test for dashboard loading performance
    - Test dashboard loads within 3 seconds
    - _Requirements: 14.3_

- [x] 15. View Implementation - Report Sections
  - [x] 15.1 Create accreditation.blade.php report view
    - Display monthly trends line chart using Chart.js
    - Display processing time by stage bar chart
    - Display approval ratio by category pie chart
    - Display approval ratio by residency bar chart
    - Display category distribution with trend indicators
    - Add drill-down modals for detailed application lists
    - _Requirements: 2.1-2.6, 12.3, 12.5_
  
  - [x] 15.2 Create financial.blade.php report view
    - Display monthly revenue trend line chart with YoY comparison
    - Display revenue by service type pie chart
    - Display revenue by applicant type bar chart
    - Display revenue by residency bar chart
    - Display revenue by payment method pie chart
    - Display waiver statistics table with breakdowns
    - Display outstanding payments aging bar chart
    - Add drill-down modals for transaction details
    - _Requirements: 3.1-3.8, 12.3, 12.5_
  
  - [x] 15.3 Create compliance.blade.php report view
    - Display category reassignments table with staff attribution
    - Display reopened applications table with staff attribution
    - Display manual overrides table with staff attribution
    - Display certificate edits table with most-edited fields
    - Display excessive reprints table by applicant and staff
    - Display print vs reprint statistics
    - Display suspicious activity alerts with color coding
    - Add drill-down modals for full audit trails
    - _Requirements: 4.1-4.12, 12.3, 12.5_
  
  - [x] 15.4 Create mediahouses.blade.php report view
    - Display media house status counts with cards
    - Display average staff per house metric
    - Display houses exceeding thresholds table
    - Display accreditations nearing expiry table
    - Display high-risk non-renewals table
    - Add drill-down modals for media house details
    - _Requirements: 5.1-5.7, 12.3, 12.5_
  
  - [x] 15.5 Create staff.blade.php report view
    - Display applications processed per officer bar chart
    - Display average review time per registrar bar chart
    - Display payment verification turnaround table
    - Display approval distribution per officer stacked bar chart
    - Display category reassignment frequency table
    - Add drill-down modals for staff activity logs
    - _Requirements: 7.1-7.6, 12.3, 12.5_
  
  - [x] 15.6 Create issuance.blade.php report view
    - Display monthly issuance counts line chart
    - Display print vs reprint ratio pie chart
    - Display print actions by staff table
    - Display outstanding unprinted approvals table
    - Add drill-down modals for print events
    - _Requirements: 8.1-8.5, 12.3, 12.5_
  
  - [x] 15.7 Create geographic.blade.php report view
    - Display accreditations by region bar chart
    - Display revenue by region bar chart
    - Display processing time by region bar chart
    - Display media houses by region pie chart
    - Add drill-down modals for region-specific details
    - _Requirements: 9.1-9.5, 12.3, 12.5_
  
  - [x] 15.8 Create downloads.blade.php report view
    - Display report generation form with date range pickers
    - Add buttons for each of 5 report types
    - Add format selection (PDF or Excel) for each report
    - Display loading indicators during report generation
    - _Requirements: 10.1-10.8, 12.1, 14.5_

- [ ] 16. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 17. View Implementation - PDF Templates
  - [x] 17.1 Create monthly-accreditation.blade.php PDF template
    - Include header with report title and date range
    - Include application volumes table
    - Include approval rates table
    - Include processing times table
    - Include footer with generation timestamp
    - _Requirements: 10.1, 10.6, 10.8_
  
  - [x] 17.2 Create revenue-financial.blade.php PDF template
    - Include revenue trends table
    - Include payment breakdowns table
    - Include waiver analysis table
    - Include generation timestamp
    - _Requirements: 10.2, 10.6, 10.8_
  
  - [x] 17.3 Create compliance-audit.blade.php PDF template
    - Include high-risk actions table
    - Include suspicious activities table
    - Include policy violations table
    - Include generation timestamp
    - _Requirements: 10.3, 10.6, 10.8_
  
  - [x] 17.4 Create mediahouse-status.blade.php PDF template
    - Include registrations table
    - Include staff counts table
    - Include renewal risks table
    - Include generation timestamp
    - _Requirements: 10.4, 10.6, 10.8_
  
  - [x] 17.5 Create operational-performance.blade.php PDF template
    - Include staff metrics table
    - Include processing efficiency table
    - Include SLA compliance table
    - Include generation timestamp
    - _Requirements: 10.5, 10.6, 10.8_

- [ ] 18. Chart.js Integration
  - [x] 18.1 Create JavaScript file for chart initialization
    - Create `public/js/director-dashboard-charts.js`
    - Implement initMonthlyTrendsChart() for line chart
    - Implement initRevenueBreakdownChart() for pie chart
    - Implement initProcessingTimeChart() for bar chart
    - Implement initCategoryDistributionChart() for doughnut chart
    - Implement initApprovalRatioChart() for stacked bar chart
    - Use color scheme from config/director-dashboard.php
    - _Requirements: 2.1-2.6, 3.1-3.8, 12.3_
  
  - [x] 18.2 Add Chart.js library to layout
    - Include Chart.js CDN in director layout
    - Include director-dashboard-charts.js script
    - _Requirements: 12.3_

- [ ] 19. Sidebar Navigation Enhancement
  - [x] 19.1 Update sidebar_staff.blade.php with director menu items
    - Add "Executive Overview" menu item linking to /staff/director/dashboard
    - Add "Accreditation Performance" menu item
    - Add "Financial Overview" menu item
    - Add "Compliance & Risk" menu item
    - Add "Media House Oversight" menu item
    - Add "Staff Performance" menu item
    - Add "Issuance & Printing" menu item
    - Add "Geographic Distribution" menu item
    - Add "Reports & Downloads" menu item
    - Show menu items only for users with director role
    - _Requirements: 12.1-12.2_

- [x] 20. Cache Implementation and Invalidation
  - [x] 20.1 Add cache invalidation to Application model
    - Add booted() method to listen for status changes
    - Invalidate director.kpis.executive_overview cache on status change
    - Invalidate director.charts.monthly_trends cache on status change
    - _Requirements: 14.6_
  
  - [x] 20.2 Add cache invalidation to Payment model
    - Add booted() method to listen for status changes to 'paid'
    - Invalidate director.kpis.executive_overview cache on payment confirmation
    - Invalidate director.charts.revenue_breakdown cache on payment confirmation
    - _Requirements: 14.6_
  
  - [x] 20.3 Add cache invalidation to ActivityLog model
    - Invalidate director.compliance cache on high-risk action logging
    - _Requirements: 14.6_

- [ ] 21. Access Control and Security
  - [x] 21.1 Add view-only middleware checks to DirectorController
    - Verify all controller methods are read-only
    - Add access denied responses for any POST/PUT/DELETE attempts to operational endpoints
    - _Requirements: 11.1-11.9_
  
  - [ ]* 21.2 Write security tests for view-only access
    - Test director cannot edit application data
    - Test director cannot approve/reject applications
    - Test director cannot assign applications
    - Test director cannot generate certificates
    - Test director cannot print documents
    - Test director cannot modify payments
    - Test director cannot grant waivers
    - Test director can view all data and generate reports
    - _Requirements: 11.1-11.9_

- [ ] 22. Documentation
  - [x] 22.1 Add inline documentation to service classes
    - Document all public methods with PHPDoc blocks
    - Include parameter types, return types, and descriptions
    - _Requirements: All_
  
  - [x] 22.2 Add inline documentation to repository classes
    - Document all public methods with PHPDoc blocks
    - Include SQLite-specific query notes
    - _Requirements: 13.1-13.4_

- [ ] 23. Final Integration and Testing
  - [ ]* 23.1 Write end-to-end integration tests
    - Test complete flow from dashboard load to drill-down to report generation
    - Test all 8 report sections load correctly
    - Test all 5 report exports generate successfully
    - _Requirements: All_
  
  - [ ]* 23.2 Write performance tests
    - Test executive overview loads within 3 seconds
    - Test report sections load within 5 seconds
    - Test cache effectiveness
    - _Requirements: 14.3-14.6_
  
  - [ ] 23.3 Final checkpoint - Ensure all tests pass
    - Run full test suite
    - Verify all 15 requirements are met
    - Ask the user if questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- All database queries must use SQLite-compatible syntax (strftime for dates, no MySQL functions)
- All service methods should implement caching with appropriate TTL values
- All views should prioritize charts and visualizations over tables
- All risk indicators must use green/amber/red color coding consistently
- All drill-down modals should load data via AJAX for better performance
- Property tests validate universal correctness properties from the design document
- Unit tests validate specific examples and edge cases
- Integration tests validate end-to-end workflows
