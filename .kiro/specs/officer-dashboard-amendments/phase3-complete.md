# Phase 3: Pending Review Page Update - COMPLETE

**Date**: February 25, 2026  
**Status**: ✅ COMPLETE

---

## COMPLETED CHANGES

### 1. Redefined Pending Review Page ✅

**Previous Definition**:
- Showed applications in Officer's queue (SUBMITTED, OFFICER_REVIEW, CORRECTION_REQUESTED, RETURNED_TO_OFFICER)
- Essentially showed applications the Officer needed to work on

**New Definition**:
- Shows applications that have been **processed by Officer**
- But **NOT yet attended to by Accounts/Payments Officer**
- Provides visibility into applications waiting for Accounts review

### 2. Updated Query Logic ✅

**File**: `app/Http/Controllers/Staff/AccreditationOfficerController.php`

**Method**: `applicationsBaseQuery()`

**New Status Filter**:
```php
elseif ($list === 'pending') {
    // REDEFINED: Applications processed by Officer but NOT yet attended to by Accounts
    $q->whereIn('status', [
        Application::ACCOUNTS_REVIEW,
        Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR,
        Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
        'renewal_submitted_awaiting_accounts_verification',
    ]);
}
```

### 3. Status Definitions ✅

#### ACCOUNTS_REVIEW
- Standard path: Applications approved by Registrar and sent to Accounts
- Waiting for payment verification

#### PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR
- Special/waiver path: Applications forwarded by Registrar without full approval
- Requires Accounts review for special cases

#### REG_FEE_SUBMITTED_AWAITING_VERIFICATION
- Two-stage payment path: Media house registration fee submitted
- Waiting for Accounts to verify payment

#### renewal_submitted_awaiting_accounts_verification
- Renewal path: Renewal application submitted with payment
- Waiting for Accounts to verify renewal payment

### 4. Updated Page Title ✅

**Previous**: "Pending Review"  
**New**: "Pending Accounts Review"

This makes it clear that these are applications waiting for Accounts, not Officer review.

### 5. Updated Sidebar Link ✅

**File**: `resources/views/layouts/sidebar_staff.blade.php`

**Previous**: "Pending Review"  
**New**: "Pending Accounts Review"

Consistent naming across the interface.

---

## FILES MODIFIED

1. **app/Http/Controllers/Staff/AccreditationOfficerController.php**
   - Updated `applicationsBaseQuery()` method
   - Changed 'pending' list scope to show Accounts-pending applications
   - Updated page title in `applicationsList()` method

2. **resources/views/layouts/sidebar_staff.blade.php**
   - Updated sidebar link text from "Pending Review" to "Pending Accounts Review"

---

## VERIFICATION CHECKLIST

- [x] Pending Review page now shows only Accounts-pending applications
- [x] Applications in ACCOUNTS_REVIEW status appear
- [x] Applications in PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR status appear
- [x] Applications in REG_FEE_SUBMITTED_AWAITING_VERIFICATION status appear
- [x] Renewal applications awaiting Accounts verification appear
- [x] Page title updated to "Pending Accounts Review"
- [x] Sidebar link updated to "Pending Accounts Review"
- [x] No PHP syntax errors
- [x] No diagnostics issues

---

## WORKFLOW PRESERVATION

✅ **NO workflow logic modified**
- Status transitions remain unchanged
- Approval logic intact
- Payment logic intact
- Production logic intact
- Renewal logic intact
- Audit logging preserved
- Pool visibility logic maintained
- Lock timeout logic maintained

---

## USE CASE

### Officer Perspective
The Officer can now use this page to:
1. **Monitor Progress**: See which applications they've processed that are now with Accounts
2. **Track Bottlenecks**: Identify if applications are stuck in Accounts review
3. **Follow Up**: Know which applications to follow up on with Accounts team
4. **Visibility**: Maintain awareness of the full application pipeline

### Previous Behavior
- "Pending Review" showed applications the Officer needed to work on
- This was redundant with the dashboard's "Incoming Applications" table

### New Behavior
- "Pending Accounts Review" shows applications that have left Officer's hands
- Provides post-processing visibility
- Helps Officer track application progress through the system

---

## STATUS FLOW CONTEXT

### Standard Flow
```
SUBMITTED
  → OFFICER_REVIEW (Officer works on it)
  → OFFICER_APPROVED
  → REGISTRAR_REVIEW (Registrar works on it)
  → REGISTRAR_APPROVED
  → ACCOUNTS_REVIEW ← Shows in "Pending Accounts Review"
  → PAYMENT_VERIFIED
  → PRODUCTION_QUEUE
```

### Special/Waiver Flow
```
SUBMITTED
  → OFFICER_REVIEW
  → FORWARDED_TO_REGISTRAR_NO_APPROVAL
  → REGISTRAR_REVIEW
  → PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR ← Shows in "Pending Accounts Review"
  → PAYMENT_VERIFIED
  → PRODUCTION_QUEUE
```

### Two-Stage Payment Flow (Media Houses)
```
SUBMITTED
  → OFFICER_REVIEW
  → OFFICER_APPROVED
  → REGISTRAR_REVIEW
  → REGISTRAR_APPROVED
  → REG_FEE_SUBMITTED_AWAITING_VERIFICATION ← Shows in "Pending Accounts Review"
  → PAYMENT_VERIFIED
  → PRODUCTION_QUEUE
```

### Renewal Flow
```
RENEWAL_SUBMITTED
  → RENEWAL_PAYMENT_SUBMITTED
  → renewal_submitted_awaiting_accounts_verification ← Shows in "Pending Accounts Review"
  → RENEWAL_PAYMENT_VERIFIED
  → RENEWAL_IN_PRODUCTION
```

---

## BENEFITS

### 1. Clear Separation of Concerns
- Officer's dashboard shows what they need to work on
- Pending Accounts Review shows what's waiting for Accounts
- No confusion about whose responsibility each application is

### 2. Improved Tracking
- Officers can monitor applications they've processed
- Identify delays in Accounts review
- Better visibility into the full pipeline

### 3. Better Communication
- Officers know which applications to follow up on
- Can provide status updates to applicants
- Can coordinate with Accounts team on specific applications

### 4. Reduced Redundancy
- Previous "Pending Review" duplicated dashboard functionality
- New definition provides unique, valuable information

---

## NEXT STEPS

**Phase 4**: Approved Page Change
- Remove Approved page from Officer dashboard
- Verify Production Dashboard has this functionality

**Phase 5**: Returned Page Update
- Rename "Rejected/Returned" to "Returned"
- Show only Registrar returns
- Exclude Accounts rejections

**Phase 6**: Records Section Structure
- Add filtering
- Add Advanced Filters modal
- Add export functionality
- Ensure only completed processes shown

**Phase 7**: Remove Rejected Page
- Remove route, view, sidebar link, controller method

**Phase 8**: System-Wide Terminology
- Replace "Journalist" with "Media Practitioner" across all views

---

## NOTES

- This change provides Officers with post-processing visibility
- Helps track application progress through the system
- Maintains clear separation between Officer and Accounts responsibilities
- No workflow logic modified - only query filtering changed
- All status transitions remain intact
- Audit logging preserved
