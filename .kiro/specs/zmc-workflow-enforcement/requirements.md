# ZMC Registration & Accreditation System - Workflow Enforcement Requirements

## Overview
Implement strict workflow enforcement for Zimbabwe Media Commission's integrated registration and accreditation system with RBAC, status machine, and complete audit trail.

## Core Workflow

### 1. Application Submission (Applicant)
**Portals:**
- Media Practitioner Portal (Accreditation - AP3)
- Media House Portal (Registration - AP1)

**Actions:**
- Fill application form with required fields
- Upload supporting documents (with type and size validation)
- Submit application
- **Result:** Status = `submitted_to_accreditation_officer`

### 2. Accreditation Officer Review (First Reviewer)
**Dashboard Queue:** Applications with status `submitted_to_accreditation_officer`

**Actions:**
- Review application fields and documents
- Assign official category from dropdown:
  - **Media Practitioner:** JE, JF, JO, JS, JM, JP, JD, JT
  - **Mass Media:** MC, MA, MF, MN, DG, MP, MS, MV

**Decision Paths:**
- **Return to Applicant:** 
  - Set status = `returned_to_applicant`
  - Provide reason/message
  - System sends notification to applicant
  
- **Approve:**
  - Set status = `approved_by_accreditation_officer`
  - **Parallel Routing:**
    - Application becomes visible to Registrar
    - Application becomes visible to Accounts/Payments
  - Trigger payment prompt to applicant

**Special Access:**
- Sidebar must include "Production" link (visible only to Accreditation Officer role)
- Production dashboard shows applications with status `payment_verified`

### 3. Payment Submission (Applicant - Triggered After Approval)
**Trigger:** Immediately after Accreditation Officer approval

**Payment Methods:**

#### A) PayNow Gateway
1. Click "Pay with PayNow" → Opens PayNow interface
2. Complete payment on PayNow platform
3. Click "Done" → Modal appears
4. Enter PayNow reference number received
5. Submit reference
6. **Result:** 
   - Status = `paynow_reference_submitted`
   - Payment method = `paynow`
   - Awaiting Accounts verification

#### B) Proof of Payment Upload
1. Upload proof of payment document
2. Submit proof
3. **Result:**
   - Status = `proof_of_payment_submitted`
   - Payment method = `proof_upload`
   - Awaiting Accounts verification

### 4. Registrar Review (Compliance Check)
**Dashboard Queue:** Applications with status `approved_by_accreditation_officer` or later

**Actions:**
- Review documents vs application fields
- Verify category correctness
- **Cannot edit applicant data directly**

**Decision Paths:**
- **Issue Found:**
  - Create "Fix Request" to Accreditation Officer
  - Set status = `registrar_raised_fix_request`
  - Describe required corrections
  - Accreditation Officer must fix and re-approve
  
- **Review Complete:**
  - Record "Registrar Review Completed"
  - Status = `registrar_review_completed`
  - **No payment authority**

### 5. Accounts/Payments Verification
**Dashboard Queue:** Applications with status `paynow_reference_submitted` OR `proof_of_payment_submitted`

**Verification Process:**

#### PayNow Verification
1. Check applicant-submitted reference against PayNow platform
2. **Match:** Approve → Status = `payment_verified`
3. **Mismatch:** Reject → Status = `payment_rejected` (with reason)

#### Proof Verification
1. Validate proof against internal payment records
2. **Valid:** Approve → Status = `payment_verified`
3. **Invalid:** Reject → Status = `payment_rejected` (with reason)

**Payment Rejection:**
- Applicant must re-submit reference or upload new proof
- Returns to payment submission step

### 6. Production (Accreditation Officer Responsible)
**Dashboard Queue:** Applications with status `payment_verified` ONLY

**Production Process:**
1. Auto-generate accreditation/registration number (security format)
2. Generate QR code with key record details
3. Print accreditation card (media practitioner) or certificate (media house)
4. Track printing:
   - Who generated
   - Who printed
   - Number of prints
   - Timestamps
   - Collection office selection
5. Set status = `produced_ready_for_collection`

## Status Machine

### Status Definitions
```
draft                              // Optional internal state
submitted_to_accreditation_officer // Initial submission
returned_to_applicant              // Returned with reason
approved_by_accreditation_officer  // First approval - triggers payment
awaiting_applicant_payment_action  // Waiting for payment submission
paynow_reference_submitted         // PayNow ref provided
proof_of_payment_submitted         // Proof uploaded
awaiting_accounts_verification     // In Accounts queue
payment_verified                   // Payment confirmed - can produce
payment_rejected                   // Payment invalid - resubmit
registrar_raised_fix_request       // Registrar found issue
fix_applied_by_accreditation_officer // Fix completed
registrar_review_completed         // Registrar satisfied
in_production                      // Being produced
produced_ready_for_collection      // Ready for pickup
collected                          // Collected by applicant
rejected_final                     // Final rejection with reason
```

### Status Transitions

| From Status | Action | To Status | Actor | Conditions |
|-------------|--------|-----------|-------|------------|
| draft | submit | submitted_to_accreditation_officer | Applicant | Form complete, docs uploaded |
| submitted_to_accreditation_officer | return | returned_to_applicant | Accreditation Officer | Reason required |
| submitted_to_accreditation_officer | approve | approved_by_accreditation_officer | Accreditation Officer | Category assigned |
| returned_to_applicant | resubmit | submitted_to_accreditation_officer | Applicant | Corrections made |
| approved_by_accreditation_officer | payment_prompt | awaiting_applicant_payment_action | System | Auto-triggered |
| awaiting_applicant_payment_action | submit_paynow_ref | paynow_reference_submitted | Applicant | Reference provided |
| awaiting_applicant_payment_action | upload_proof | proof_of_payment_submitted | Applicant | Proof uploaded |
| paynow_reference_submitted | verify_payment | payment_verified | Accounts Officer | Payment confirmed |
| paynow_reference_submitted | reject_payment | payment_rejected | Accounts Officer | Reason required |
| proof_of_payment_submitted | verify_payment | payment_verified | Accounts Officer | Payment confirmed |
| proof_of_payment_submitted | reject_payment | payment_rejected | Accounts Officer | Reason required |
| payment_rejected | resubmit_payment | paynow_reference_submitted/proof_of_payment_submitted | Applicant | New payment info |
| approved_by_accreditation_officer | raise_fix_request | registrar_raised_fix_request | Registrar | Issue description |
| registrar_raised_fix_request | apply_fix | fix_applied_by_accreditation_officer | Accreditation Officer | Corrections made |
| fix_applied_by_accreditation_officer | re_approve | approved_by_accreditation_officer | Accreditation Officer | Re-confirmation |
| approved_by_accreditation_officer | complete_review | registrar_review_completed | Registrar | No issues found |
| payment_verified | start_production | in_production | Accreditation Officer | Production initiated |
| in_production | complete_production | produced_ready_for_collection | Accreditation Officer | Card/cert printed |
| produced_ready_for_collection | mark_collected | collected | Staff | Collection confirmed |
| * | reject_final | rejected_final | Authorized Role | Reason required |

## Enforcement Rules (Hard Constraints)

### 1. Sequential Flow Enforcement
- ✅ No one can skip Accreditation Officer approval
- ✅ Applicant cannot access payment until `approved_by_accreditation_officer`
- ✅ Accounts cannot set `payment_verified` without payment submission
- ✅ Production cannot start unless status = `payment_verified`
- ✅ Registrar cannot approve payment or produce cards
- ✅ Accounts cannot edit application fields or category

### 2. Category Validation
- ✅ Category must be from official dropdown only
- ✅ Media Practitioner: JE, JF, JO, JS, JM, JP, JD, JT
- ✅ Mass Media: MC, MA, MF, MN, DG, MP, MS, MV
- ✅ Server-side validation on all category assignments

### 3. Reason Requirements
- ✅ Every return/reject/fix request must include reason
- ✅ Reason must be non-empty string
- ✅ Logged in audit trail

### 4. Document Requirements
- ✅ Type validation (PDF, JPG, PNG only)
- ✅ Size limits enforced (max 5MB per file)
- ✅ Required document types per application type

### 5. Payment Verification
- ✅ PayNow reference must be validated against platform
- ✅ Proof must be validated against internal records
- ✅ Cannot proceed without verification

## RBAC Permission Matrix

### Applicant (Media Practitioner/Media House)
**Permissions:**
- Create application (draft)
- Submit application
- Upload documents
- View own applications
- Resubmit after return
- Submit payment (PayNow ref or proof)
- Resubmit payment after rejection
- View application timeline
- View rejection reasons
- Receive messages/notifications

**Restrictions:**
- Cannot view other applicants' data
- Cannot change status directly
- Cannot access staff dashboards

### Accreditation Officer
**Permissions:**
- View queue: `submitted_to_accreditation_officer`
- Review applications and documents
- Assign category (from official list)
- Approve application
- Return to applicant (with reason)
- View fix requests from Registrar
- Apply fixes to applications
- Re-approve after fixes
- Access Production dashboard
- Generate accreditation numbers
- Print cards/certificates
- Track production
- View audit logs (own actions)

**Restrictions:**
- Cannot verify payments
- Cannot skip to production without payment verification
- Cannot edit after Registrar review without fix request

### Registrar
**Permissions:**
- View queue: `approved_by_accreditation_officer` and later
- Review application compliance
- Raise fix requests to Accreditation Officer
- Mark review as completed
- View audit logs
- View payment status (read-only)

**Restrictions:**
- Cannot edit applicant data directly
- Cannot approve/reject payments
- Cannot produce cards/certificates
- Cannot change application status directly

### Accounts/Payments Officer
**Permissions:**
- View queue: `paynow_reference_submitted`, `proof_of_payment_submitted`
- Verify PayNow references
- Verify proof of payment
- Approve payment → `payment_verified`
- Reject payment (with reason) → `payment_rejected`
- View payment history
- View audit logs (payment actions)

**Restrictions:**
- Cannot edit application identity fields
- Cannot change category
- Cannot produce cards/certificates
- Cannot approve applications

### Production Staff (if separate from Accreditation Officer)
**Permissions:**
- View queue: `payment_verified`
- Generate numbers
- Print cards/certificates
- Track prints
- Mark as produced

**Restrictions:**
- Cannot change application data
- Cannot verify payments
- Cannot approve applications

### Auditor
**Permissions:**
- View all audit logs
- Export audit reports
- View all application statuses
- Read-only access to all data

**Restrictions:**
- Cannot modify any data
- Cannot approve/reject
- Cannot change statuses

### Director/Super Admin
**Permissions:**
- View all dashboards
- View all audit logs
- Override statuses (with reason and audit)
- Access all reports
- Manage user roles

**Restrictions:**
- All actions must be audited
- Cannot bypass workflow without explicit override flag

## Dashboard Queues

### Accreditation Officer Dashboard
**Tabs/Sections:**
1. **New Submissions** - Status: `submitted_to_accreditation_officer`
2. **Returned Items** - Status: `returned_to_applicant` (for follow-up)
3. **Fix Requests** - Status: `registrar_raised_fix_request`
4. **Approved** - Status: `approved_by_accreditation_officer` (monitoring)
5. **Production Queue** - Status: `payment_verified` (ready to produce)
6. **In Production** - Status: `in_production`
7. **Completed** - Status: `produced_ready_for_collection`

### Registrar Dashboard
**Tabs/Sections:**
1. **Pending Review** - Status: `approved_by_accreditation_officer`
2. **Fix Requests Raised** - Status: `registrar_raised_fix_request`
3. **Review Completed** - Status: `registrar_review_completed`
4. **All Applications** - All statuses (read-only)

### Accounts Dashboard
**Tabs/Sections:**
1. **Awaiting Verification** - Status: `paynow_reference_submitted`, `proof_of_payment_submitted`
2. **Payment Rejected** - Status: `payment_rejected` (follow-ups)
3. **Payment Verified** - Status: `payment_verified` (history)
4. **All Payments** - Payment history and reports

### Applicant Dashboard
**Sections:**
1. **My Applications** - All own applications
2. **Application Timeline** - Status history with timestamps
3. **Payment Prompts** - Active payment requests
4. **Messages** - Returns, rejections, fix requests
5. **Documents** - Uploaded documents

## Audit Trail Requirements

### Audit Log Structure
```php
audit_logs table:
- id
- application_id (foreign key)
- actor_role (enum: applicant, accreditation_officer, registrar, accounts, production, system)
- actor_user_id (foreign key to users)
- action_type (enum: submit, return, approve, assign_category, raise_fix, apply_fix, 
               submit_payment, verify_payment, reject_payment, start_production, 
               print, mark_collected, reject_final, etc.)
- before_state (JSON: status + key fields)
- after_state (JSON: status + key fields)
- reason (text, nullable but required for returns/rejections)
- notes (text, nullable)
- ip_address
- user_agent
- timestamp (created_at)
```

### Audit Events (Mandatory Logging)
1. Application submission
2. Status changes (all transitions)
3. Category assignments/changes
4. Document uploads
5. Returns to applicant (with reason)
6. Approvals
7. Fix requests raised
8. Fixes applied
9. Payment submissions
10. Payment verifications/rejections
11. Production starts
12. Prints (who, when, how many)
13. Collection confirmations
14. Final rejections

### Audit UI
**Features:**
- Filterable by application, user, role, action type, date range
- Exportable to CSV/PDF
- Visible to: Accreditation Officer, Registrar, Auditor, Director
- Shows complete timeline per application
- Highlights critical actions (approvals, rejections, payments)

## Edge Cases & Handling

### 1. Payment Rejected
**Scenario:** Accounts rejects payment due to invalid reference/proof
**Handling:**
- Status → `payment_rejected`
- Reason logged and shown to applicant
- Applicant receives notification
- Applicant can resubmit payment (new reference or proof)
- Returns to `paynow_reference_submitted` or `proof_of_payment_submitted`
- Accounts re-verifies

### 2. Registrar Fix Request
**Scenario:** Registrar finds error in approved application
**Handling:**
- Registrar creates fix request with description
- Status → `registrar_raised_fix_request`
- Accreditation Officer receives notification
- Accreditation Officer applies fix
- Status → `fix_applied_by_accreditation_officer`
- Accreditation Officer re-approves
- Status → `approved_by_accreditation_officer`
- Registrar can review again

### 3. Applicant Resubmission After Return
**Scenario:** Accreditation Officer returns application
**Handling:**
- Status → `returned_to_applicant`
- Applicant receives reason/message
- Applicant makes corrections
- Applicant resubmits
- Status → `submitted_to_accreditation_officer`
- Re-enters Accreditation Officer queue

### 4. Re-approval Loops
**Scenario:** Multiple fix requests or returns
**Handling:**
- Each cycle is fully audited
- Status transitions tracked
- Prevent infinite loops with max iteration limit (e.g., 5 returns)
- After limit, escalate to Director for manual review

### 5. Payment Method Change
**Scenario:** Applicant submits PayNow ref, gets rejected, wants to upload proof instead
**Handling:**
- Allow payment method change on resubmission
- Clear previous payment data
- Update payment_method field
- Re-enter Accounts queue

### 6. Concurrent Edits
**Scenario:** Multiple staff members access same application
**Handling:**
- Implement optimistic locking (version field)
- Show "Application being edited by [User]" warning
- Prevent conflicting status changes
- Last action wins with audit trail

### 7. Production Reprints
**Scenario:** Card/certificate needs reprint (damaged, lost)
**Handling:**
- Accreditation Officer can reprint from Production dashboard
- Log each print with reason
- Track print count
- Charge reprint fee if applicable

## Implementation Checklist

### Database
- [ ] Create/update applications table with all status fields
- [ ] Create audit_logs table
- [ ] Create payment_submissions table
- [ ] Create fix_requests table
- [ ] Create production_print_logs table
- [ ] Create messages/notifications table
- [ ] Add category validation constraints
- [ ] Add status enum constraints
- [ ] Add foreign keys and indexes

### Models & Business Logic
- [ ] Application model with status machine
- [ ] Status transition validation methods
- [ ] Category validation (official lists)
- [ ] Audit logging trait/service
- [ ] Payment verification service
- [ ] Production number generation service
- [ ] QR code generation service
- [ ] Document validation service

### Controllers & Routes
- [ ] Applicant portal controllers (submit, resubmit, payment)
- [ ] Accreditation Officer controllers (review, approve, return, production)
- [ ] Registrar controllers (review, fix requests)
- [ ] Accounts controllers (verify, reject payment)
- [ ] Production controllers (generate, print, track)
- [ ] Audit controllers (view logs, export)
- [ ] Route middleware for RBAC enforcement

### Middleware & Guards
- [ ] Role-based access middleware
- [ ] Status-based action guards
- [ ] Payment verification guards
- [ ] Production access guards
- [ ] Audit logging middleware

### Views & UI
- [ ] Accreditation Officer dashboard with queues
- [ ] Registrar dashboard with queues
- [ ] Accounts dashboard with queues
- [ ] Applicant dashboard with timeline
- [ ] Payment submission modals (PayNow + Proof)
- [ ] Fix request forms
- [ ] Production interface
- [ ] Audit log viewer
- [ ] Sidebar: Production link for Accreditation Officer

### Notifications
- [ ] Email notifications for status changes
- [ ] SMS notifications (optional)
- [ ] In-app notifications
- [ ] Payment prompts
- [ ] Return/rejection reasons
- [ ] Fix request alerts

### Testing
- [ ] Unit tests for status transitions
- [ ] Integration tests for workflow
- [ ] RBAC permission tests
- [ ] Payment verification tests
- [ ] Audit logging tests
- [ ] Edge case tests (rejections, loops, etc.)

### Documentation
- [ ] User guides per role
- [ ] API documentation
- [ ] Status machine diagram
- [ ] RBAC matrix
- [ ] Deployment guide

## Acceptance Criteria

✅ **Workflow Enforcement:**
- [ ] User cannot reach Production without `payment_verified` status
- [ ] User cannot pay until Accreditation Officer approved
- [ ] Registrar fix requests force Accreditation Officer correction
- [ ] Accounts verification requires payment submission
- [ ] All transitions generate audit logs
- [ ] Categories validated against official codes only

✅ **RBAC:**
- [ ] Each role can only access permitted actions
- [ ] Unauthorized access returns 403
- [ ] Role-based dashboard queues work correctly

✅ **Status Machine:**
- [ ] Invalid status transitions are blocked
- [ ] Status changes are atomic
- [ ] Concurrent updates handled correctly

✅ **Audit Trail:**
- [ ] All actions logged with actor, timestamp, before/after states
- [ ] Audit UI shows complete history
- [ ] Audit logs are immutable
- [ ] Export functionality works

✅ **Payment:**
- [ ] PayNow reference submission works
- [ ] Proof upload works
- [ ] Verification/rejection flows correctly
- [ ] Resubmission after rejection works

✅ **Production:**
- [ ] Only `payment_verified` applications accessible
- [ ] Number generation follows security format
- [ ] QR codes generated correctly
- [ ] Print tracking works
- [ ] Accreditation Officer has Production sidebar link

## Next Steps

1. Review and approve requirements
2. Create database migrations
3. Implement status machine and validation
4. Build RBAC middleware
5. Implement audit logging
6. Build dashboards and queues
7. Implement payment flows
8. Build production module
9. Test all workflows
10. Deploy and train users
