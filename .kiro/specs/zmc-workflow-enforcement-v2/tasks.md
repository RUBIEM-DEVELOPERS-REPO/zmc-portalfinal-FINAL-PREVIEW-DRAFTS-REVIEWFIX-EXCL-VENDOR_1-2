# ZMC Complete System Flow Correction - Implementation Tasks

## Overview
This document outlines the implementation tasks for enforcing the complete workflow logic in the ZMC system with strict server-side validation, RBAC enforcement, and comprehensive audit logging.

## Phase 1: Database Schema Updates (Priority: CRITICAL)

### Task 1.1: Enhance Applications Table
- [ ] Create migration to add workflow tracking fields
- [ ] Add status tracking columns (status, previous_status, status_changed_at, status_changed_by)
- [ ] Add assignment columns (assigned_officer_id, locked_by, locked_at)
- [ ] Add category tracking (category_code, category_assigned_by, category_assigned_at)
- [ ] Add workflow flags (officer_approved, registrar_reviewed, payment_verified, in_production, produced)
- [ ] Add timestamp columns for each workflow stage
- [ ] Add indexes for performance
- [ ] Add check constraint for valid status values

### Task 1.2: Enhance Payment Submissions Table
- [ ] Create migration to add payment tracking fields
- [ ] Add payment_type column (application_fee, registration_fee, accreditation_fee)
- [ ] Add verification columns (verified, verified_by, verified_at, verification_notes)
- [ ] Add rejection columns (rejected, rejected_by, rejected_at, rejection_reason)
- [ ] Add unique constraints (paynow_reference, cash_receipt_number)
- [ ] Add indexes for performance

### Task 1.3: Create Activity Logs Table
- [ ] Create migration for activity_logs table
- [ ] Add actor tracking (actor_role, actor_user_id, actor_name)
- [ ] Add action tracking (action, description)
- [ ] Add status tracking (before_status, after_status)
- [ ] Add context (reason_notes, metadata JSON)
- [ ] Add request tracking (ip_address, user_agent)
- [ ] Add indexes for performance
- [ ] Ensure immutability (no updates/deletes)

### Task 1.4: Create Fix Requests Table
- [ ] Create migration for fix_requests table
- [ ] Add request tracking (requested_by, requested_by_role, request_message)
- [ ] Add status tracking (status, resolved_by, resolved_at)
- [ ] Add foreign keys and indexes

### Task 1.5: Enhance Official Letters Table
- [ ] Update official_letters table if needed
- [ ] Add verification tracking
- [ ] Add indexes

### Task 1.6: Run Migrations
- [ ] Test migrations on development database
- [ ] Create rollback scripts
- [ ] Document migration process
- [ ] Run migrations on staging
- [ ] Run migrations on production

## Phase 2: Core Services Implementation (Priority: CRITICAL)

### Task 2.1: Create StatusTransitionValidator
- [ ] Create StatusTransitionValidator class
- [ ] Define VALID_TRANSITIONS constant
- [ ] Implement canTransition() method
- [ ] Implement validateTransition() method
- [ ] Create InvalidStatusTransitionException
- [ ] Write unit tests

### Task 2.2: Create ActivityLogger Service
- [ ] Create ActivityLogger class
- [ ] Implement log() method
- [ ] Implement logStatusTransition() method
- [ ] Implement logPaymentAction() method
- [ ] Implement logProductionAction() method
- [ ] Write unit tests

### Task 2.3: Create ApplicationWorkflowService
- [ ] Create ApplicationWorkflowService class
- [ ] Implement submitApplication() method
- [ ] Implement approveApplication() method
- [ ] Implement returnToApplicant() method
- [ ] Implement forwardWithoutApproval() method
- [ ] Implement transitionStatus() private method
- [ ] Implement validation methods
- [ ] Write unit tests
- [ ] Write integration tests

### Task 2.4: Create PaymentWorkflowService
- [ ] Create PaymentWorkflowService class
- [ ] Implement submitPayNowPayment() method
- [ ] Implement submitProofPayment() method
- [ ] Implement verifyPayment() method
- [ ] Implement rejectPayment() method
- [ ] Implement recordCashPayment() method
- [ ] Write unit tests
- [ ] Write integration tests

### Task 2.5: Create ProductionWorkflowService
- [ ] Create ProductionWorkflowService class
- [ ] Implement startProduction() method
- [ ] Implement generateNumber() method
- [ ] Implement generateQRCode() method
- [ ] Implement logPrint() method
- [ ] Write unit tests
- [ ] Write integration tests

### Task 2.6: Create NotificationService
- [ ] Create NotificationService class
- [ ] Implement notifyOfficers() method
- [ ] Implement notifyApplicantPaymentRequired() method
- [ ] Implement notifyApplicantReturned() method
- [ ] Implement notifyAccountsPaymentSubmitted() method
- [ ] Implement notifyApplicantPaymentVerified() method
- [ ] Implement notifyProductionReady() method
- [ ] Write unit tests

## Phase 3: RBAC Implementation (Priority: CRITICAL)

### Task 3.1: Define Permissions
- [ ] Create RolePermissions class
- [ ] Define PERMISSIONS constant with all permissions
- [ ] Implement hasPermission() static method
- [ ] Document all permissions

### Task 3.2: Create Permission Middleware
- [ ] Create CheckPermission middleware
- [ ] Implement handle() method
- [ ] Register middleware in Kernel
- [ ] Write tests

### Task 3.3: Create Status Validation Middleware
- [ ] Create ValidateStatusTransition middleware
- [ ] Implement handle() method
- [ ] Implement getRequiredStatus() method
- [ ] Register middleware in Kernel
- [ ] Write tests

### Task 3.4: Create Audit Log Middleware
- [ ] Create AuditLogMiddleware
- [ ] Implement handle() method
- [ ] Implement logAction() method
- [ ] Register middleware in Kernel
- [ ] Write tests

### Task 3.5: Apply Middleware to Routes
- [ ] Update routes/web.php with middleware
- [ ] Apply auth middleware to all protected routes
- [ ] Apply permission middleware to role-specific routes
- [ ] Apply status validation middleware where needed
- [ ] Apply audit log middleware to critical routes

## Phase 4: Controller Refactoring (Priority: HIGH)

### Task 4.1: Refactor AccreditationOfficerController
- [ ] Update dashboard() method to use new statuses
- [ ] Update approve() method to use ApplicationWorkflowService
- [ ] Update return() method to use ApplicationWorkflowService
- [ ] Add forwardWithoutApproval() method
- [ ] Update show() method
- [ ] Remove direct status updates
- [ ] Add proper error handling
- [ ] Write controller tests

### Task 4.2: Refactor RegistrarController
- [ ] Update dashboard() method to use new statuses
- [ ] Add raiseFixRequest() method
- [ ] Add markReviewComplete() method
- [ ] Add approveMediaHouse() method (with official letter)
- [ ] Add pushToAccounts() method (special cases)
- [ ] Add payment oversight methods (READ-ONLY)
- [ ] Write controller tests

### Task 4.3: Refactor AccountsController
- [ ] Update dashboard() method to use new statuses
- [ ] Update verifyPayment() method to use PaymentWorkflowService
- [ ] Update rejectPayment() method to use PaymentWorkflowService
- [ ] Add recordCashPayment() method
- [ ] Add verifyPayNowReference() method
- [ ] Write controller tests

### Task 4.4: Create ProductionController
- [ ] Create ProductionController
- [ ] Implement index() method (production queue)
- [ ] Implement generate() method (generate number/QR)
- [ ] Implement print() method (log print)
- [ ] Implement logs() method (view print logs)
- [ ] Add middleware (officer only)
- [ ] Write controller tests

### Task 4.5: Refactor Portal Controllers
- [ ] Update ApplicationController for applicants
- [ ] Add submitPayment() method
- [ ] Add resubmit() method
- [ ] Update status display logic
- [ ] Write controller tests

## Phase 5: Model Updates (Priority: HIGH)

### Task 5.1: Update Application Model
- [ ] Add new fillable fields
- [ ] Add new casts
- [ ] Add workflow flag accessors
- [ ] Add status scope methods
- [ ] Add canTransitionTo() method
- [ ] Add isInStatus() method
- [ ] Update relationships
- [ ] Write model tests

### Task 5.2: Update PaymentSubmission Model
- [ ] Add new fillable fields
- [ ] Add new casts
- [ ] Add verification methods
- [ ] Add scope methods
- [ ] Update relationships
- [ ] Write model tests

### Task 5.3: Create ActivityLog Model
- [ ] Create ActivityLog model
- [ ] Define fillable fields
- [ ] Define casts
- [ ] Add relationships
- [ ] Add scope methods
- [ ] Prevent updates/deletes
- [ ] Write model tests

### Task 5.4: Create FixRequest Model
- [ ] Create FixRequest model
- [ ] Define fillable fields
- [ ] Add relationships
- [ ] Add status methods
- [ ] Write model tests

## Phase 6: API Endpoints (Priority: HIGH)

### Task 6.1: Applicant API Endpoints
- [ ] POST /api/portal/applications (submit)
- [ ] GET /api/portal/applications (list own)
- [ ] GET /api/portal/applications/{id} (view)
- [ ] PUT /api/portal/applications/{id}/resubmit
- [ ] POST /api/portal/applications/{id}/payment
- [ ] POST /api/portal/applications/{id}/payment/paynow
- [ ] POST /api/portal/applications/{id}/payment/proof
- [ ] GET /api/portal/applications/{id}/status
- [ ] GET /api/portal/applications/{id}/history
- [ ] Write API tests

### Task 6.2: Officer API Endpoints
- [ ] GET /api/staff/officer/applications
- [ ] GET /api/staff/officer/applications/{id}
- [ ] POST /api/staff/officer/applications/{id}/return
- [ ] POST /api/staff/officer/applications/{id}/approve
- [ ] POST /api/staff/officer/applications/{id}/forward
- [ ] PUT /api/staff/officer/applications/{id}/category
- [ ] POST /api/staff/officer/applications/{id}/claim
- [ ] POST /api/staff/officer/applications/{id}/release
- [ ] Write API tests

### Task 6.3: Production API Endpoints
- [ ] GET /api/staff/officer/production
- [ ] POST /api/staff/officer/production/{id}/generate
- [ ] POST /api/staff/officer/production/{id}/print
- [ ] GET /api/staff/officer/production/logs
- [ ] Write API tests

### Task 6.4: Registrar API Endpoints
- [ ] GET /api/staff/registrar/applications
- [ ] GET /api/staff/registrar/applications/{id}
- [ ] POST /api/staff/registrar/applications/{id}/fix-request
- [ ] PUT /api/staff/registrar/applications/{id}/review-complete
- [ ] POST /api/staff/registrar/applications/{id}/approve
- [ ] POST /api/staff/registrar/applications/{id}/official-letter
- [ ] POST /api/staff/registrar/applications/{id}/push-to-accounts
- [ ] GET /api/staff/registrar/payments (READ-ONLY)
- [ ] Write API tests

### Task 6.5: Accounts API Endpoints
- [ ] GET /api/staff/accounts/applications
- [ ] GET /api/staff/accounts/payments
- [ ] POST /api/staff/accounts/payments/{id}/verify
- [ ] POST /api/staff/accounts/payments/{id}/reject
- [ ] POST /api/staff/accounts/payments/cash
- [ ] GET /api/staff/accounts/payments/paynow/verify
- [ ] Write API tests

### Task 6.6: Auditor API Endpoints
- [ ] GET /api/staff/auditor/applications
- [ ] GET /api/staff/auditor/payments
- [ ] GET /api/staff/auditor/audit-logs
- [ ] GET /api/staff/auditor/audit-logs/export
- [ ] GET /api/staff/auditor/production/logs
- [ ] Write API tests

## Phase 7: UI Updates (Priority: MEDIUM)

### Task 7.1: Update Officer Dashboard
- [ ] Update application list to show new statuses
- [ ] Add "Forward without Approval" button
- [ ] Update approve modal
- [ ] Update return modal
- [ ] Add status badges with new colors
- [ ] Test UI workflows

### Task 7.2: Update Registrar Dashboard
- [ ] Update application list
- [ ] Add "Raise Fix Request" button
- [ ] Add "Mark Review Complete" button
- [ ] Add official letter upload (media house)
- [ ] Add payment oversight view (READ-ONLY)
- [ ] Test UI workflows

### Task 7.3: Update Accounts Dashboard
- [ ] Update payment list
- [ ] Add verify/reject buttons
- [ ] Add cash payment form
- [ ] Add PayNow verification interface
- [ ] Test UI workflows

### Task 7.4: Create Production Dashboard
- [ ] Create production queue view
- [ ] Add generate number interface
- [ ] Add print interface
- [ ] Add print logs view
- [ ] Test UI workflows

### Task 7.5: Update Applicant Portal
- [ ] Update application status display
- [ ] Add payment submission interface
- [ ] Add resubmission interface
- [ ] Update application history view
- [ ] Test UI workflows

### Task 7.6: Update Status Badges
- [ ] Create status badge component
- [ ] Define colors for each status
- [ ] Update all views to use new badges
- [ ] Test display

## Phase 8: Media House Two-Stage Payment (Priority: HIGH)

### Task 8.1: Application Fee Flow
- [ ] Update media house submission to require app fee
- [ ] Add app fee payment interface
- [ ] Block officer access until app fee verified
- [ ] Test flow end-to-end

### Task 8.2: Registration Fee Flow
- [ ] Add registration fee prompt after registrar approval
- [ ] Add registration fee payment interface
- [ ] Block production until reg fee verified
- [ ] Test flow end-to-end

### Task 8.3: Official Letter Requirement
- [ ] Add official letter upload to registrar approval
- [ ] Make official letter mandatory
- [ ] Validate file upload
- [ ] Test requirement enforcement

## Phase 9: Testing (Priority: CRITICAL)

### Task 9.1: Unit Tests
- [ ] Test StatusTransitionValidator
- [ ] Test ApplicationWorkflowService
- [ ] Test PaymentWorkflowService
- [ ] Test ProductionWorkflowService
- [ ] Test all models
- [ ] Achieve 80%+ code coverage

### Task 9.2: Integration Tests
- [ ] Test complete accreditation flow
- [ ] Test complete media house registration flow
- [ ] Test special case (waiver) flow
- [ ] Test fix request flow
- [ ] Test payment rejection flow
- [ ] Test production flow

### Task 9.3: API Tests
- [ ] Test all applicant endpoints
- [ ] Test all officer endpoints
- [ ] Test all registrar endpoints
- [ ] Test all accounts endpoints
- [ ] Test all auditor endpoints
- [ ] Test authentication/authorization

### Task 9.4: Security Tests
- [ ] Test RBAC enforcement
- [ ] Test status transition guards
- [ ] Test input validation
- [ ] Test SQL injection prevention
- [ ] Test XSS prevention
- [ ] Test CSRF protection

### Task 9.5: Performance Tests
- [ ] Load test production endpoints
- [ ] Test database query performance
- [ ] Test concurrent access
- [ ] Optimize slow queries

## Phase 10: Documentation (Priority: HIGH)

### Task 10.1: API Documentation
- [ ] Generate OpenAPI/Swagger docs
- [ ] Document all endpoints
- [ ] Document request/response formats
- [ ] Document error codes
- [ ] Publish API docs

### Task 10.2: Workflow Documentation
- [ ] Create workflow diagrams
- [ ] Document status transitions
- [ ] Document business rules
- [ ] Document validation rules

### Task 10.3: User Guides
- [ ] Write applicant user guide
- [ ] Write officer user guide
- [ ] Write registrar user guide
- [ ] Write accounts user guide
- [ ] Write auditor user guide

### Task 10.4: Admin Guide
- [ ] Write system admin guide
- [ ] Document troubleshooting procedures
- [ ] Document common issues
- [ ] Document maintenance tasks

### Task 10.5: Developer Guide
- [ ] Write developer setup guide
- [ ] Document architecture
- [ ] Document extending the system
- [ ] Document testing procedures

## Phase 11: Data Migration (Priority: CRITICAL)

### Task 11.1: Analyze Existing Data
- [ ] Count applications by current status
- [ ] Identify data quality issues
- [ ] Plan migration strategy

### Task 11.2: Create Migration Scripts
- [ ] Map old statuses to new statuses
- [ ] Create data migration script
- [ ] Create rollback script
- [ ] Test on copy of production data

### Task 11.3: Execute Migration
- [ ] Backup production database
- [ ] Run migration script
- [ ] Verify data integrity
- [ ] Test critical workflows
- [ ] Monitor for issues

## Phase 12: Deployment (Priority: CRITICAL)

### Task 12.1: Staging Deployment
- [ ] Deploy to staging environment
- [ ] Run all tests
- [ ] Perform UAT
- [ ] Fix any issues

### Task 12.2: Production Deployment
- [ ] Create deployment plan
- [ ] Schedule maintenance window
- [ ] Deploy to production
- [ ] Run smoke tests
- [ ] Monitor system health

### Task 12.3: Post-Deployment
- [ ] Monitor error logs
- [ ] Monitor performance
- [ ] Gather user feedback
- [ ] Address issues promptly

## Estimated Timeline

- **Phase 1**: 3-4 days
- **Phase 2**: 5-7 days
- **Phase 3**: 3-4 days
- **Phase 4**: 5-7 days
- **Phase 5**: 2-3 days
- **Phase 6**: 5-7 days
- **Phase 7**: 5-7 days
- **Phase 8**: 3-4 days
- **Phase 9**: 7-10 days
- **Phase 10**: 4-5 days
- **Phase 11**: 2-3 days
- **Phase 12**: 2-3 days

**Total Estimated Time: 46-64 days (9-13 weeks)**

## Priority Order

1. Phase 1 (Database) - Foundation
2. Phase 2 (Services) - Core logic
3. Phase 3 (RBAC) - Security
4. Phase 4 (Controllers) - API layer
5. Phase 11 (Migration) - Data preparation
6. Phase 5 (Models) - Data layer
7. Phase 6 (API) - Endpoints
8. Phase 8 (Media House) - Special flow
9. Phase 7 (UI) - User interface
10. Phase 9 (Testing) - Quality assurance
11. Phase 10 (Documentation) - Knowledge transfer
12. Phase 12 (Deployment) - Go live
