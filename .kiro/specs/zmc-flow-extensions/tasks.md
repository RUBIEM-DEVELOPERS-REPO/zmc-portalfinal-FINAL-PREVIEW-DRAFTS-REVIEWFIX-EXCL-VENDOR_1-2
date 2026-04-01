# ZMC Flow Extensions - Task Breakdown

## Document Information
**Version**: 1.0  
**Date**: 2026-02-25  
**Project**: ZMC Flow Extensions Implementation Tasks  
**Status**: Task Planning

---

## Phase 1: Database & Models (HIGH PRIORITY)

### Task 1.1: Add New Status Constants
**File**: `app/Models/Application.php`
**Estimated Time**: 15 minutes
**Dependencies**: None

- [ ] Add FORWARDED_TO_REGISTRAR_NO_APPROVAL constant
- [ ] Add PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR constant
- [ ] Add SUBMITTED_WITH_APP_FEE constant
- [ ] Add VERIFIED_BY_OFFICER_PENDING_REGISTRAR constant
- [ ] Add REGISTRAR_APPROVED_PENDING_REG_FEE constant
- [ ] Add REG_FEE_SUBMITTED_AWAITING_VERIFICATION constant
- [ ] Update stageForStatus() method to include new statuses

### Task 1.2: Create PaymentSubmissions Migration
**File**: `database/migrations/2026_02_25_XXXXXX_create_payment_submissions_table.php`
**Estimated Time**: 20 minutes
**Dependencies**: None

- [ ] Create migration file
- [ ] Define table schema (id, application_id, payment_stage, method, reference, amount, currency, status, timestamps, file paths, metadata)
- [ ] Add foreign key constraints
- [ ] Add indexes for performance
- [ ] Test migration up/down

### Task 1.3: Create OfficialLetters Migration
**File**: `database/migrations/2026_02_25_XXXXXX_create_official_letters_table.php`
**Estimated Time**: 15 minutes
**Dependencies**: None

- [ ] Create migration file
- [ ] Define table schema (id, application_id, uploaded_by, file details, timestamps)
- [ ] Add foreign key constraints
- [ ] Add indexes
- [ ] Test migration up/down

### Task 1.4: Add Fields to Applications Table
**File**: `database/migrations/2026_02_25_XXXXXX_add_flow_extension_fields_to_applications.php`
**Estimated Time**: 15 minutes
**Dependencies**: Task 1.3 (for official_letter_id FK)

- [ ] Create migration file
- [ ] Add forward_no_approval_reason field
- [ ] Add official_letter_id foreign key
- [ ] Add current_payment_stage enum field
- [ ] Test migration up/down

### Task 1.5: Create PaymentSubmission Model
**File**: `app/Models/PaymentSubmission.php`
**Estimated Time**: 30 minutes
**Dependencies**: Task 1.2

- [ ] Create model file
- [ ] Define fillable fields
- [ ] Define casts (dates, JSON, decimal)
- [ ] Add application() relationship
- [ ] Add verifier() relationship
- [ ] Add scopes (applicationFee, registrationFee, pending)
- [ ] Add helper methods

### Task 1.6: Create OfficialLetter Model
**File**: `app/Models/OfficialLetter.php`
**Estimated Time**: 20 minutes
**Dependencies**: Task 1.3

- [ ] Create model file
- [ ] Define fillable fields
- [ ] Define casts
- [ ] Add application() relationship
- [ ] Add uploader() relationship
- [ ] Add getDownloadUrl() helper method

### Task 1.7: Update Application Model Relationships
**File**: `app/Models/Application.php`
**Estimated Time**: 25 minutes
**Dependencies**: Tasks 1.5, 1.6

- [ ] Add paymentSubmissions() relationship
- [ ] Add applicationFeePayment() relationship
- [ ] Add registrationFeePayment() relationship
- [ ] Add officialLetter() relationship
- [ ] Add requiresApplicationFee() helper
- [ ] Add hasApplicationFeePaid() helper
- [ ] Add hasRegistrationFeePaid() helper

### Task 1.8: Update ApplicationWorkflow Service
**File**: `app/Services/ApplicationWorkflow.php`
**Estimated Time**: 30 minutes
**Dependencies**: Task 1.1

- [ ] Add OFFICER_REVIEW → FORWARDED_TO_REGISTRAR_NO_APPROVAL transition
- [ ] Add FORWARDED_TO_REGISTRAR_NO_APPROVAL transitions
- [ ] Add SUBMITTED_WITH_APP_FEE transitions
- [ ] Add VERIFIED_BY_OFFICER_PENDING_REGISTRAR transitions
- [ ] Add REGISTRAR_APPROVED_PENDING_REG_FEE transitions
- [ ] Add REG_FEE_SUBMITTED_AWAITING_VERIFICATION transitions
- [ ] Add PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR transitions
- [ ] Test all new transitions

**Phase 1 Total Estimated Time**: 2.5 hours

---

## Phase 2: Waiver Process (HIGH PRIORITY)

### Task 2.1: Add Forward Without Approval Action (Officer)
**File**: `app/Http/Controllers/Staff/AccreditationOfficerController.php`
**Estimated Time**: 30 minutes
**Dependencies**: Phase 1 complete

- [ ] Add forwardWithoutApproval() method
- [ ] Validate reason field (required)
- [ ] Save reason to application
- [ ] Transition to FORWARDED_TO_REGISTRAR_NO_APPROVAL
- [ ] Add audit logging
- [ ] Return success response

### Task 2.2: Add Registrar Special Case Handling
**File**: `app/Http/Controllers/Staff/RegistrarController.php`
**Estimated Time**: 40 minutes
**Dependencies**: Phase 1 complete

- [ ] Add approveSpecialCase() method for FORWARDED_TO_REGISTRAR_NO_APPROVAL status
- [ ] Validate application status
- [ ] Transition to PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR
- [ ] Add audit logging
- [ ] Update dashboard to show special cases queue

### Task 2.3: Add Waiver Verification (Accounts)
**File**: `app/Http/Controllers/Staff/AccountsPaymentsController.php`
**Estimated Time**: 45 minutes
**Dependencies**: Phase 1 complete

- [ ] Update dashboard to show waiver submissions
- [ ] Add verifyWaiverSubmission() method
- [ ] Validate waiver documents
- [ ] Create PaymentSubmission record for waiver
- [ ] Transition to PAYMENT_VERIFIED or PAYMENT_REJECTED
- [ ] Add audit logging

### Task 2.4: Create Officer Forward Modal UI
**File**: `resources/views/staff/officer/show.blade.php`
**Estimated Time**: 30 minutes
**Dependencies**: Task 2.1

- [ ] Add "Forward to Registrar (No Approval)" button
- [ ] Create modal with reason dropdown and textarea
- [ ] Add form submission handler
- [ ] Add JavaScript for modal show/hide
- [ ] Style with black/yellow theme

### Task 2.5: Create Registrar Special Cases View
**File**: `resources/views/staff/registrar/special_cases.blade.php`
**Estimated Time**: 40 minutes
**Dependencies**: Task 2.2

- [ ] Create view file
- [ ] Display applications with FORWARDED_TO_REGISTRAR_NO_APPROVAL status
- [ ] Show forward reason prominently
- [ ] Add approve/reject/fix request actions
- [ ] Style with black/yellow theme

### Task 2.6: Update Accounts Dashboard for Waivers
**File**: `resources/views/staff/accounts/dashboard.blade.php`
**Estimated Time**: 30 minutes
**Dependencies**: Task 2.3

- [ ] Add waiver submissions section
- [ ] Display waiver metadata (beneficiary, offered_by, etc.)
- [ ] Add verify/reject buttons
- [ ] Show waiver document preview/download
- [ ] Update KPIs to include waiver count

### Task 2.7: Add Routes for Waiver Workflow
**File**: `routes/web.php`
**Estimated Time**: 15 minutes
**Dependencies**: Tasks 2.1, 2.2, 2.3

- [ ] Add officer forward-no-approval route
- [ ] Add registrar special-cases route
- [ ] Add registrar approve-special-case route
- [ ] Add accounts verify-waiver route
- [ ] Apply appropriate middleware

**Phase 2 Total Estimated Time**: 3.5 hours

---

## Phase 3: Media House Two-Stage Payment (HIGH PRIORITY)

### Task 3.1: Add Application Fee Payment at Submission
**File**: `app/Http/Controllers/Portal/MediaHousePortalController.php`
**Estimated Time**: 1 hour
**Dependencies**: Phase 1 complete

- [ ] Intercept media house submission
- [ ] Show application fee payment modal
- [ ] Handle PayNow application fee initiation
- [ ] Handle proof upload for application fee
- [ ] Create PaymentSubmission record (payment_stage = 'application_fee')
- [ ] Set status to SUBMITTED_WITH_APP_FEE
- [ ] Prevent submission without application fee

### Task 3.2: Update Officer Review for Media House
**File**: `app/Http/Controllers/Staff/AccreditationOfficerController.php`
**Estimated Time**: 30 minutes
**Dependencies**: Phase 1 complete

- [ ] Update show() method to display application fee status
- [ ] Add verifyMediaHouse() method
- [ ] Validate all required documents
- [ ] Transition to VERIFIED_BY_OFFICER_PENDING_REGISTRAR
- [ ] Add audit logging

### Task 3.3: Add Official Letter Upload (Registrar)
**File**: `app/Http/Controllers/Staff/RegistrarController.php`
**Estimated Time**: 1 hour
**Dependencies**: Phase 1 complete

- [ ] Add approveWithOfficialLetter() method
- [ ] Validate file upload (PDF/image, max 5MB)
- [ ] Store file in official_letters directory
- [ ] Calculate file hash (SHA256)
- [ ] Create OfficialLetter record
- [ ] Link to application
- [ ] Transition to REGISTRAR_APPROVED_PENDING_REG_FEE
- [ ] Add audit logging
- [ ] Trigger notification to applicant

### Task 3.4: Add Registration Fee Payment Prompt
**File**: `resources/views/portal/mediahouse/dashboard.blade.php`
**Estimated Time**: 45 minutes
**Dependencies**: Task 3.3

- [ ] Detect REGISTRAR_APPROVED_PENDING_REG_FEE status
- [ ] Display alert with official letter download link
- [ ] Add "Pay Registration Fee" button
- [ ] Create registration fee payment modal
- [ ] Handle PayNow registration fee initiation
- [ ] Handle proof upload for registration fee
- [ ] Create PaymentSubmission record (payment_stage = 'registration_fee')
- [ ] Transition to REG_FEE_SUBMITTED_AWAITING_VERIFICATION

### Task 3.5: Update Accounts Two-Stage Verification
**File**: `app/Http/Controllers/Staff/AccountsPaymentsController.php`
**Estimated Time**: 1 hour
**Dependencies**: Phase 1 complete

- [ ] Update dashboard to show both payment stages
- [ ] Add verifyPaymentSubmission() method
- [ ] Check if both fees verified (for media house)
- [ ] Transition to PAYMENT_VERIFIED only when both verified
- [ ] Handle rejection of either fee
- [ ] Add audit logging for each stage

### Task 3.6: Create Official Letter Upload UI
**File**: `resources/views/staff/registrar/show.blade.php`
**Estimated Time**: 40 minutes
**Dependencies**: Task 3.3

- [ ] Add official letter upload section (media house only)
- [ ] Create file input with validation
- [ ] Add decision notes textarea
- [ ] Add "Approve & Upload Letter" button
- [ ] Show validation errors
- [ ] Style with black/yellow theme

### Task 3.7: Create Application Fee Payment Modal
**File**: `resources/views/portal/mediahouse/partials/app_fee_modal.blade.php`
**Estimated Time**: 45 minutes
**Dependencies**: Task 3.1

- [ ] Create modal component
- [ ] Add PayNow option with reference input
- [ ] Add proof upload option with form fields
- [ ] Add JavaScript for payment method switching
- [ ] Add form validation
- [ ] Style with black/yellow theme

### Task 3.8: Create Registration Fee Payment Modal
**File**: `resources/views/portal/mediahouse/partials/reg_fee_modal.blade.php`
**Estimated Time**: 45 minutes
**Dependencies**: Task 3.4

- [ ] Create modal component
- [ ] Add official letter download link
- [ ] Add PayNow option with reference input
- [ ] Add proof upload option with form fields
- [ ] Add JavaScript for payment method switching
- [ ] Add form validation
- [ ] Style with black/yellow theme

### Task 3.9: Update Accounts Dashboard for Two-Stage
**File**: `resources/views/staff/accounts/dashboard.blade.php`
**Estimated Time**: 45 minutes
**Dependencies**: Task 3.5

- [ ] Add payment stage column
- [ ] Show both fees for media house applications
- [ ] Add filter by payment stage
- [ ] Update KPIs to show app fee vs reg fee counts
- [ ] Add visual indicator for two-stage progress

### Task 3.10: Add Routes for Two-Stage Payment
**File**: `routes/web.php`
**Estimated Time**: 20 minutes
**Dependencies**: Tasks 3.1-3.5

- [ ] Add portal app-fee-payment route
- [ ] Add portal reg-fee-payment route
- [ ] Add portal download-official-letter route
- [ ] Add registrar approve-with-letter route
- [ ] Add accounts verify-payment-submission route
- [ ] Apply appropriate middleware

**Phase 3 Total Estimated Time**: 7 hours

---

## Phase 4: Registrar Payment Oversight (MEDIUM PRIORITY)

### Task 4.1: Create Payment Oversight Controller Method
**File**: `app/Http/Controllers/Staff/RegistrarController.php`
**Estimated Time**: 45 minutes
**Dependencies**: Phase 1 complete

- [ ] Add paymentOversight() method
- [ ] Query PaymentSubmission with filters
- [ ] Calculate KPIs (pending, verified, rejected by method)
- [ ] Paginate results
- [ ] Return view with data
- [ ] Ensure READ-ONLY (no write operations)

### Task 4.2: Create Payment Oversight View
**File**: `resources/views/staff/registrar/payment_oversight.blade.php`
**Estimated Time**: 1 hour
**Dependencies**: Task 4.1

- [ ] Create view file
- [ ] Add KPI cards (pending, verified, rejected, by method)
- [ ] Add filter form (status, method, date range)
- [ ] Create payment list table
- [ ] Add "View Details" link (read-only)
- [ ] Style with black/yellow theme
- [ ] Add "READ-ONLY" badge/indicator

### Task 4.3: Create Payment Detail View (Read-Only)
**File**: `resources/views/staff/registrar/payment_detail.blade.php`
**Estimated Time**: 45 minutes
**Dependencies**: Task 4.1

- [ ] Create view file
- [ ] Display payment submission details
- [ ] Show application information
- [ ] Display verification history
- [ ] Show audit trail
- [ ] Add download links for proof/waiver documents
- [ ] Ensure no action buttons (read-only)
- [ ] Style with black/yellow theme

### Task 4.4: Add Sidebar Link for Payment Oversight
**File**: `resources/views/layouts/sidebar_staff.blade.php`
**Estimated Time**: 10 minutes
**Dependencies**: Task 4.1

- [ ] Add "Payments Oversight" link to Registrar section
- [ ] Add icon (eye or chart icon)
- [ ] Set active state detection
- [ ] Position appropriately in menu

### Task 4.5: Add Route for Payment Oversight
**File**: `routes/web.php`
**Estimated Time**: 10 minutes
**Dependencies**: Task 4.1

- [ ] Add registrar payment-oversight route
- [ ] Add registrar payment-detail route
- [ ] Apply registrar role middleware
- [ ] Ensure no write operation routes

### Task 4.6: Add Audit Logging for Oversight Access
**File**: `app/Http/Controllers/Staff/RegistrarController.php`
**Estimated Time**: 15 minutes
**Dependencies**: Task 4.1

- [ ] Log when Registrar accesses payment oversight
- [ ] Log when Registrar views payment details
- [ ] Include timestamp, user, IP address
- [ ] Store in activity_logs table

**Phase 4 Total Estimated Time**: 3 hours

---

## Phase 5: Testing & Documentation (HIGH PRIORITY)

### Task 5.1: Unit Tests - Models
**File**: `tests/Unit/Models/PaymentSubmissionTest.php`, `tests/Unit/Models/OfficialLetterTest.php`
**Estimated Time**: 1 hour
**Dependencies**: Phase 1 complete

- [ ] Test PaymentSubmission relationships
- [ ] Test PaymentSubmission scopes
- [ ] Test OfficialLetter relationships
- [ ] Test Application helper methods
- [ ] Test model casts and attributes

### Task 5.2: Unit Tests - Workflow
**File**: `tests/Unit/Services/ApplicationWorkflowTest.php`
**Estimated Time**: 1.5 hours
**Dependencies**: Phase 1 complete

- [ ] Test all new status transitions
- [ ] Test invalid transitions (should fail)
- [ ] Test transition validation
- [ ] Test audit logging

### Task 5.3: Integration Tests - Waiver Workflow
**File**: `tests/Feature/WaiverWorkflowTest.php`
**Estimated Time**: 2 hours
**Dependencies**: Phase 2 complete

- [ ] Test officer forward without approval
- [ ] Test registrar approve special case
- [ ] Test accounts verify waiver
- [ ] Test accounts reject waiver
- [ ] Test complete waiver workflow end-to-end

### Task 5.4: Integration Tests - Two-Stage Payment
**File**: `tests/Feature/TwoStagePaymentTest.php`
**Estimated Time**: 2.5 hours
**Dependencies**: Phase 3 complete

- [ ] Test media house submission with app fee
- [ ] Test officer verification
- [ ] Test registrar approval with official letter
- [ ] Test registration fee payment
- [ ] Test accounts verification of both fees
- [ ] Test rejection scenarios
- [ ] Test complete two-stage workflow end-to-end

### Task 5.5: Integration Tests - Payment Oversight
**File**: `tests/Feature/PaymentOversightTest.php`
**Estimated Time**: 1 hour
**Dependencies**: Phase 4 complete

- [ ] Test registrar can view payment oversight
- [ ] Test registrar cannot modify payments
- [ ] Test filters work correctly
- [ ] Test KPIs calculate correctly
- [ ] Test audit logging

### Task 5.6: Update User Training Guide
**File**: `.kiro/specs/zmc-flow-extensions/USER-TRAINING-GUIDE.md`
**Estimated Time**: 2 hours
**Dependencies**: Phases 1-4 complete

- [ ] Document forward without approval process
- [ ] Document waiver submission and verification
- [ ] Document two-stage payment process
- [ ] Document official letter upload
- [ ] Document payment oversight access
- [ ] Add screenshots/diagrams
- [ ] Add troubleshooting section

### Task 5.7: Update Deployment Guide
**File**: `.kiro/specs/zmc-flow-extensions/DEPLOYMENT-GUIDE.md`
**Estimated Time**: 1 hour
**Dependencies**: Phases 1-4 complete

- [ ] Document migration steps
- [ ] Document configuration changes
- [ ] Document rollback procedures
- [ ] Add pre-deployment checklist
- [ ] Add post-deployment verification steps

### Task 5.8: Create Final Summary Document
**File**: `.kiro/specs/zmc-flow-extensions/FINAL-SUMMARY.md`
**Estimated Time**: 1 hour
**Dependencies**: All phases complete

- [ ] Summarize all changes
- [ ] List all new features
- [ ] Document database changes
- [ ] List all new routes
- [ ] Provide handover notes

**Phase 5 Total Estimated Time**: 12 hours

---

## Summary

**Total Tasks**: 58  
**Total Estimated Time**: 28 hours  

**Priority Breakdown**:
- HIGH PRIORITY: Phases 1, 2, 3, 5 (23 hours)
- MEDIUM PRIORITY: Phase 4 (3 hours)

**Recommended Implementation Order**:
1. Phase 1 (Foundation) - 2.5 hours
2. Phase 2 (Waiver Process) - 3.5 hours
3. Phase 3 (Two-Stage Payment) - 7 hours
4. Phase 4 (Payment Oversight) - 3 hours
5. Phase 5 (Testing & Docs) - 12 hours

**Critical Path**:
Phase 1 → Phase 2 → Phase 3 → Phase 5

**Can Be Parallelized**:
- Phase 4 can be done in parallel with Phase 2 or 3 (after Phase 1)
- Testing (Phase 5) can start as soon as each phase completes

---

**Document Version**: 1.0  
**Status**: Ready for Implementation  
**Next Step**: Begin Phase 1 - Database & Models
