# ZMC Complete System Flow Correction - Requirements

## Overview
Complete refactoring and enforcement of workflow logic in the Zimbabwe Media Commission Integrated Registration & Accreditation System with strict server-side validation, RBAC enforcement, and comprehensive audit logging.

## SECTION 1: STANDARD ACCREDITATION FLOW (MEDIA PRACTITIONERS)

### Flow Diagram
```
Applicant → Accreditation Officer → (Registrar + Accounts) → Accounts Verification → Production
```

### 1.1 Applicant Submission
**Action**: Applicant submits New Accreditation (Journalist/Media Practitioner)

**Status Transition**: 
- `draft` → `submitted_to_accreditation_officer`

**Rules**:
- Application must have all required fields completed
- Required documents must be uploaded
- Only Accreditation Officer can see the application initially
- Registrar and Accounts cannot see the application at this stage

**Server-Side Validation**:
- Validate all required fields are present
- Validate document uploads (type, size, required documents)
- Prevent submission if validation fails

### 1.2 Accreditation Officer Review
**Actions Available**:
1. **Review** - View application fields and uploaded documents
2. **Return to Applicant** - Send back with mandatory reason
3. **Approve** - Approve and forward to Registrar + Accounts
4. **Forward without Approval** - Special cases (waiver/complicated)

#### 1.2.1 Return to Applicant
**Status Transition**: 
- `submitted_to_accreditation_officer` → `returned_to_applicant`

**Rules**:
- Reason/message is mandatory
- Applicant receives notification
- Applicant can resubmit after corrections
- Upon resubmission: `returned_to_applicant` → `submitted_to_accreditation_officer`

#### 1.2.2 Approve Application
**Status Transition**: 
- `submitted_to_accreditation_officer` → `approved_by_officer_awaiting_payment`

**Rules**:
- Category code must be assigned (official codes only)
- Application becomes visible to:
  - Registrar (for oversight/review)
  - Accounts (for payment verification)
- System immediately prompts applicant to make payment
- Cannot skip payment step

**Server-Side Validation**:
- Validate category code is from official list
- Validate officer has permission to approve
- Create audit log entry

#### 1.2.3 Forward without Approval (Special Cases)
**Status Transition**: 
- `submitted_to_accreditation_officer` → `forwarded_to_registrar_no_approval`

**Rules**:
- Used for waiver cases or complicated applications
- Does NOT mark as approved
- Registrar reviews first
- Registrar can then push to Accounts
- Accounts verifies waiver as payment proof

### 1.3 Payment Prompt (After Accreditation Officer Approval)

#### Option A: PayNow
**Flow**:
1. Applicant clicks "Pay with PayNow"
2. Redirect to PayNow interface
3. After payment, applicant clicks "Done"
4. Modal appears: "Enter PayNow Reference Number"
5. Applicant enters reference and submits

**Status Transition**: 
- `approved_by_officer_awaiting_payment` → `awaiting_accounts_verification`

**Rules**:
- PayNow reference is mandatory
- Reference must be unique
- Reference stored in payment_submissions table

#### Option B: Upload Payment Proof
**Flow**:
1. Modal opens
2. Applicant uploads proof document
3. Submits

**Status Transition**: 
- `approved_by_officer_awaiting_payment` → `awaiting_accounts_verification`

**Rules**:
- Proof document is mandatory
- File type validation (PDF, JPG, PNG)
- File size limit (5MB)
- Document stored in payment_submissions table

## SECTION 2: REGISTRAR DASHBOARD

### 2.1 Registrar Access
**Receives**:
- Applications approved by Accreditation Officer
- Applications forwarded without approval (special cases)

**Statuses Visible**:
- `approved_by_officer_awaiting_payment`
- `forwarded_to_registrar_no_approval`
- `awaiting_accounts_verification`
- `payment_verified`

### 2.2 Registrar Actions

#### 2.2.1 Review Documents
**Action**: Review documents vs form details

**If Errors Exist**:
- Send Fix Request to Accreditation Officer
- Status: `approved_by_officer_awaiting_payment` → `registrar_raised_fix_request`
- Mandatory reason/notes
- Accreditation Officer receives notification
- Officer fixes and resubmits to Registrar

#### 2.2.2 Mark Review Complete
**Action**: Mark review as complete

**Rules**:
- Does NOT verify payment
- Only confirms document review is complete
- Application remains in current status
- Adds review completion flag to application

### 2.3 Registrar Payment Oversight (READ-ONLY)
**Access**:
- View payment submissions
- View PayNow references
- View payment approvals/rejections
- View audit logs

**Rules**:
- READ-ONLY access
- Cannot verify payments
- Cannot modify payment records
- Can view for oversight purposes only

### 2.4 Special Case Handling
**For applications with status** `forwarded_to_registrar_no_approval`:

**Actions**:
1. Review application
2. If approved: Push to Accounts dashboard
3. Status: `forwarded_to_registrar_no_approval` → `pending_accounts_review_from_registrar`

**Rules**:
- Registrar approval required before Accounts sees it
- Accounts treats as waiver case
- Accounts verifies waiver documentation

## SECTION 3: ACCOUNTS / PAYMENTS OFFICER

### 3.1 Accounts Access
**Receives**:
- Applications with PayNow reference or proof uploaded
- Applications pushed by Registrar (special cases)

**Statuses Visible**:
- `awaiting_accounts_verification`
- `pending_accounts_review_from_registrar`

### 3.2 Verification Logic

#### 3.2.1 PayNow Verification
**Process**:
1. Officer checks reference against PayNow platform
2. If match: Approve
3. If mismatch: Reject with reason

**Approve**:
- Status: `awaiting_accounts_verification` → `payment_verified`
- Application automatically routed to Production Dashboard
- Applicant receives notification

**Reject**:
- Status: `awaiting_accounts_verification` → `payment_rejected`
- Reason is mandatory
- Applicant receives notification
- Applicant can resubmit payment

#### 3.2.2 Proof Verification
**Process**:
1. Verify against internal records
2. If valid: Approve
3. If invalid: Reject with reason

**Approve**:
- Status: `awaiting_accounts_verification` → `payment_verified`
- Application automatically routed to Production Dashboard

**Reject**:
- Status: `awaiting_accounts_verification` → `payment_rejected`
- Reason is mandatory
- Applicant can resubmit payment

#### 3.2.3 Waiver Verification (Special Cases)
**For applications with status** `pending_accounts_review_from_registrar`:

**Process**:
1. Verify waiver documentation
2. If valid: Approve
3. If invalid: Reject with reason

**Approve**:
- Status: `pending_accounts_review_from_registrar` → `payment_verified`
- Application routed to Production

**Reject**:
- Status: `pending_accounts_review_from_registrar` → `payment_rejected`
- Send back to Registrar with reason

### 3.3 Accounts Rules
**Restrictions**:
- Cannot edit core application data
- Cannot modify applicant information
- Cannot change category codes
- Can only verify/reject payments
- All actions must be logged

## SECTION 4: MEDIA HOUSE REGISTRATION (TWO-STAGE PAYMENT FLOW)

### Flow Diagram
```
Applicant → (Application Fee) → Accreditation Officer → Registrar → (Registration Fee) → Accounts → Production
```

### 4.1 STAGE 1: Submission + Application Fee

#### 4.1.1 Application Submission
**Action**: Media House submits registration application

**Immediate Requirement**: Application Fee Payment

**Options**:
- Option A: PayNow Application Fee
- Option B: Upload Application Fee Proof

**Status Transition**: 
- `draft` → `submitted_with_application_fee`

**Rules**:
- Application CANNOT reach Accreditation Officer without Application Fee submission
- Payment submission is mandatory before proceeding
- Server-side validation enforces this rule

#### 4.1.2 Application Fee Payment
**Option A - PayNow**:
1. Pay via PayNow
2. Enter reference
3. Submit

**Option B - Upload Proof**:
1. Upload proof document
2. Submit

**Status After Payment Submission**: 
- `submitted_with_application_fee` → `awaiting_application_fee_verification`

**Accounts Verification**:
- Accounts verifies application fee
- If approved: `awaiting_application_fee_verification` → `application_fee_verified_pending_officer_review`
- If rejected: `awaiting_application_fee_verification` → `application_fee_rejected`

**After Application Fee Verified**:
- Application becomes visible to Accreditation Officer
- Status: `application_fee_verified_pending_officer_review`

### 4.2 STAGE 2: Accreditation Officer Review

**Actions**:
1. Verify all documents and requirements
2. Review company information
3. Review directors and managers
4. If valid: Push to Registrar

**Status Transition**: 
- `application_fee_verified_pending_officer_review` → `officer_approved_pending_registrar`

**Rules**:
- Cannot skip to Registrar without officer approval
- All required documents must be present
- Category code must be assigned

### 4.3 STAGE 3: Registrar Review

**Actions**:
1. Review full application
2. Review all documents
3. If satisfied:
   - Must upload Official Letter (mandatory)
   - Click Approve

**Status Transition**: 
- `officer_approved_pending_registrar` → `registrar_approved_pending_registration_fee`

**Rules**:
- Official Letter upload is MANDATORY
- Cannot approve without official letter
- Server-side validation enforces this
- Applicant is immediately prompted to pay Registration Fee

### 4.4 STAGE 4: Registration Fee Payment

**Prompt**: Applicant receives notification to pay Registration Fee

**Options**:
- PayNow OR Upload Proof

**Status Transition**: 
- `registrar_approved_pending_registration_fee` → `awaiting_registration_fee_verification`

**Accounts Verification**:
- Accounts verifies registration fee
- If approved: `awaiting_registration_fee_verification` → `payment_verified`
- If rejected: `awaiting_registration_fee_verification` → `registration_fee_rejected`

**After Registration Fee Verified**:
- Application routed to Production
- Status: `payment_verified`

## SECTION 5: PRODUCTION

### 5.1 Production Access
**Handled By**: Accreditation Officer ONLY

**Rules**:
- Only `payment_verified` applications appear
- Sidebar menu includes "Production Dashboard" link
- Visible only to Accreditation Officer role

### 5.2 Production Actions

#### 5.2.1 Generate Accreditation/Registration
**Process**:
1. Generate accreditation number OR registration number
2. Generate QR code
3. Prepare card/certificate for printing

**Status Transition**: 
- `payment_verified` → `in_production`

#### 5.2.2 Print Card/Certificate
**Process**:
1. Print card/certificate
2. Log print action

**Logged Information**:
- Print count
- Who printed (user_id)
- Timestamp
- Printer name (if available)
- Template version used

**Status Transition**: 
- `in_production` → `produced_ready_for_collection`

### 5.3 Production Rules
**Restrictions**:
- Cannot produce without `payment_verified` status
- Cannot skip production step
- All print actions must be logged
- Cannot delete print logs (immutable)

## SECTION 6: RBAC ENFORCEMENT

### 6.1 Applicant Permissions
**Can**:
- Submit application
- Upload payment proof
- Enter PayNow reference
- View own application status
- Resubmit after rejection/return

**Cannot**:
- View other applications
- Modify application after submission (except resubmission)
- Access staff dashboards
- Verify payments

### 6.2 Accreditation Officer Permissions
**Can**:
- View submitted applications
- Review application details
- Return application to applicant (with reason)
- Approve application
- Forward without approval (special cases)
- Assign category codes
- Access Production Dashboard
- Generate accreditation/registration numbers
- Print cards/certificates
- View production logs

**Cannot**:
- Verify payments
- Modify payment records
- Skip workflow steps
- Approve without required fields

### 6.3 Registrar Permissions
**Can**:
- View approved applications
- Review documents
- Raise fix requests to Accreditation Officer
- Mark review complete
- Approve media house applications (with official letter)
- Push special cases to Accounts
- View payment records (READ-ONLY)
- View audit logs (READ-ONLY)

**Cannot**:
- Verify payments
- Modify payment records
- Skip Accreditation Officer review
- Approve without official letter (media house)

### 6.4 Accounts Officer Permissions
**Can**:
- View applications with payment submissions
- Verify PayNow references
- Verify payment proofs
- Approve payments
- Reject payments (with reason)
- View payment history
- Record cash payments

**Cannot**:
- Edit core application data
- Modify applicant information
- Change category codes
- Skip verification step
- Approve without verification

### 6.5 Auditor Permissions
**Can**:
- View all applications (READ-ONLY)
- View all payment records (READ-ONLY)
- View all audit logs (READ-ONLY)
- Generate reports

**Cannot**:
- Modify any data
- Approve/reject applications
- Verify payments
- Access production

## SECTION 7: STATUS MACHINE (MANDATORY)

### 7.1 Status Definitions

#### Accreditation Flow Statuses
1. `draft` - Initial state
2. `submitted_to_accreditation_officer` - Submitted by applicant
3. `returned_to_applicant` - Returned by officer for corrections
4. `approved_by_officer_awaiting_payment` - Approved, waiting for payment
5. `forwarded_to_registrar_no_approval` - Special case forwarded
6. `registrar_raised_fix_request` - Registrar found issues
7. `awaiting_accounts_verification` - Payment submitted, awaiting verification
8. `pending_accounts_review_from_registrar` - Special case from Registrar
9. `payment_verified` - Payment approved by Accounts
10. `payment_rejected` - Payment rejected by Accounts
11. `in_production` - Being produced
12. `produced_ready_for_collection` - Ready for collection

#### Media House Registration Statuses
1. `draft` - Initial state
2. `submitted_with_application_fee` - Submitted with app fee
3. `awaiting_application_fee_verification` - App fee awaiting verification
4. `application_fee_rejected` - App fee rejected
5. `application_fee_verified_pending_officer_review` - App fee verified
6. `officer_approved_pending_registrar` - Officer approved
7. `registrar_approved_pending_registration_fee` - Registrar approved, awaiting reg fee
8. `awaiting_registration_fee_verification` - Reg fee awaiting verification
9. `registration_fee_rejected` - Reg fee rejected
10. `payment_verified` - All payments verified
11. `in_production` - Being produced
12. `produced_ready_for_collection` - Ready for collection

### 7.2 Status Transition Rules

**Rule 1**: No status can be skipped
**Rule 2**: All transitions must validate previous state
**Rule 3**: Invalid transitions must be rejected with error
**Rule 4**: All transitions must be logged

### 7.3 Valid Transitions

#### From `draft`:
- → `submitted_to_accreditation_officer` (applicant submits)
- → `submitted_with_application_fee` (media house submits with fee)

#### From `submitted_to_accreditation_officer`:
- → `returned_to_applicant` (officer returns)
- → `approved_by_officer_awaiting_payment` (officer approves)
- → `forwarded_to_registrar_no_approval` (officer forwards special case)

#### From `returned_to_applicant`:
- → `submitted_to_accreditation_officer` (applicant resubmits)

#### From `approved_by_officer_awaiting_payment`:
- → `awaiting_accounts_verification` (applicant submits payment)
- → `registrar_raised_fix_request` (registrar finds issues)

#### From `forwarded_to_registrar_no_approval`:
- → `pending_accounts_review_from_registrar` (registrar approves)

#### From `registrar_raised_fix_request`:
- → `approved_by_officer_awaiting_payment` (officer fixes and resubmits)

#### From `awaiting_accounts_verification`:
- → `payment_verified` (accounts approves)
- → `payment_rejected` (accounts rejects)

#### From `pending_accounts_review_from_registrar`:
- → `payment_verified` (accounts approves waiver)
- → `payment_rejected` (accounts rejects)

#### From `payment_rejected`:
- → `awaiting_accounts_verification` (applicant resubmits payment)

#### From `payment_verified`:
- → `in_production` (production starts)

#### From `in_production`:
- → `produced_ready_for_collection` (production completes)

#### Media House Specific Transitions

#### From `submitted_with_application_fee`:
- → `awaiting_application_fee_verification` (fee submitted)

#### From `awaiting_application_fee_verification`:
- → `application_fee_verified_pending_officer_review` (accounts approves)
- → `application_fee_rejected` (accounts rejects)

#### From `application_fee_rejected`:
- → `awaiting_application_fee_verification` (applicant resubmits)

#### From `application_fee_verified_pending_officer_review`:
- → `officer_approved_pending_registrar` (officer approves)

#### From `officer_approved_pending_registrar`:
- → `registrar_approved_pending_registration_fee` (registrar approves with letter)

#### From `registrar_approved_pending_registration_fee`:
- → `awaiting_registration_fee_verification` (applicant submits reg fee)

#### From `awaiting_registration_fee_verification`:
- → `payment_verified` (accounts approves)
- → `registration_fee_rejected` (accounts rejects)

#### From `registration_fee_rejected`:
- → `awaiting_registration_fee_verification` (applicant resubmits)

## SECTION 8: AUDIT LOGGING (MANDATORY)

### 8.1 Events to Log

**All of the following must be logged**:
1. Application submission
2. Application return to applicant
3. Application approval by officer
4. Application forward without approval
5. Fix request raised by registrar
6. Payment submission (PayNow or proof)
7. Payment verification (approve/reject)
8. Production generation
9. Print action
10. Status transitions
11. Document uploads
12. Official letter uploads

### 8.2 Audit Log Structure

```php
activity_logs table:
- id
- application_id
- actor_role (applicant, accreditation_officer, registrar, accounts, auditor)
- actor_user_id
- action (submitted, approved, returned, payment_submitted, payment_verified, etc.)
- before_status
- after_status
- reason_notes (for returns, rejections, etc.)
- metadata (JSON - additional context)
- ip_address
- user_agent
- timestamp
- created_at
```

### 8.3 Audit Log Requirements

**Immutability**:
- Audit logs cannot be deleted
- Audit logs cannot be modified
- Only INSERT operations allowed

**Retention**:
- Logs retained indefinitely
- Archival strategy for old logs

**Access**:
- Auditor has full read access
- Staff can view logs for their applications
- Applicants can view logs for their own applications

## SECTION 9: SERVER-SIDE ENFORCEMENT

### 9.1 Validation Guards

**All endpoints must validate**:
1. User authentication
2. User authorization (RBAC)
3. Current application status
4. Valid status transition
5. Required fields present
6. Required documents uploaded
7. Business rules satisfied

### 9.2 Middleware Stack

```php
Route::middleware([
    'auth',
    'role:accreditation_officer',
    'validate.status.transition',
    'audit.log'
])->group(function () {
    // Protected routes
});
```

### 9.3 Service Layer Validation

**All service methods must**:
1. Validate current state
2. Validate transition rules
3. Validate business rules
4. Execute transaction atomically
5. Log action
6. Return result or throw exception

### 9.4 Database Constraints

**Enforce at database level**:
1. Foreign key constraints
2. Unique constraints (PayNow references, receipt numbers)
3. Check constraints (valid status values)
4. Not null constraints (required fields)

## SECTION 10: CONFIRMATION CHECKLIST

### 10.1 Workflow Enforcement
- ✅ No step can skip Accreditation Officer (except renewal flows)
- ✅ No payment verification happens before submission
- ✅ No production happens before payment_verified status
- ✅ Registrar has oversight but no payment authority
- ✅ Media house uses 2-stage payment enforcement
- ✅ All status transitions validated server-side
- ✅ All actions generate audit logs
- ✅ RBAC enforced at API level
- ✅ Business rules enforced at service level
- ✅ Database constraints enforce data integrity

### 10.2 Security Enforcement
- ✅ Authentication required for all endpoints
- ✅ Authorization checked before action
- ✅ Input validation on all user data
- ✅ SQL injection prevention (parameterized queries)
- ✅ XSS prevention (output escaping)
- ✅ CSRF protection on forms
- ✅ File upload validation (type, size, content)
- ✅ Rate limiting on sensitive endpoints

### 10.3 Data Integrity
- ✅ Atomic transactions for multi-step operations
- ✅ Rollback on failure
- ✅ Audit logs immutable
- ✅ Status transitions validated
- ✅ Required fields enforced
- ✅ Foreign key relationships maintained
- ✅ Unique constraints enforced

## Implementation Priority

1. **Phase 1**: Status machine and transition validation
2. **Phase 2**: RBAC enforcement and middleware
3. **Phase 3**: Service layer refactoring
4. **Phase 4**: Audit logging implementation
5. **Phase 5**: UI updates to match workflow
6. **Phase 6**: Testing and validation
7. **Phase 7**: Documentation and training
