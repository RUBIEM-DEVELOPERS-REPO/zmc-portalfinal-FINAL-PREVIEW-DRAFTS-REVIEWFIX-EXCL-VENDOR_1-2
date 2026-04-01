# ZMC Workflow Enforcement - Gap Analysis

## Executive Summary

After analyzing the existing codebase, the ZMC system already has a robust workflow implementation with most required features. This document identifies what exists vs what's missing from the requirements.

---

## ✅ ALREADY IMPLEMENTED

### 1. Database Schema & Models
- ✅ `applications` table with comprehensive workflow fields
- ✅ `audit_logs` table (2026_01_02_000001)
- ✅ `audit_trails` table (2025_12_29_215456)
- ✅ Application model with all status constants defined
- ✅ Relationships: applicant, assignedOfficer, documents, messages, workflowLogs, payments, printLogs
- ✅ Locking mechanism (locked_by, locked_at) for concurrency control
- ✅ Payment fields: paynow_reference, payment_proof_path, waiver_path, payment_status
- ✅ Category fields: accreditation_category_code, media_house_category_code

### 2. Status Machine
- ✅ 17+ status constants defined in Application model:
  - DRAFT, SUBMITTED, WITHDRAWN
  - OFFICER_REVIEW, OFFICER_APPROVED, OFFICER_REJECTED, CORRECTION_REQUESTED
  - REGISTRAR_REVIEW, REGISTRAR_APPROVED, REGISTRAR_REJECTED, RETURNED_TO_OFFICER
  - ACCOUNTS_REVIEW, PAID_CONFIRMED, RETURNED_TO_ACCOUNTS
  - PRODUCTION_QUEUE, CARD_GENERATED, CERT_GENERATED, PRINTED, ISSUED
- ✅ ApplicationWorkflow service with transition validation
- ✅ Status transition map enforcing allowed transitions
- ✅ Automatic stage assignment based on status

### 3. RBAC (Role-Based Access Control)
- ✅ Middleware: `staff.portal`, `role:accreditation_officer|registrar|accounts_payments|production`
- ✅ Route groups for each role with proper middleware
- ✅ Session-based role tracking: `session('active_staff_role')`
- ✅ Role-specific dashboards and queues

### 4. Workflow Implementation

#### Accreditation Officer
- ✅ Dashboard with KPIs and filtered queues
- ✅ Claim/lock mechanism to prevent concurrent editing
- ✅ Category assignment (accreditation & mass media categories)
- ✅ Approve action → transitions to REGISTRAR_REVIEW
- ✅ Request correction → CORRECTION_REQUESTED status
- ✅ Messaging system for applicant communication
- ✅ Cannot reject (enforced - must use correction or forward)

#### Registrar
- ✅ Dashboard with incoming queue (REGISTRAR_REVIEW status)
- ✅ Approve for payment → transitions to ACCOUNTS_REVIEW
- ✅ Reject capability
- ✅ Return to Accounts capability
- ✅ Category reassignment with reason tracking
- ✅ Audit trail viewing

#### Accounts/Payments
- ✅ Dashboard for ACCOUNTS_REVIEW queue
- ✅ PayNow integration (paynow_reference, poll_url, webhook)
- ✅ Payment proof upload handling (proof_status: submitted/approved/rejected)
- ✅ Waiver handling (waiver_status: submitted/approved/rejected)
- ✅ Mark paid → transitions to PAID_CONFIRMED → PRODUCTION_QUEUE
- ✅ Return to Officer capability
- ✅ Payment reconciliation views
- ✅ Ledger and financial reporting

#### Production
- ✅ ProductionController exists
- ✅ Print logs tracking (PrintLog model)
- ✅ Document versions (DocumentVersion model)
- ✅ QR code generation capability (generateFormattedNumber method)

### 5. Audit Trail
- ✅ ActivityLogger service
- ✅ AuditTrail support class
- ✅ audit_logs table with actor_user_id, action, model_type, model_id, meta, ip, user_agent
- ✅ audit_trails table with old_values, new_values, description
- ✅ Automatic logging on all transitions
- ✅ Audit trail viewing in Registrar dashboard

### 6. Category Validation
- ✅ Journalist categories: JE, JF, JO, JS, JM, JP, JD, JT (defined in Application model)
- ✅ Mass media categories: MC, MA, MF, MN, DG, MP, MS, MV (defined in Application model)
- ✅ Category validation in approve actions (422 error if invalid)
- ✅ Category reassignment by Registrar with reason tracking

### 7. Dashboard Queues
- ✅ Role-specific filtering in all dashboards
- ✅ Concurrency control (locked_by, locked_at with 2-hour timeout)
- ✅ Pool visibility logic (only show unassigned or assigned to current user)
- ✅ Status-based filtering per role

### 8. Payment Flow
- ✅ PayNow gateway integration
- ✅ Payment proof upload
- ✅ Waiver submission and approval
- ✅ Payment status tracking
- ✅ Accounts verification workflow

---

## ❌ MISSING / INCOMPLETE FEATURES

### 1. Parallel Routing After Officer Approval
**REQUIREMENT**: When Accreditation Officer approves, application must be visible to BOTH Registrar AND Accounts simultaneously.

**CURRENT STATE**: Officer approval → REGISTRAR_REVIEW only. Accounts sees it only after Registrar approves.

**GAP**: Need to implement parallel routing so both Registrar and Accounts can work independently after Officer approval.

**SOLUTION**: 
- Add new status: `OFFICER_APPROVED_PENDING_DUAL_REVIEW`
- Both Registrar and Accounts dashboards should show this status
- Application proceeds to ACCOUNTS_REVIEW only when BOTH have completed their tasks
- Or: Keep current flow but add visibility flag for Accounts to see REGISTRAR_REVIEW items

### 2. Applicant Payment Prompt
**REQUIREMENT**: After Officer approval, system must prompt applicant to pay via PayNow OR upload proof.

**CURRENT STATE**: Payment handling exists but no automatic prompt/notification to applicant after approval.

**GAP**: Missing applicant notification/prompt mechanism.

**SOLUTION**:
- Add notification service call after Officer approval
- Create applicant portal view showing "Payment Required" status
- Email/SMS notification to applicant
- Portal dashboard shows payment options (PayNow button + proof upload)

### 3. PayNow Reference Submission Modal
**REQUIREMENT**: After PayNow payment, applicant must enter reference number in a modal.

**CURRENT STATE**: PayNow integration exists but reference entry flow unclear.

**GAP**: Need explicit modal/form for reference number entry after PayNow redirect.

**SOLUTION**:
- Add PayNow callback page with reference entry modal
- Store reference in `paynow_reference` field
- Set payment_status to 'reference_submitted'

### 4. Fix Request Workflow (Registrar → Officer)
**REQUIREMENT**: Registrar cannot edit application data directly. Must send "Fix Request" to Officer.

**CURRENT STATE**: Registrar can reassign category but no structured fix request system.

**GAP**: Missing fix_requests table and workflow.

**SOLUTION**:
- Create `fix_requests` table (application_id, requested_by, request_type, description, status, resolved_at)
- Add "Request Fix" button in Registrar view
- Officer dashboard shows fix requests
- Status: REGISTRAR_RAISED_FIX_REQUEST

### 5. Production Link in Officer Sidebar
**REQUIREMENT**: Accreditation Officer sidebar must have persistent "Production" link.

**CURRENT STATE**: Production module exists but link not in Officer sidebar.

**GAP**: Missing sidebar menu item.

**SOLUTION**:
- Add Production link to `resources/views/layouts/sidebar_staff.blade.php` for accreditation_officer role
- Link to production dashboard

### 6. Payment Submission Tracking
**REQUIREMENT**: Track whether applicant submitted PayNow reference OR proof upload.

**CURRENT STATE**: Fields exist (paynow_reference, payment_proof_path) but no explicit submission method tracking.

**GAP**: No `payment_submission_method` field.

**SOLUTION**:
- Add `payment_submission_method` enum field: null, 'paynow_reference', 'proof_upload'
- Set when applicant submits payment info
- Accounts dashboard filters by submission method

### 7. Enhanced Status Labels
**REQUIREMENT**: More descriptive status names matching requirements.

**CURRENT STATE**: Status constants exist but some names don't match requirements exactly.

**GAP**: Status naming alignment.

**SOLUTION**:
- Map existing statuses to requirement names:
  - SUBMITTED → SubmittedToAccreditationOfficer
  - CORRECTION_REQUESTED → ReturnedToApplicant
  - OFFICER_APPROVED → ApprovedByAccreditationOfficer
  - ACCOUNTS_REVIEW → AwaitingAccountsVerification
  - PAID_CONFIRMED → PaymentVerified
  - etc.
- Add display labels without changing database values

### 8. Messaging System Enhancement
**REQUIREMENT**: Return to Applicant must include message/reason.

**CURRENT STATE**: ApplicationMessage model exists, correction_notes field exists.

**GAP**: Need to ensure messages are always created when returning to applicant.

**SOLUTION**:
- Enforce message creation in correction request action
- Add applicant portal view to display messages
- Email notification when message sent

---

## 📋 IMPLEMENTATION PRIORITY

### HIGH PRIORITY (Core Workflow Gaps)
1. ✅ Production link in Officer sidebar
2. ⚠️ Applicant payment prompt/notification after Officer approval
3. ⚠️ Fix Request workflow (Registrar → Officer)
4. ⚠️ Payment submission method tracking

### MEDIUM PRIORITY (UX Improvements)
5. ⚠️ PayNow reference entry modal
6. ⚠️ Parallel routing visibility (Registrar + Accounts)
7. ⚠️ Enhanced status display labels
8. ⚠️ Messaging system enforcement

### LOW PRIORITY (Already Functional)
9. ✅ Category validation (already working)
10. ✅ Audit trail (already comprehensive)
11. ✅ RBAC (already enforced)
12. ✅ Dashboard queues (already filtered)

---

## 🎯 RECOMMENDED APPROACH

### Phase 1: Quick Wins (1-2 hours)
1. Add Production link to Officer sidebar
2. Add payment_submission_method field to applications table
3. Create status display label mapping
4. Enhance messaging enforcement

### Phase 2: Core Workflow (3-4 hours)
5. Create fix_requests table and migration
6. Implement Fix Request workflow (Registrar → Officer)
7. Add applicant payment notification service
8. Create applicant payment prompt view

### Phase 3: Payment Flow Enhancement (2-3 hours)
9. Implement PayNow reference entry modal
10. Add payment submission tracking
11. Update Accounts dashboard to show submission method

### Phase 4: Advanced Features (2-3 hours)
12. Implement parallel routing visibility
13. Add comprehensive applicant messaging portal
14. Create payment status timeline view

---

## 📊 COMPLIANCE MATRIX

| Requirement | Status | Implementation |
|------------|--------|----------------|
| RBAC | ✅ Complete | Middleware + routes |
| Status Machine | ✅ Complete | ApplicationWorkflow service |
| Audit Trail | ✅ Complete | ActivityLogger + audit tables |
| Category Validation | ✅ Complete | Validation in approve actions |
| Officer → Registrar Flow | ✅ Complete | OFFICER_APPROVED → REGISTRAR_REVIEW |
| Registrar → Accounts Flow | ✅ Complete | REGISTRAR_REVIEW → ACCOUNTS_REVIEW |
| Accounts → Production Flow | ✅ Complete | PAID_CONFIRMED → PRODUCTION_QUEUE |
| Concurrency Control | ✅ Complete | Lock mechanism |
| Payment Integration | ✅ Complete | PayNow + proof + waiver |
| Production Module | ✅ Complete | ProductionController exists |
| Parallel Routing | ⚠️ Partial | Needs visibility enhancement |
| Applicant Payment Prompt | ❌ Missing | Needs notification service |
| Fix Request Workflow | ❌ Missing | Needs table + workflow |
| Production Sidebar Link | ❌ Missing | Needs menu item |
| Payment Method Tracking | ⚠️ Partial | Needs explicit field |

---

## 🔧 MIGRATION STRATEGY

### DO NOT CREATE:
- ❌ applications table (already exists)
- ❌ audit_logs table (already exists)
- ❌ audit_trails table (already exists)
- ❌ payment_submissions table (use applications.payment_* fields)
- ❌ production_print_logs table (PrintLog model already exists)
- ❌ application_messages table (ApplicationMessage already exists)

### CREATE ONLY:
- ✅ fix_requests table (new feature)
- ✅ Add payment_submission_method to applications (enhancement)
- ✅ Add any missing indexes for performance

---

## 📝 NEXT STEPS

1. Review this gap analysis with stakeholders
2. Prioritize missing features based on business impact
3. Implement Phase 1 (Quick Wins) first
4. Test each phase thoroughly before proceeding
5. Update documentation as features are completed

---

**Generated**: 2026-02-25
**Analyst**: Kiro AI
**Status**: Ready for Implementation
