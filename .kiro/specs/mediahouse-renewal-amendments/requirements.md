# Media House Portal Renewal Flow Amendments

**Project**: Zimbabwe Media Commission - Media House Renewal Flow Updates  
**Date**: February 25, 2026  
**Status**: 🔄 IN PROGRESS

---

## Executive Summary

Implement amendments to the Media House Portal renewal flow to:
1. Remove the "Contact Details" step completely
2. Implement number-only lookup for Registration Info step
3. Enforce mandatory confirmation (No Changes OR Changes declaration)
4. Implement dual payment submission (PayNow + Proof Upload)
5. Add comprehensive status machine and audit logging

---

## A) REMOVE CONTACT DETAILS STEP

### Requirements
- Remove step 2 ("Contact Details") from the wizard completely
- Do not collect or update contact details within this renewal flow
- Update step numbering: 1→Type, 2→Registration Info, 3→Documents, 4→Declaration

### Implementation
- Update wizard step progress bar (remove step 2)
- Remove all contact details form fields and validation
- Update step navigation logic

---

## B) REGISTRATION INFO STEP — NUMBER-ONLY LOOKUP

### Requirements

#### Input
- Single field: Registration Number (text input)
- No other editable fields on this step

#### Lookup Process
1. User enters registration number
2. System queries database for media house record
3. Retrieve minimum data:
   - Media house name
   - Registration number
   - Category
   - Registration issue date
   - Expiry date / validity info
   - Current license status
   - Other relevant stored details

#### Lookup Results

**If NOT FOUND:**
- Block progression
- Show error: "Registration number not found."
- Log lookup attempt (without sensitive details)
- Allow retry

**If FOUND:**
- Display retrieved information (read-only)
- Show two options:
  - A) "No changes" → Confirm record is correct
  - B) "There are changes" → Indicate changes needed

#### Change Capture (if "There are changes" selected)
- Show structured change input section
- Fields:
  - Field name (what changed)
  - Old value (from system)
  - New value (user input)
  - Supporting document upload (optional)
- Allow multiple changes
- Must confirm before proceeding

#### Hard Rules
- Cannot proceed without confirmation
- Must select either "No changes" OR submit "Changes"
- Server-side validation enforced

---

## C) PAYMENT STEP (AFTER CONFIRMATION)

### Requirements

After confirmation, applicant proceeds to payment with two options:

#### Option 1: PayNow
- Button: "Pay with PayNow"
- Flow:
  1. Open/redirect to PayNow gateway
  2. After payment, user clicks "Done"
  3. Show modal: "Enter PayNow Reference Number"
  4. User enters reference and submits
- Store:
  - `payment_method = 'PAYNOW'`
  - `payment_reference = <reference_number>`

#### Option 2: Upload Payment Proof
- Show modal to capture:
  - Amount (optional)
  - Date paid
  - Receipt/reference (optional)
  - Upload proof document(s)
- Store:
  - `payment_method = 'PROOF_UPLOAD'`
  - `payment_documents = <file_paths>`

#### After Payment Submission
- Status: `RenewalSubmitted_AwaitingAccountsVerification`
- Route to Accounts/Payments officer queue
- Existing verification processes remain unchanged

---

## D) STATUS + AUDIT REQUIREMENTS

### Status Machine (Minimum)

```
1. RenewalStarted
2. RegistrationNumberEntered
3. RecordNotFound (terminal until corrected)
4. RecordFound_Displayed
5. RenewalConfirmed_NoChanges OR RenewalConfirmed_WithChanges
6. PaymentSubmitted_PayNowReference OR PaymentSubmitted_ProofUpload
7. RenewalSubmitted_AwaitingAccountsVerification
```

### Audit Log Requirements

Must capture:
- User ID (who entered registration number)
- Lookup result (found/not found)
- Confirmation selection (no changes vs changes)
- Change declarations (structured list) + attachments metadata
- Payment method + submitted reference/proof metadata
- Timestamp for every action

### Enforcement
- All validation must be server-side (API guards)
- Not just UI validation
- RBAC controls enforced

---

## E) API ENDPOINTS REQUIRED

### 1. Lookup Media House by Registration Number
```
POST /api/mediahouse/renewals/lookup
Request: { registration_number: string }
Response: {
  found: boolean,
  data: {
    name, registration_number, category, issue_date, expiry_date, status, ...
  } | null,
  error: string | null
}
```

### 2. Submit Renewal - No Changes
```
POST /api/mediahouse/renewals/{renewal}/confirm-no-changes
Request: {}
Response: { success: boolean, message: string }
```

### 3. Submit Renewal - With Changes
```
POST /api/mediahouse/renewals/{renewal}/submit-changes
Request: {
  changes: [
    { field_name, old_value, new_value, supporting_document? }
  ]
}
Response: { success: boolean, message: string }
```

### 4. Submit PayNow Reference
```
POST /api/mediahouse/renewals/{renewal}/payment/paynow
Request: { reference: string }
Response: { success: boolean, message: string }
```

### 5. Submit Payment Proof
```
POST /api/mediahouse/renewals/{renewal}/payment/proof
Request: {
  amount?, date_paid, reference?, proof_file
}
Response: { success: boolean, message: string }
```

---

## F) DATABASE SCHEMA UPDATES

### Renewal Applications Table
- Already exists from previous implementation
- Ensure fields support:
  - `renewal_type` (renewal/replacement)
  - `original_number` (registration number)
  - `original_application_id` (FK to applications)
  - `lookup_status` (pending/found/not_found)
  - `has_changes` (boolean)
  - `confirmation_type` (no_changes/with_changes)
  - `payment_method` (PAYNOW/PROOF_UPLOAD)
  - `payment_reference`
  - `payment_proof_path`
  - `status` (status machine values)

### Renewal Change Requests Table
- Already exists
- Fields:
  - `renewal_application_id` (FK)
  - `field_name`
  - `old_value`
  - `new_value`
  - `supporting_document_path`
  - `status` (pending/approved/rejected)

### Activity Logs
- Link to renewal_applications via polymorphic relationship
- Capture all required audit events

---

## G) EDGE CASES HANDLING

### 1. Duplicate Renewal Attempts
- Check if active renewal exists for registration number
- Block if pending renewal found
- Show message: "You already have a pending renewal for this registration"

### 2. Invalid/Expired Registration Numbers
- Validate format before lookup
- Check expiry status
- Allow renewal of expired registrations (with warning)

### 3. Payment Proof Without Confirmation
- Server-side check: block payment if not confirmed
- Return error: "Please confirm your information first"

### 4. PayNow Reference Without "Done" Flow
- Accept reference submission at any time
- Store with timestamp
- Mark as pending verification

### 5. Incomplete Change Declarations
- Validate all required fields
- Require at least one change if "There are changes" selected
- Validate supporting documents (file type, size)

---

## H) UNCHANGED PIPELINE

The following remain as previously implemented:
- Accounts verification process
- Production workflow
- Collection process
- Staff dashboards and queues
- Reporting and analytics

---

## Success Criteria

✅ Contact Details step removed  
✅ Registration Info step implements number-only lookup  
✅ Mandatory confirmation enforced (server-side)  
✅ Dual payment options implemented  
✅ Status machine enforced  
✅ Comprehensive audit logging  
✅ All edge cases handled  
✅ Server-side validation on all endpoints  
✅ RBAC controls enforced  

---

**Next Steps**: Implement backend controllers, update views, add routes, test thoroughly
