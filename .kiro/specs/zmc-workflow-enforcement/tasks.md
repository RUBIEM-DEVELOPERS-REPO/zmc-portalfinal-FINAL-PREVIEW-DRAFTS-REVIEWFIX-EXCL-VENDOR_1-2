# ZMC Workflow Enforcement - Implementation Tasks

## Phase 1: Database & Models (Priority: Critical)

### Database Migrations
- [x] Create migration: enhance_applications_table_for_workflow_enforcement
- [x] Create migration: create_audit_logs_table
- [x] Create migration: create_fix_requests_table
- [x] Create migration: create_payment_submissions_table
- [x] Create migration: create_production_print_logs_table
- [x] Create migration: create_application_messages_table
- [ ] Write migration code for applications table enhancement
- [ ] Write migration code for audit_logs table
- [ ] Write migration code for fix_requests table
- [ ] Write migration code for payment_submissions table
- [ ] Write migration code for production_print_logs table
- [ ] Write migration code for application_messages table
- [ ] Run migrations
- [ ] Verify database schema

### Models
- [ ] Create AuditLog model
- [ ] Create FixRequest model
- [ ] Create PaymentSubmission model
- [ ] Create ProductionPrintLog model
- [ ] Create ApplicationMessage model
- [ ] Update Application model with new relationships
- [ ] Add status constants to Application model
- [ ] Add category constants to Application model

## Phase 2: Core Services (Priority: Critical)

### Status Machine
- [ ] Create ApplicationStatusMachine service
- [ ] Implement status transition validation
- [ ] Implement transition requirements validation
- [ ] Implement state capture methods
- [ ] Implement transition field updates
- [ ] Add status transition tests

### Category Validation
- [ ] Create CategoryValidator service
- [ ] Define media practitioner categories
- [ ] Define mass media categories
- [ ] Implement validation logic
- [ ] Add category validation tests

### Audit Logging
- [ ] Create AuditLogger service
- [ ] Implement log method
- [ ] Implement actor role determination
- [ ] Implement state capture
- [ ] Add audit logging tests

### Production Services
- [ ] Create AccreditationNumberGenerator service
- [ ] Implement secure number generation
- [ ] Implement sequence tracking
- [ ] Implement checksum generation
- [ ] Create QRCodeGenerator service
- [ ] Implement QR code data structure
- [ ] Add production service tests

## Phase 3: RBAC & Authorization (Priority: High)

### Middleware
- [ ] Create CheckApplicationAccess middleware
- [ ] Implement permission matrix
- [ ] Implement role-based checks
- [ ] Implement status-based checks
- [ ] Register middleware in Kernel

### Policies
- [ ] Create/Update ApplicationPolicy
- [ ] Implement viewAny method
- [ ] Implement view method
- [ ] Implement approve method
- [ ] Implement verifyPayment method
- [ ] Implement startProduction method
- [ ] Implement raiseFixRequest method
- [ ] Register policy in AuthServiceProvider

### Guards
- [ ] Create StatusGuard for production access
- [ ] Create PaymentGuard for payment verification
- [ ] Create ApprovalGuard for approvals

## Phase 4: Controllers & Routes (Priority: High)

### Applicant Controllers
- [ ] Update ApplicationController with new workflow
- [ ] Implement submit method
- [ ] Implement resubmit method
- [ ] Create PaymentController
- [ ] Implement submitPaynowReference method
- [ ] Implement uploadProof method
- [ ] Implement resubmitPayment method

### Accreditation Officer Controllers
- [ ] Update AccreditationOfficerController
- [ ] Implement newSubmissions queue
- [ ] Implement approve method with category assignment
- [ ] Implement return method with reason
- [ ] Implement applyFix method
- [ ] Implement reApprove method
- [ ] Create ProductionController
- [ ] Implement production queue
- [ ] Implement generate method (number + QR)
- [ ] Implement print method with tracking

### Registrar Controllers
- [ ] Update RegistrarController
- [ ] Implement pendingReview queue
- [ ] Implement raiseFix method
- [ ] Implement completeReview method
- [ ] Implement fixRequests queue

### Accounts Controllers
- [ ] Update/Create AccountsPaymentsController
- [ ] Implement awaitingVerification queue
- [ ] Implement verifyPayment method
- [ ] Implement rejectPayment method
- [ ] Implement paymentRejected queue

### Audit Controllers
- [ ] Create AuditController
- [ ] Implement index method (audit log viewer)
- [ ] Implement applicationTimeline method
- [ ] Implement export method (CSV/PDF)
- [ ] Implement filters (user, role, action, date)

### Routes
- [ ] Define applicant routes
- [ ] Define accreditation officer routes
- [ ] Define registrar routes
- [ ] Define accounts routes
- [ ] Define production routes
- [ ] Define audit routes
- [ ] Apply middleware to all routes
- [ ] Test route authorization

## Phase 5: Dashboard Views (Priority: High)

### Accreditation Officer Dashboard
- [ ] Create queue views (new, returned, fix requests, approved)
- [ ] Create application review modal
- [ ] Create category assignment dropdown
- [ ] Create approve button with confirmation
- [ ] Create return modal with reason field
- [ ] Create fix application form
- [ ] Add Production link to sidebar
- [ ] Create production queue view
- [ ] Create production generation interface
- [ ] Create print interface with tracking

### Registrar Dashboard
- [ ] Create pending review queue view
- [ ] Create fix request form
- [ ] Create fix requests raised queue
- [ ] Create review completion button
- [ ] Create application detail view (read-only)

### Accounts Dashboard
- [ ] Create awaiting verification queue
- [ ] Create payment verification interface
- [ ] Create PayNow reference verification form
- [ ] Create proof verification interface
- [ ] Create reject payment modal with reason
- [ ] Create payment rejected queue
- [ ] Create payment history view

### Applicant Dashboard
- [ ] Update application timeline view
- [ ] Create payment prompt modal
- [ ] Create PayNow payment interface
- [ ] Create PayNow reference submission modal
- [ ] Create proof upload interface
- [ ] Create messages/notifications view
- [ ] Create return reason display
- [ ] Create rejection reason display
- [ ] Create resubmission interface

### Audit Dashboard
- [ ] Create audit log viewer
- [ ] Create filter interface
- [ ] Create application timeline view
- [ ] Create export functionality
- [ ] Create search functionality

## Phase 6: UI Components (Priority: Medium)

### Shared Components
- [ ] Create StatusBadge component
- [ ] Create CategoryBadge component
- [ ] Create ActionButton component
- [ ] Create ReasonModal component
- [ ] Create ConfirmationModal component
- [ ] Create TimelineComponent
- [ ] Create DocumentViewer component

### Sidebar Updates
- [ ] Add Production link for Accreditation Officer
- [ ] Update queue counters
- [ ] Add status indicators

## Phase 7: Notifications & Messaging (Priority: Medium)

### Notification System
- [ ] Create ApplicationStatusChanged notification
- [ ] Create PaymentPrompt notification
- [ ] Create ApplicationReturned notification
- [ ] Create ApplicationRejected notification
- [ ] Create FixRequestRaised notification
- [ ] Create PaymentVerified notification
- [ ] Create PaymentRejected notification
- [ ] Create ProductionReady notification

### Message System
- [ ] Implement message creation on status changes
- [ ] Implement message delivery
- [ ] Implement message read tracking
- [ ] Create message inbox view
- [ ] Create message notification badges

## Phase 8: Validation & Business Rules (Priority: High)

### Form Validation
- [ ] Create ApplicationSubmissionRequest
- [ ] Create CategoryAssignmentRequest
- [ ] Create ReturnApplicationRequest
- [ ] Create PaymentSubmissionRequest
- [ ] Create FixRequestRequest
- [ ] Create PaymentVerificationRequest

### Business Rules
- [ ] Enforce sequential workflow
- [ ] Validate category assignments
- [ ] Validate payment submissions
- [ ] Validate status transitions
- [ ] Implement max return limit (prevent loops)
- [ ] Implement concurrent edit protection

## Phase 9: Testing (Priority: High)

### Unit Tests
- [ ] Test ApplicationStatusMachine transitions
- [ ] Test CategoryValidator
- [ ] Test AccreditationNumberGenerator
- [ ] Test QRCodeGenerator
- [ ] Test AuditLogger
- [ ] Test permission checks

### Feature Tests
- [ ] Test complete workflow (submission to collection)
- [ ] Test PayNow payment flow
- [ ] Test proof upload flow
- [ ] Test return and resubmission
- [ ] Test fix request cycle
- [ ] Test payment rejection and resubmission
- [ ] Test production process
- [ ] Test audit logging

### Integration Tests
- [ ] Test RBAC enforcement
- [ ] Test dashboard queues
- [ ] Test concurrent access
- [ ] Test edge cases
- [ ] Test notification delivery

## Phase 10: Documentation & Training (Priority: Medium)

### User Documentation
- [ ] Create applicant user guide
- [ ] Create accreditation officer guide
- [ ] Create registrar guide
- [ ] Create accounts officer guide
- [ ] Create production guide
- [ ] Create auditor guide

### Technical Documentation
- [ ] Document API endpoints
- [ ] Document status machine
- [ ] Document RBAC matrix
- [ ] Document database schema
- [ ] Create deployment guide

### Training Materials
- [ ] Create workflow diagrams
- [ ] Create video tutorials
- [ ] Create FAQ document
- [ ] Create troubleshooting guide

## Phase 11: Deployment (Priority: Critical)

### Pre-Deployment
- [ ] Review all code
- [ ] Run all tests
- [ ] Perform security audit
- [ ] Review RBAC permissions
- [ ] Test on staging environment
- [ ] Create backup plan

### Deployment Steps
- [ ] Backup production database
- [ ] Run migrations
- [ ] Deploy code
- [ ] Seed official categories
- [ ] Configure PayNow integration
- [ ] Set up QR code library
- [ ] Configure notification channels
- [ ] Set up audit log retention
- [ ] Configure monitoring

### Post-Deployment
- [ ] Verify all workflows
- [ ] Test all roles
- [ ] Monitor error logs
- [ ] Monitor performance
- [ ] Gather user feedback
- [ ] Address issues

## Current Status

**Phase 1: Database & Models**
- Migrations created ✅
- Migration code: IN PROGRESS 🔄
- Models: PENDING ⏳

**Next Immediate Tasks:**
1. Write migration code for all 6 migrations
2. Create all model files
3. Implement ApplicationStatusMachine service
4. Implement CategoryValidator service
5. Implement AuditLogger service

## Notes

- All critical path items must be completed before deployment
- Testing should be done in parallel with development
- Documentation should be updated as features are completed
- User training should begin before deployment
- Staging environment testing is mandatory

## Dependencies

- Laravel 10.x
- PHP 8.1+
- SQLite/MySQL database
- QR code library (simplesoftwareio/simple-qrcode)
- PayNow integration credentials
- Email/SMS notification service

## Estimated Timeline

- Phase 1-2: 2-3 days (Database & Core Services)
- Phase 3-4: 3-4 days (RBAC & Controllers)
- Phase 5-6: 4-5 days (Views & UI)
- Phase 7-8: 2-3 days (Notifications & Validation)
- Phase 9: 3-4 days (Testing)
- Phase 10-11: 2-3 days (Documentation & Deployment)

**Total Estimated Time: 16-22 days**
