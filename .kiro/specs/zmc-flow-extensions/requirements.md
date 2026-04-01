# ZMC Flow Extensions - Requirements Specification

## Document Information
**Version**: 1.0  
**Date**: 2026-02-25  
**Project**: ZMC Flow Extensions - Waivers + Registrar Oversight + Media House Two-Stage Payments  
**Status**: Requirements Analysis

---

## Executive Summary

This specification defines three major workflow extensions to the ZMC system:

1. **Waiver Payment Process**: Support for waiver submissions as payment proof with special routing
2. **Registrar Payment Oversight**: Read-only oversight of Accounts/Payments activities
3. **Media House Two-Stage Payment**: Separate Application Fee and Registration Fee with official letter requirement

---

## Process 1: Complicated Payment Methods (Waivers) + Forward Without Approval

### Overview
Some applications require special handling (waivers, special cases) where the Accreditation Officer must forward to Registrar WITHOUT approving first.

### Requirements

#### 1.1 Accreditation Officer Actions

**Action A: "Approve & Route"** (Standard)
- Used for normal applications
- After approval: follows standard routing rules
- Status: OFFICER_APPROVED → REGISTRAR_REVIEW

**Action B: "Forward to Registrar (No Approval)"** (Special)
- Used for waiver/complicated payment applications
- Does NOT mean application is approved
- Routes ONLY to Registrar review queue
- Status: FORWARDED_TO_REGISTRAR_NO_APPROVAL
- **Mandatory**: Reason field (e.g., "Waiver submitted", "Special case")
- Audit log captures: reason, timestamp, officer identity

#### 1.2 Registrar Review for Special Cases

**Receives**: Applications with status FORWARDED_TO_REGISTRAR_NO_APPROVAL

**Actions Available**:
1. **Reject**: With mandatory reason → Status: REGISTRAR_REJECTED
2. **Request Corrections**: Send fix request to Officer → Status: RETURNED_TO_OFFICER
3. **Approve**: After full review → Status: PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR

**Constraints**:
- Must review all application details
- Must verify category assignment
- Must check all documents
- Cannot skip to payment verification

#### 1.3 Waiver as Payment Proof

**Waiver Submission**:
- Treated as payment submission type in Accounts
- Can be uploaded by Applicant OR Accreditation Officer
- Document type: "WaiverProof"
- Metadata captured:
  - Beneficiary name
  - Waiver offered by (authority)
  - Waiver date
  - Waiver reason
  - Supporting documents

**Accounts Verification**:
- Verify waiver validity
- Check authorization
- Validate against waiver rules
- **If Accepted**: Status → PAYMENT_VERIFIED
- **If Rejected**: Status → PAYMENT_REJECTED (with reason, return to applicant for alternative payment)

#### 1.4 Registrar Payment Oversight

**Access Level**: READ-ONLY

**Can View**:
- Payment submissions (PayNow references, proofs, waivers)
- Verification outcomes with timestamps
- Accounts Officer identity for each verification
- Payment audit trail
- Queue metrics and statistics

**Cannot Do**:
- Verify/approve payments
- Reject payments
- Modify payment records
- Access payment verification actions

**Dashboard Page**: "Payments Oversight"

**Displays**:
- Counts by payment status (Pending, Verified, Rejected)
- Recently verified payments (last 50)
- Recently rejected payments (last 50)
- Payment method breakdown (PayNow, Proof, Waiver)
- Drilldown into payment audit logs
- Search by application reference
- Filter by date range, status, method

---

## Process 2: Media House Registration (AP1) Two-Stage Payment

### Overview
Media House Registration requires TWO separate payments:
1. **Application Fee**: Paid at submission (before Officer review)
2. **Registration Fee**: Paid after Registrar approval (with official letter)

### Requirements

#### 2.1 Submission + Application Fee (Stage 1)

**Timing**: At the moment of application submission

**Required Documents** (AP1):
- Certified IDs for directors
- Mission statement
- Code of ethics
- Code of conduct
- Style book
- Editorial charter
- Market analysis
- Financial projections
- Certificate of incorporation
- Memorandum and articles
- Other supporting documents

**Application Fee Payment Methods**:

**Method 1: PayNow Application Fee**
1. Click "Pay Application Fee with PayNow"
2. Redirect to PayNow interface
3. Complete payment
4. Click "Done"
5. Modal appears: "Enter PayNow Reference Number"
6. Input reference number (mandatory)
7. System saves:
   - `application_fee_payment.method = 'PAYNOW'`
   - `application_fee_payment.reference = [entered reference]`
   - `application_fee_payment.submitted_at = now()`
8. Status: SUBMITTED_WITH_APP_FEE

**Method 2: Upload Application Fee Proof**
1. On submit, modal appears
2. Capture:
   - Payer name (optional)
   - Date paid (required)
   - Amount (required)
   - Reference/receipt number (optional)
   - Upload proof document(s) (required)
3. System saves:
   - `application_fee_payment.method = 'PROOF_UPLOAD'`
   - `application_fee_payment.proof_path = [file path]`
   - `application_fee_payment.submitted_at = now()`
4. Status: SUBMITTED_WITH_APP_FEE

**Hard Rule**: Media house application CANNOT be submitted without Application Fee submission (PayNow reference OR proof upload). No skip option.

#### 2.2 Accreditation Officer Review

**Receives**: Applications with status SUBMITTED_WITH_APP_FEE

**Verification Scope**:
- All required fields completed
- All required supporting documents attached
- Category selection (mass media category codes only)
- Document quality and validity

**Note**: Application fee verification is NOT done here (Accounts does this later)

**Actions**:
1. **Return to Applicant**: If incomplete
   - Mandatory reason
   - Status: RETURNED_TO_APPLICANT
   - **Preserve**: Application fee payment records
   
2. **Verify & Push to Registrar**: If complete
   - Status: VERIFIED_BY_OFFICER_PENDING_REGISTRAR

#### 2.3 Registrar Review + Official Letter

**Receives**: Applications with status VERIFIED_BY_OFFICER_PENDING_REGISTRAR

**Review Process**:
1. Review full application
2. Verify all details
3. Check category assignment

**If Errors Found**:
- Send "Fix Request" to Accreditation Officer
- Include structured notes
- Status: REGISTRAR_RAISED_FIX_REQUEST
- Officer applies fixes and re-verifies

**If Satisfied - Approval Process**:
1. Click "Approve" button
2. **Mandatory**: Upload "Official Letter" (PDF/image)
3. **Validation**: Cannot approve without official letter
4. System saves:
   - Official letter file
   - Upload timestamp
   - Registrar identity
5. Status: REGISTRAR_APPROVED_PENDING_REG_FEE
6. Application routed to Accounts queue
7. **Trigger**: System prompts applicant to pay Registration Fee

#### 2.4 Registration Fee Payment (Stage 2)

**Timing**: After Registrar approval with official letter

**Trigger**: Status = REGISTRAR_APPROVED_PENDING_REG_FEE

**Registration Fee Payment Methods**:

**Method 1: PayNow Registration Fee**
1. Applicant portal shows "Pay Registration Fee" prompt
2. Click "Pay with PayNow"
3. Redirect to PayNow interface
4. Complete payment
5. Click "Done"
6. Modal: "Enter PayNow Reference Number"
7. Input reference (mandatory)
8. System saves:
   - `registration_fee_payment.method = 'PAYNOW'`
   - `registration_fee_payment.reference = [entered reference]`
   - `registration_fee_payment.submitted_at = now()`
9. Status: REG_FEE_SUBMITTED_AWAITING_VERIFICATION

**Method 2: Upload Registration Fee Proof**
1. Applicant portal shows "Upload Registration Fee Proof" option
2. Modal captures:
   - Payer name (optional)
   - Date paid (required)
   - Amount (required)
   - Reference/receipt number (optional)
   - Upload proof document(s) (required)
3. System saves:
   - `registration_fee_payment.method = 'PROOF_UPLOAD'`
   - `registration_fee_payment.proof_path = [file path]`
   - `registration_fee_payment.submitted_at = now()`
4. Status: REG_FEE_SUBMITTED_AWAITING_VERIFICATION

#### 2.5 Accounts Verification (Both Fees)

**Receives**: Applications with status REG_FEE_SUBMITTED_AWAITING_VERIFICATION

**Verification Scope**:
1. **Application Fee** (read-only check):
   - Verify submission exists
   - Note method and reference
   - No action required (already submitted)

2. **Registration Fee** (active verification):
   - **If PayNow**: Validate reference on PayNow platform
   - **If Proof**: Validate against internal bank records
   - **If Waiver** (optional): Validate waiver rules if configured

**Outcomes**:
- **Valid**: Status → PAYMENT_VERIFIED
- **Invalid**: Status → PAYMENT_REJECTED
  - Mandatory reason
  - Applicant must resubmit registration fee payment
  - Application fee remains valid

#### 2.6 Production (Accreditation Officer)

**Access**: Only applications with status PAYMENT_VERIFIED

**Operator**: Accreditation Officer

**Process**:
1. Generate certificate/license
2. Auto-generate registration number
3. Embed QR code
4. Print tracking:
   - Who generated
   - Who printed
   - Number of prints
   - Timestamps
   - Office/collection location
5. Status: PRODUCED_READY_FOR_COLLECTION

**UI Requirement**: Accreditation Officer sidebar must include "Production" link

---

## Status Machine

### New Statuses Required

**Waiver/Special Path**:
- `FORWARDED_TO_REGISTRAR_NO_APPROVAL`
- `PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR`

**Media House Two-Stage Path**:
- `APP_FEE_PAYMENT_INITIATED` (optional internal)
- `SUBMITTED_WITH_APP_FEE`
- `VERIFIED_BY_OFFICER_PENDING_REGISTRAR`
- `REGISTRAR_APPROVED_PENDING_REG_FEE`
- `REG_FEE_SUBMITTED_AWAITING_VERIFICATION`

**Common Statuses** (reused):
- `RETURNED_TO_APPLICANT`
- `REGISTRAR_RAISED_FIX_REQUEST`
- `PAYMENT_VERIFIED`
- `PAYMENT_REJECTED`
- `IN_PRODUCTION`
- `PRODUCED_READY_FOR_COLLECTION`

### Status Transition Rules

```
SUBMITTED
  → FORWARDED_TO_REGISTRAR_NO_APPROVAL (Officer: Forward without approval)
  → OFFICER_REVIEW (Officer: Standard review)

FORWARDED_TO_REGISTRAR_NO_APPROVAL
  → REGISTRAR_REJECTED (Registrar: Reject)
  → RETURNED_TO_OFFICER (Registrar: Request fix)
  → PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR (Registrar: Approve)

SUBMITTED_WITH_APP_FEE
  → RETURNED_TO_APPLICANT (Officer: Incomplete)
  → VERIFIED_BY_OFFICER_PENDING_REGISTRAR (Officer: Complete)

VERIFIED_BY_OFFICER_PENDING_REGISTRAR
  → REGISTRAR_RAISED_FIX_REQUEST (Registrar: Errors found)
  → REGISTRAR_APPROVED_PENDING_REG_FEE (Registrar: Approve + upload letter)

REGISTRAR_APPROVED_PENDING_REG_FEE
  → REG_FEE_SUBMITTED_AWAITING_VERIFICATION (Applicant: Submit reg fee)

REG_FEE_SUBMITTED_AWAITING_VERIFICATION
  → PAYMENT_VERIFIED (Accounts: Approve)
  → PAYMENT_REJECTED (Accounts: Reject)

PAYMENT_VERIFIED
  → IN_PRODUCTION (Production: Start)

IN_PRODUCTION
  → PRODUCED_READY_FOR_COLLECTION (Production: Complete)
```

---

## RBAC Matrix

| Role | Permissions |
|------|-------------|
| **Applicant** | - Submit application with app fee<br>- Upload documents<br>- Submit registration fee<br>- View own application timeline<br>- Respond to returns/rejections<br>- Resubmit payments if rejected |
| **Accreditation Officer** | - Review applications<br>- Return to applicant<br>- Categorize<br>- Verify & approve (standard)<br>- Forward to Registrar without approval (special)<br>- Upload waivers<br>- Access Production module<br>- Generate/print certificates<br>- **CANNOT**: Verify payments |
| **Registrar** | - Review applications<br>- Approve with official letter upload (media house)<br>- Send fix requests<br>- Reject applications<br>- **READ-ONLY**: Payment oversight<br>- View payment audit logs<br>- View payment queue metrics<br>- **CANNOT**: Verify/approve payments<br>- **CANNOT**: Modify payment records |
| **Accounts/Payments** | - Verify application fees (read-only)<br>- Verify registration fees (active)<br>- Approve/reject payment proofs<br>- Verify waivers<br>- Set PAYMENT_VERIFIED/REJECTED<br>- **CANNOT**: Edit application fields<br>- **CANNOT**: Change categories<br>- **CANNOT**: Access production |

---

## Data Model

### PaymentSubmission (Enhanced)

```php
id
application_id
payment_stage  // 'application_fee' | 'registration_fee'
method         // 'PAYNOW' | 'PROOF_UPLOAD' | 'WAIVER'
reference      // PayNow reference or receipt number
amount
currency
status         // 'submitted' | 'verified' | 'rejected'
submitted_at
verified_at
verified_by
rejection_reason
proof_path     // For proof uploads
proof_metadata // JSON: payer_name, date_paid, etc.
waiver_path    // For waivers
waiver_metadata // JSON: beneficiary, offered_by, etc.
created_at
updated_at
```

### OfficialLetter

```php
id
application_id
uploaded_by    // Registrar user_id
file_path
file_name
file_size
file_hash
uploaded_at
created_at
updated_at
```

### RegistrarPaymentOversight (View/Query Helper)

```sql
-- Not a table, but a view/query that Registrar can access
SELECT 
  p.id,
  p.application_id,
  a.reference as app_reference,
  p.payment_stage,
  p.method,
  p.status,
  p.submitted_at,
  p.verified_at,
  u.name as verified_by_name,
  p.rejection_reason
FROM payment_submissions p
JOIN applications a ON p.application_id = a.id
LEFT JOIN users u ON p.verified_by = u.id
ORDER BY p.created_at DESC
```

---

## Audit Logging Requirements

### Every Action Must Log:

```php
[
  'application_id' => $id,
  'actor_role' => $role,
  'actor_user_id' => $userId,
  'action_type' => $action,  // e.g., 'forward_without_approval', 'upload_official_letter'
  'before_status' => $oldStatus,
  'after_status' => $newStatus,
  'reason' => $reason,  // Mandatory for: return, reject, forward-no-approval, payment-reject
  'metadata' => [
    'attachments' => [...],  // Official letter, waiver, payment proof
    'payment_stage' => '...',
    'payment_method' => '...',
    'reference_number' => '...',
  ],
  'timestamp' => now(),
  'ip_address' => $ip,
  'user_agent' => $userAgent,
]
```

### Specific Actions to Log:

1. `forward_without_approval` - Officer forwards to Registrar
2. `upload_official_letter` - Registrar uploads letter
3. `submit_application_fee` - Applicant pays app fee
4. `submit_registration_fee` - Applicant pays reg fee
5. `verify_payment` - Accounts verifies payment
6. `reject_payment` - Accounts rejects payment
7. `upload_waiver` - Waiver document uploaded
8. `verify_waiver` - Accounts verifies waiver
9. `registrar_view_payment_oversight` - Registrar accesses oversight page

---

## UI Requirements

### Accreditation Officer Dashboard

**New Actions**:
- "Approve & Route" button (standard)
- "Forward to Registrar (No Approval)" button (special)
  - Opens modal for mandatory reason
  - Dropdown: Common reasons + "Other"
  - Text area for details

**Sidebar**:
- "Production" link (already added in Phase 1)

### Registrar Dashboard

**New Page**: "Payments Oversight"

**Layout**:
```
┌─────────────────────────────────────────────────────┐
│ Payments Oversight (Read-Only)                     │
├─────────────────────────────────────────────────────┤
│ KPIs:                                               │
│ [Pending: 45] [Verified: 120] [Rejected: 8]       │
│ [PayNow: 80] [Proof: 60] [Waiver: 25]             │
├─────────────────────────────────────────────────────┤
│ Filters:                                            │
│ Status: [All ▼] Method: [All ▼] Date: [Range]     │
├─────────────────────────────────────────────────────┤
│ Recent Verifications:                               │
│ APP-1234 | PayNow | Verified | John Doe | 2h ago  │
│ APP-1235 | Proof  | Verified | Jane Smith | 3h ago│
├─────────────────────────────────────────────────────┤
│ Recent Rejections:                                  │
│ APP-1236 | Waiver | Rejected | Bob Wilson | 1d ago│
│ Reason: Invalid waiver authorization               │
└─────────────────────────────────────────────────────┘
```

**Sidebar**:
- "Payments Oversight" link

### Applicant Portal

**Media House Submission**:
1. Fill application form
2. Upload all required documents
3. Click "Submit Application"
4. **Modal appears**: "Application Fee Payment Required"
   - Option 1: Pay with PayNow
   - Option 2: Upload Proof of Payment
5. After payment submission: "Application Submitted Successfully"

**After Registrar Approval**:
1. Portal shows: "Registration Fee Payment Required"
2. Download official letter from Registrar
3. Pay registration fee:
   - Option 1: Pay with PayNow
   - Option 2: Upload Proof of Payment
4. After payment: "Registration Fee Submitted - Awaiting Verification"

### Accounts Dashboard

**Enhanced View**:
- Show payment stage (Application Fee / Registration Fee)
- For media house: Show both fees in timeline
- Application fee: Read-only indicator
- Registration fee: Active verification buttons

---

## Edge Cases & Validations

### 1. Applicant Returns After App Fee Submitted
**Scenario**: Officer returns application to applicant for corrections

**Handling**:
- Preserve application fee payment record
- Do NOT require re-payment of application fee
- Applicant fixes issues and resubmits
- Application fee remains valid
- Status: RETURNED_TO_APPLICANT → SUBMITTED_WITH_APP_FEE (after resubmit)

### 2. Registrar Fix Loop
**Scenario**: Registrar sends multiple fix requests

**Handling**:
- Track all fix requests in fix_requests table
- Each fix creates new record
- Officer resolves each fix
- Application returns to Registrar after each resolution
- Limit: Maximum 5 fix requests per application (configurable)

### 3. Payment Rejected and Resubmitted
**Scenario**: Accounts rejects registration fee, applicant resubmits

**Handling**:
- Original payment record: status = 'rejected'
- Create new payment record for resubmission
- Link both to same application
- Track resubmission count
- Audit log shows full payment history

### 4. Waiver Accepted/Rejected
**Scenario**: Waiver submitted as payment proof

**Handling**:
- **Accepted**: 
  - Status: PAYMENT_VERIFIED
  - Audit log: waiver details + verification notes
  - Proceed to production
- **Rejected**:
  - Status: PAYMENT_REJECTED
  - Mandatory reason
  - Applicant must submit alternative payment (PayNow or Proof)
  - Waiver record preserved for audit

### 5. Duplicate Reference Numbers
**Scenario**: Two applications use same PayNow reference

**Handling**:
- Validation: Check for duplicate references
- If duplicate found:
  - Flag for manual review
  - Notify Accounts officer
  - Require additional verification
  - Audit log: duplicate reference alert

### 6. Official Letter Missing
**Scenario**: Registrar tries to approve without uploading letter

**Handling**:
- **Server-side validation**: Block approval action
- **UI validation**: Disable approve button until letter uploaded
- **Error message**: "Official letter is required for media house approval"
- **Audit log**: Attempted approval without letter (if server-side triggered)

---

## Implementation Priority

### Phase 1: Database & Models (High Priority)
1. Add new status constants to Application model
2. Create PaymentSubmission model (enhanced)
3. Create OfficialLetter model
4. Create migrations for new tables/fields
5. Update ApplicationWorkflow service with new transitions

### Phase 2: Waiver Process (High Priority)
1. Add "Forward without approval" action to Officer controller
2. Update Registrar controller for special case handling
3. Add waiver upload functionality
4. Update Accounts controller for waiver verification
5. Create waiver verification UI

### Phase 3: Media House Two-Stage Payment (High Priority)
1. Add application fee payment at submission
2. Update Officer controller for media house verification
3. Add official letter upload to Registrar controller
4. Add registration fee payment prompt
5. Update Accounts controller for two-stage verification
6. Create payment stage UI components

### Phase 4: Registrar Payment Oversight (Medium Priority)
1. Create read-only payment oversight queries
2. Add Registrar payment oversight controller methods
3. Create payment oversight dashboard view
4. Add audit log viewing functionality
5. Add sidebar link

### Phase 5: Testing & Documentation (High Priority)
1. Unit tests for all new methods
2. Integration tests for workflows
3. UI/UX testing
4. Update user training guide
5. Update deployment guide

---

## Success Criteria

### Functional Requirements ✓
- [ ] Officer can forward to Registrar without approval
- [ ] Waiver can be submitted and verified
- [ ] Media house requires application fee at submission
- [ ] Registrar must upload official letter to approve media house
- [ ] Registration fee prompted after Registrar approval
- [ ] Accounts verifies both payment stages
- [ ] Registrar has read-only payment oversight
- [ ] All status transitions enforced server-side
- [ ] Complete audit trail maintained

### Non-Functional Requirements ✓
- [ ] No breaking changes to existing workflows
- [ ] Performance: Page load < 2 seconds
- [ ] Security: RBAC enforced at API level
- [ ] Usability: Intuitive UI matching existing design
- [ ] Reliability: 99.9% uptime
- [ ] Maintainability: Well-documented code

---

**Document Version**: 1.0  
**Status**: Ready for Design Phase  
**Next Step**: Create technical design document
