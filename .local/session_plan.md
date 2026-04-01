# Objective
Implement comprehensive workflow enforcement, UI improvements, and new features for the ZMC Portal as described in the user's detailed change request. This covers 5 major sections: core workflow enforcement, renewals simplification, portal UI features, accounts cash payments/physical intake, and production designer.

# Tasks

### T001: New Status Constants & State Machine Migration
- **Blocked By**: []
- **Details**:
  - Add new status constants to `Application` model:
    - `SubmittedToAccreditationOfficer` (replaces current `submitted`)
    - `ReturnedToApplicant` (replaces `correction_requested`)
    - `ApprovedByAccreditationOfficer_AwaitingPayment`
    - `AwaitingAccountsVerification`
    - `PaymentVerified`
    - `PaymentRejected`
    - `ProducedReadyForCollection`
    - `ForwardedToRegistrar_NoAccredApproval`
    - `PendingAccountsReview_FromRegistrar`
    - Media House specific: `SubmittedWithApplicationFee_ToAccreditationOfficer`, `VerifiedByAccreditationOfficer_PendingRegistrarReview`, `RegistrarApproved_PendingRegistrationFeePayment`, `RegistrarRaisedFixRequest`
  - Create migration to update status CHECK constraint to include all new statuses
  - Update `ApplicationWorkflow` service with new transition map & validation
  - Update `stageForStatus()` helper for routing
  - Add `payment_stage` field to applications (application_fee vs registration_fee for media houses)
  - Files: `app/Models/Application.php`, `app/Services/ApplicationWorkflow.php`, new migration
  - Acceptance: New statuses accepted by DB, transitions validate correctly

### T002: Backend Workflow Guards - Accreditation Officer
- **Blocked By**: [T001]
- **Details**:
  - Update `AccreditationOfficerController`:
    - `approve()` → sets status to `ApprovedByAccreditationOfficer_AwaitingPayment`
    - `requestCorrection()` → sets status to `ReturnedToApplicant`
    - Add `forwardToRegistrar()` action for waiver/special cases → `ForwardedToRegistrar_NoAccredApproval` with mandatory reason
    - Add Production sidebar link and route to production queue filtered for this officer
  - Enforce server-side guards: only valid transitions allowed
  - Label applications as New/Renewal/Replacement in views
  - Files: `app/Http/Controllers/Staff/AccreditationOfficerController.php`, `routes/web.php`, officer blade templates
  - Acceptance: Officer can approve, return, and forward to registrar; invalid transitions rejected

### T003: Backend Workflow Guards - Registrar
- **Blocked By**: [T001]
- **Details**:
  - Update `RegistrarController`:
    - Receives `ApprovedByAccreditationOfficer_AwaitingPayment` and `ForwardedToRegistrar_NoAccredApproval` applications
    - Can send "Fix Request" to Accreditation Officer → `RegistrarRaisedFixRequest`
    - For media houses: approval requires uploading official letter (mandatory, block if missing)
    - After approval, push to Accounts or prompt registration fee payment
    - Add read-only oversight of Accounts activity (payments queue, outcomes, audit logs)
  - Add `PendingAccountsReview_FromRegistrar` transition from Registrar for waiver cases
  - Files: `app/Http/Controllers/Staff/RegistrarController.php`, registrar blade templates, `routes/web.php`
  - Acceptance: Registrar can review, raise fix requests, approve with letter requirement

### T004: Backend Workflow Guards - Accounts
- **Blocked By**: [T001]
- **Details**:
  - Update `AccountsPaymentsController`:
    - Receives `AwaitingAccountsVerification` applications
    - PayNow: verify reference → `PaymentVerified` or `PaymentRejected`
    - Proof/Waiver: verify → approve/reject
    - `PaymentVerified` routes to Production queue
    - Add "Record Cash Payment" with receipt number, amount, date linking
    - Cash records must be auditable and immutable (void with reason only)
  - Add receipt linking to payment record
  - Files: `app/Http/Controllers/Staff/AccountsPaymentsController.php`, accounts blade templates, `routes/web.php`
  - Acceptance: Accounts can verify payments, record cash, approve/reject with audit trail

### T005: Portal Payment Flow Updates
- **Blocked By**: [T001]
- **Details**:
  - After Accreditation Officer approval, applicant sees payment prompt on dashboard
  - PayNow flow: Pay → click "Done" → modal for reference number → submit → `AwaitingAccountsVerification`
  - Proof upload flow: modal with metadata + file → submit → `AwaitingAccountsVerification`
  - Media House two-stage payment: application fee required before submission, registration fee after registrar approval
  - Block media house submission if application fee not provided
  - Files: `resources/views/portal/partials/payment_modal.blade.php`, `resources/views/portal/accreditation/dashboard.blade.php`, `resources/views/portal/mediahouse/dashboard.blade.php`, `app/Http/Controllers/Portal/ManualPaymentController.php`
  - Acceptance: Payment flows work end-to-end for both portal types

### T006: Renewal Flow Simplification
- **Blocked By**: [T001]
- **Details**:
  - Renewal Step 2: only ask for Accreditation Number (practitioner) or Registration Number (media house)
  - On entering number, query DB for full record and display
  - Applicant confirms "No changes" or indicates changes (structured list)
  - After confirmation, proceed to payment (PayNow reference or proof upload)
  - Renewal routing: Applicant → Accounts → Production
  - Media House renewal: remove "Contact Details" step, enter Registration Number → lookup → confirm → payment
  - Document rules: Freelancer hides employment letter; Employed requires it
  - Media House renewal docs: Previous Certificate (required) + Official Letter (required)
  - Files: `resources/views/portal/accreditation/renewals.blade.php`, `resources/views/portal/mediahouse/renewals.blade.php`, `app/Http/Controllers/AccreditationPortalController.php`, `app/Http/Controllers/MediaHousePortalController.php`
  - Acceptance: Renewals use number lookup, simplified steps, correct document requirements

### T007: Registrar Reminders & Portal Notifications
- **Blocked By**: [T001]
- **Details**:
  - Create `reminders` table migration (target_type, target_id, message, type, acknowledged_at, created_by)
  - Registrar can create reminders targeting specific practitioners or media houses
  - Reminders display as attention-grabbing popups/banners on portal dashboards until acknowledged
  - Levy payment reminders for media houses
  - Track acknowledgement in audit log
  - Files: new migration, new `Reminder` model, registrar controller update, portal dashboard updates
  - Acceptance: Registrar creates reminders, they show on portals, acknowledgement tracked

### T008: Portal UI Enhancements
- **Blocked By**: []
- **Details**:
  - Requirements pages: dynamic DB-driven requirements display on both portals (dashboard + full page)
  - Redesign "How to get Accredited" and "How to get Registered" pages (steps, checklists, payment paths, timelines, contacts, CTAs)
  - Show previous applications list for logged-in users on portal dashboards
  - Show previous applications panel in staff views (Officer, Auditor, Registrar)
  - Show previous payments panel in Accounts and Auditor views
  - Add "Media Hub" link in both portal sidebars
  - Notices & Events: support image uploads with thumbnails
  - Files: portal blade templates, staff blade templates, `ContentController`
  - Acceptance: Requirements pages display, how-to pages redesigned, previous applications visible

### T009: Profile Updates & UI Cleanup
- **Blocked By**: []
- **Details**:
  - Local practitioners: add required ID Number field to profile
  - Foreign practitioners: add required Passport Number field
  - Phone numbers: require at least TWO in profile
  - Media House profile: add social media fields (FB/X/IG/YT/TikTok/Website)
  - Media House dashboard: show license status (active/expired) with time left, levy reminders
  - Remove language translation features entirely
  - Remove Staff Management module/pages/routes/menus
  - Remove social media login links (keep standard auth only)
  - Functional light/dark mode persisted per user
  - Files: profile blade templates, user model, migrations, layout templates, auth views
  - Acceptance: Profile fields added, removed features gone, theme toggle works

### T010: Physical Intake & Accounts Cash Payments
- **Blocked By**: [T001, T004]
- **Details**:
  - Accreditation Officer "Physical Intake" action:
    - Enter accreditation/registration number → query DB → display record
    - Confirm identity, enter receipt number
    - Receipt visible to Accounts (linked payment record)
    - Auto-create application record and add to Production queue
    - Prevent duplicates for same cycle (block or override with audit)
  - Accounts cash payment recording already in T004
  - Files: `AccreditationOfficerController`, new blade template for physical intake, `routes/web.php`
  - Acceptance: Physical intake creates application linked to receipt, shows in production queue

### T011: Production Dashboard & Card/Certificate Designer
- **Blocked By**: [T001, T002]
- **Details**:
  - Production dashboard shows only `PaymentVerified` applications
  - Card Designer interface for accreditation cards
  - Certificate Designer interface for media house certificates
  - Capabilities: upload background template, place dynamic fields, save template versions
  - Generate preview → final output → print tracking
  - `ProductionPrintLog` tracking (template version, who generated, who printed, count, timestamps)
  - Files: `ProductionController`, new blade templates for designer, new migration for templates table
  - Acceptance: Designer allows template creation, preview, generation, and print tracking

### T012: Audit Log Enforcement & Status Transition Table
- **Blocked By**: [T001]
- **Details**:
  - Ensure EVERY action writes to audit log (actor, role, action, before_state, after_state, timestamp, notes)
  - Create status transition table document
  - Create RBAC permission matrix
  - Update all controllers to log transitions
  - Files: `app/Services/ActivityLogger.php`, all staff controllers
  - Acceptance: All transitions logged, documentation generated

### T013: Testing, Verification & Documentation
- **Blocked By**: [T001-T012]
- **Details**:
  - Update database seeder with test data for all new flows
  - Create verification notes for each workflow
  - Output: status transition table, RBAC matrix, endpoints list, screens changed list
  - Update replit.md with all changes
  - Files: `database/seeders/`, `replit.md`
  - Acceptance: Seed data covers all flows, documentation complete
