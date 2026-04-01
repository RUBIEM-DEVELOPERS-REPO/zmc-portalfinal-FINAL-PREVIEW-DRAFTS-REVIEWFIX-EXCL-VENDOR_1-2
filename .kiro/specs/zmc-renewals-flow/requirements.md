# ZMC Renewals Flow - Requirements Specification

## Document Information
**Version**: 1.0  
**Date**: 2026-02-25  
**Project**: ZMC Renewals Flow - Number-Only Lookup + Change Confirm + Payment → Production  
**Status**: Requirements Analysis

---

## Executive Summary

This specification defines the renewal workflow for the Zimbabwe Media Commission system. Renewals follow a simplified flow that bypasses Officer and Registrar review stages, going directly from Applicant confirmation to Accounts verification to Production.

**Key Principle**: Renewals are for existing, previously approved entities. The focus is on payment verification and document production, not re-evaluation of credentials.

---

## Renewal Flow Overview

```
Applicant (Number Lookup + Confirm) 
  ↓
Accounts/Payments (Verify Payment)
  ↓
Production (Accreditation Officer - Generate Renewed Documents)
```

**Critical**: NO Officer review, NO Registrar review for renewals.

---

## Renewal Types Supported

1. **Journalist / Media Practitioner Accreditation Renewal**
   - Renews existing accreditation
   - Uses accreditation number for lookup
   - Produces renewed accreditation card

2. **Media House Registration Renewal**
   - Renews existing registration
   - Uses registration number for lookup
   - Produces renewed certificate/license

3. **Permission Renewal** (Optional)
   - If system supports permissions
   - Uses permission number for lookup

---

## A) Renewal Application UI/Form Rules

### Step 1: Select Renewal Type

**UI Component**: Radio buttons or dropdown

**Options**:
- Journalist / Media Practitioner Accreditation Renewal
- Media House Registration Renewal
- Permission Renewal (if supported)

**Validation**:
- Required field
- Must be one of the supported types

**Storage**: `renewal_type` field

---

### Step 2: Number-Only Entry (CRITICAL RULE)

**ONLY Input Field**:
- For Journalist: "Accreditation Number"
- For Media House: "Registration Number"
- For Permission: "Permission Number"

**NO OTHER FIELDS** on Step 2. This is a hard requirement.

**UI Elements**:
- Single text input for number
- "Search" or "Continue" button
- Clear label indicating what number to enter

**Example UI**:
```
┌─────────────────────────────────────────┐
│ Enter Your Accreditation Number        │
├─────────────────────────────────────────┤
│ Accreditation Number: [____________]    │
│                                         │
│ [Search]                                │
└─────────────────────────────────────────┘
```

---

### Step 3: Lookup Behavior (Mandatory)

**On Submit**:
1. Query database for existing record by number
2. Retrieve ALL associated data:
   - Applicant/entity details
   - Category
   - Previous approvals
   - Issue date
   - Expiry date
   - Status history
   - Associated documents metadata
   - Current status

**If Found**:
- Display complete record to applicant
- Show review screen
- Proceed to Step 4

**If NOT Found**:
- Block progression
- Display error: "Number not found. Please verify your number and try again."
- Log attempt in audit trail (without exposing sensitive data)
- Allow retry

**Security**:
- Rate limit lookup attempts (max 5 per hour per user)
- Log all attempts
- No sensitive data in error messages

---

### Step 4: Change Confirmation (MANDATORY CHOICE)

**Applicant Must Choose ONE**:

**Option A: "No Changes"**
- Applicant confirms all details remain the same
- System records confirmation
- Proceed to payment

**Option B: "There Are Changes"**
- Applicant must specify changes
- For each change:
  - Field name (dropdown of changeable fields)
  - Old value (system-filled, read-only)
  - New value (applicant input)
  - Supporting document upload (optional, required for certain fields)
- Applicant confirms submission
- Proceed to payment

**Hard Rule**: Cannot proceed without explicit confirmation.

**Changeable Fields** (examples):
- Contact information (email, phone, address)
- Employment details (for journalists)
- Organization details (for media houses)
- NOT changeable: Name, ID number, core credentials

**UI Example**:
```
┌─────────────────────────────────────────────────────┐
│ Review Your Information                             │
├─────────────────────────────────────────────────────┤
│ Name: John Doe                                      │
│ ID Number: 12-345678-A-12                          │
│ Category: JE - Local journalist employed full-time │
│ Expiry Date: 2026-12-31                            │
│                                                     │
│ Are there any changes to your information?         │
│                                                     │
│ ( ) No changes - All information is correct        │
│ ( ) Yes, there are changes                         │
│                                                     │
│ [Continue]                                          │
└─────────────────────────────────────────────────────┘
```

---

## B) Payment Prompt (After Confirmation)

**Timing**: Immediately after applicant confirms (No changes OR Changes submitted)

**Payment Methods**:

### Method 1: PayNow

**Flow**:
1. Click "Pay with PayNow"
2. Redirect to PayNow interface
3. Complete payment
4. Click "Done"
5. Modal appears: "Enter PayNow Reference Number"
6. Applicant enters reference (mandatory)
7. Submit

**Storage**:
```php
renewal_payment.method = 'PAYNOW'
renewal_payment.reference_number = [entered reference]
renewal_payment.submitted_at = now()
```

### Method 2: Upload Proof of Payment

**Flow**:
1. Click "Upload Proof of Payment"
2. Modal appears with form:
   - Payment date (required)
   - Amount paid (required)
   - Payer name (optional)
   - Receipt/reference number (optional)
   - Upload proof document(s) (required)
3. Submit

**Storage**:
```php
renewal_payment.method = 'PROOF_UPLOAD'
renewal_payment.proof_files = [file paths]
renewal_payment.payment_date = [date]
renewal_payment.amount = [amount]
renewal_payment.submitted_at = now()
```

**After Payment Submission**:
- Status: `RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION`
- Route to Accounts/Payments dashboard queue
- Send confirmation to applicant

---

## C) Accounts/Payments Officer Verification

**Queue**: Only renewals with status `RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION`

**Verification Process**:

### For PayNow:
1. Compare applicant-submitted reference against PayNow platform record
2. Verify amount matches renewal fee
3. Verify payment date is recent
4. Verify payment status is successful

### For Proof Upload:
1. Review uploaded proof document
2. Validate against internal bank records
3. Verify amount matches renewal fee
4. Verify payment date
5. Check for authenticity

### For Waiver (Optional):
1. Verify waiver validity
2. Check authorization
3. Validate waiver rules

**Outcomes**:

**If Valid**:
- Status: `RENEWAL_PAYMENT_VERIFIED`
- Record verification timestamp
- Record verifier identity
- Add verification notes
- Route to Production queue

**If Invalid**:
- Status: `RENEWAL_PAYMENT_REJECTED`
- Mandatory rejection reason
- Notify applicant
- Applicant must resubmit payment

**Constraints**:
- Accounts Officer CANNOT edit core entity profile data
- Can only verify payments
- Must record all decisions
- All actions require audit logs

---

## D) Production (Accreditation Officer) - Final Stage

**Queue**: Only renewals with status `RENEWAL_PAYMENT_VERIFIED`

**Operator**: Accreditation Officer

**Process**:

### For Journalist Renewal:
1. Generate renewed accreditation card
2. Maintain same accreditation number
3. Update expiry date (typically +1 year)
4. Embed QR code with renewed status
5. Print tracking

### For Media House Renewal:
1. Generate renewed certificate/license
2. Maintain same registration number
3. Update expiry date
4. Embed QR code with renewed status
5. Print tracking

**Number Format Logic**:
- Number stays linked to same entity
- No new number generation for renewals
- Format: [PREFIX][ORIGINAL_NUMBER][SUFFIX]

**Print Tracking** (Mandatory):
- Who generated
- Who printed
- Number of prints
- Timestamps
- Office/collection location

**Status After Production**: `RENEWAL_PRODUCED_READY_FOR_COLLECTION`

**UI Requirement**: Accreditation Officer sidebar must include "Production" link

---

## E) Status Machine

### Required Statuses

```php
// Draft/Initial
RENEWAL_DRAFT = 'renewal_draft'
RENEWAL_TYPE_SELECTED = 'renewal_type_selected'

// Lookup
RENEWAL_NUMBER_ENTERED = 'renewal_number_entered'
RENEWAL_RECORD_FOUND = 'renewal_record_found'
RENEWAL_RECORD_NOT_FOUND = 'renewal_record_not_found'

// Confirmation
RENEWAL_CONFIRMED_NO_CHANGES = 'renewal_confirmed_no_changes'
RENEWAL_CONFIRMED_WITH_CHANGES = 'renewal_confirmed_with_changes'

// Payment
RENEWAL_PAYMENT_INITIATED = 'renewal_payment_initiated'
RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION = 'renewal_submitted_awaiting_accounts_verification'
RENEWAL_PAYMENT_VERIFIED = 'renewal_payment_verified'
RENEWAL_PAYMENT_REJECTED = 'renewal_payment_rejected'

// Production
RENEWAL_IN_PRODUCTION = 'renewal_in_production'
RENEWAL_PRODUCED_READY_FOR_COLLECTION = 'renewal_produced_ready_for_collection'

// Terminal
RENEWAL_COLLECTED = 'renewal_collected'
RENEWAL_CANCELLED = 'renewal_cancelled'
```

### Status Transitions

```
RENEWAL_DRAFT
  → RENEWAL_TYPE_SELECTED (applicant selects type)

RENEWAL_TYPE_SELECTED
  → RENEWAL_NUMBER_ENTERED (applicant enters number)

RENEWAL_NUMBER_ENTERED
  → RENEWAL_RECORD_FOUND (system finds record)
  → RENEWAL_RECORD_NOT_FOUND (system doesn't find record)

RENEWAL_RECORD_NOT_FOUND
  → RENEWAL_NUMBER_ENTERED (retry)

RENEWAL_RECORD_FOUND
  → RENEWAL_CONFIRMED_NO_CHANGES (applicant confirms no changes)
  → RENEWAL_CONFIRMED_WITH_CHANGES (applicant submits changes)

RENEWAL_CONFIRMED_NO_CHANGES | RENEWAL_CONFIRMED_WITH_CHANGES
  → RENEWAL_PAYMENT_INITIATED (applicant starts payment)

RENEWAL_PAYMENT_INITIATED
  → RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION (payment submitted)

RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION
  → RENEWAL_PAYMENT_VERIFIED (accounts approves)
  → RENEWAL_PAYMENT_REJECTED (accounts rejects)

RENEWAL_PAYMENT_REJECTED
  → RENEWAL_PAYMENT_INITIATED (resubmit payment)

RENEWAL_PAYMENT_VERIFIED
  → RENEWAL_IN_PRODUCTION (production starts)

RENEWAL_IN_PRODUCTION
  → RENEWAL_PRODUCED_READY_FOR_COLLECTION (production complete)

RENEWAL_PRODUCED_READY_FOR_COLLECTION
  → RENEWAL_COLLECTED (applicant collects)
```

**Server-Side Enforcement**: All transitions validated, no skipping allowed.

---

## F) Audit Logging (Mandatory)

### Events to Log

1. **renewal_type_selection**
   - renewal_type
   - timestamp

2. **number_lookup_attempt**
   - renewal_type
   - lookup_result (found/not_found)
   - timestamp
   - NO sensitive data in logs

3. **no_changes_confirmation**
   - timestamp

4. **changes_submission**
   - change_list (structured)
   - supporting_documents
   - timestamp

5. **payment_method_selection**
   - method (PAYNOW/PROOF_UPLOAD)
   - timestamp

6. **payment_submission**
   - method
   - reference/proof_metadata
   - timestamp

7. **accounts_verification**
   - outcome (verified/rejected)
   - reason (if rejected)
   - verifier_id
   - timestamp

8. **production_generation**
   - document_type
   - generator_id
   - timestamp

9. **print_log**
   - printer_id
   - print_count
   - timestamp

### Log Structure

```php
[
    'application_id' => $renewalId,
    'actor_user_id' => $userId,
    'actor_role' => $role,
    'action_type' => $action,
    'before_status' => $oldStatus,
    'after_status' => $newStatus,
    'timestamp' => now(),
    'notes' => $notes,
    'metadata' => [
        'renewal_type' => '...',
        'original_number' => '...',
        'changes' => [...],
        'payment_method' => '...',
        'reference' => '...',
    ],
    'ip_address' => $ip,
    'user_agent' => $userAgent,
]
```

---

## G) Data Model

### RenewalApplication Table

```php
id
applicant_user_id
renewal_type // 'accreditation' | 'registration' | 'permission'
original_application_id // FK to applications table
original_number // The accreditation/registration number
lookup_status // 'found' | 'not_found'
has_changes // boolean
change_requests // JSON
confirmation_type // 'no_changes' | 'with_changes'
confirmed_at

// Payment
payment_method // 'PAYNOW' | 'PROOF_UPLOAD' | 'WAIVER'
payment_reference
payment_amount
payment_date
payment_proof_path
payment_metadata // JSON
payment_submitted_at
payment_verified_at
payment_verified_by
payment_rejection_reason

// Status
status
current_stage
last_action_at
last_action_by

// Production
produced_at
produced_by
print_count
collection_location

// Timestamps
created_at
updated_at
```

### RenewalChangeRequest Table

```php
id
renewal_application_id
field_name
old_value
new_value
supporting_document_path
status // 'pending' | 'approved' | 'rejected'
reviewed_by
reviewed_at
review_notes
created_at
updated_at
```

### RenewalPayment Table (Alternative: reuse PaymentSubmission)

```php
id
renewal_application_id
payment_stage // 'renewal_fee'
method // 'PAYNOW' | 'PROOF_UPLOAD' | 'WAIVER'
reference
amount
currency
status // 'submitted' | 'verified' | 'rejected'
submitted_at
verified_at
verified_by
rejection_reason
proof_path
proof_metadata // JSON
created_at
updated_at
```

---

## H) RBAC Matrix

| Role | Permissions |
|------|-------------|
| **Applicant** | - Select renewal type<br>- Enter number<br>- View retrieved record<br>- Confirm no changes OR submit changes<br>- Submit payment<br>- View renewal status<br>- Resubmit payment if rejected |
| **Accounts/Payments** | - View renewal payment queue<br>- Verify payment submissions<br>- Approve/reject payments<br>- Add verification notes<br>- **CANNOT**: Edit applicant data<br>- **CANNOT**: Access production |
| **Accreditation Officer** | - View production queue<br>- Generate renewed documents<br>- Print documents<br>- Track prints<br>- Mark as ready for collection<br>- **CANNOT**: Verify payments<br>- **CANNOT**: Edit applicant data |
| **Registrar** | - NO role in renewal flow<br>- Can view for oversight only |

---

## I) API Endpoints

### Public/Applicant Endpoints

```php
POST /portal/renewals/select-type
  - Body: { renewal_type }
  - Returns: { success, renewal_id }

POST /portal/renewals/{renewal}/lookup
  - Body: { number }
  - Returns: { success, record_found, record_data }

POST /portal/renewals/{renewal}/confirm-no-changes
  - Returns: { success, next_step: 'payment' }

POST /portal/renewals/{renewal}/submit-changes
  - Body: { changes: [{field, old_value, new_value, document}] }
  - Returns: { success, next_step: 'payment' }

POST /portal/renewals/{renewal}/payment/paynow
  - Body: { reference }
  - Returns: { success, status }

POST /portal/renewals/{renewal}/payment/proof
  - Body: { date, amount, payer_name, reference, proof_file }
  - Returns: { success, status }
```

### Staff Endpoints

```php
GET /staff/accounts/renewals/queue
  - Returns: { renewals: [...] }

POST /staff/accounts/renewals/{renewal}/verify-payment
  - Body: { action: 'verify'|'reject', notes }
  - Returns: { success, new_status }

GET /staff/officer/production/renewals/queue
  - Returns: { renewals: [...] }

POST /staff/officer/production/renewals/{renewal}/generate
  - Returns: { success, document_url }

POST /staff/officer/production/renewals/{renewal}/print
  - Returns: { success, print_log_id }
```

---

## J) UI Requirements

### Applicant Portal

**Step 1: Select Type**
```
┌─────────────────────────────────────────┐
│ Renewal Application                     │
├─────────────────────────────────────────┤
│ Select Renewal Type:                    │
│                                         │
│ ( ) Journalist/Media Practitioner       │
│     Accreditation Renewal               │
│                                         │
│ ( ) Media House Registration Renewal    │
│                                         │
│ [Continue]                              │
└─────────────────────────────────────────┘
```

**Step 2: Enter Number**
```
┌─────────────────────────────────────────┐
│ Enter Your Accreditation Number         │
├─────────────────────────────────────────┤
│ Accreditation Number:                   │
│ [____________________________]          │
│                                         │
│ Example: J12345678E                     │
│                                         │
│ [Search]                                │
└─────────────────────────────────────────┘
```

**Step 3: Review & Confirm**
```
┌─────────────────────────────────────────┐
│ Review Your Information                 │
├─────────────────────────────────────────┤
│ [Full record display]                   │
│                                         │
│ Are there any changes?                  │
│ ( ) No changes                          │
│ ( ) Yes, there are changes              │
│                                         │
│ [Continue]                              │
└─────────────────────────────────────────┘
```

**Step 4: Payment**
```
┌─────────────────────────────────────────┐
│ Payment Required                        │
├─────────────────────────────────────────┤
│ Renewal Fee: $XX.XX                     │
│                                         │
│ [Pay with PayNow]                       │
│ [Upload Proof of Payment]               │
└─────────────────────────────────────────┘
```

### Accounts Dashboard

- Queue of renewals awaiting verification
- Filter by payment method
- Verify/Reject buttons
- Payment details display
- Verification notes input

### Production Dashboard (Officer)

- Queue of verified renewals
- Generate document button
- Print tracking
- Collection location selection
- Status updates

---

## K) Validation Rules

### Number Lookup
- Must be valid format
- Must exist in database
- Must not be expired beyond grace period
- Must not be cancelled/revoked

### Change Requests
- Only allowed fields can be changed
- Supporting documents required for certain changes
- Cannot change core identity fields

### Payment
- Amount must match renewal fee
- Reference must be unique
- Proof must be valid file type
- Date must be recent

### Production
- Can only generate for verified payments
- Must track all prints
- Cannot generate duplicate without reason

---

## Success Criteria

### Functional Requirements
- [ ] Applicant can select renewal type
- [ ] Number-only lookup works correctly
- [ ] Record retrieval displays all data
- [ ] Change confirmation is mandatory
- [ ] Payment methods work (PayNow + Proof)
- [ ] Accounts can verify payments
- [ ] Production generates renewed documents
- [ ] Print tracking works
- [ ] Status transitions enforced
- [ ] Audit logs complete

### Non-Functional Requirements
- [ ] No breaking changes to existing flows
- [ ] Performance: Lookup < 1 second
- [ ] Security: RBAC enforced
- [ ] Usability: Intuitive UI
- [ ] Reliability: 99.9% uptime
- [ ] Maintainability: Well-documented

---

**Document Version**: 1.0  
**Status**: Ready for Implementation  
**Next Step**: Create technical design document
