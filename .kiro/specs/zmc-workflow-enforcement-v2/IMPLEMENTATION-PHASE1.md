# ZMC Workflow Enforcement v2 - Phase 1 Implementation Complete

## Date: February 26, 2026

## Overview
Phase 1 of the comprehensive workflow enforcement has been completed. This phase establishes the foundation for strict server-side workflow validation and RBAC enforcement.

## Completed Components

### 1. Status Constants (Application Model)
**File**: `app/Models/Application.php`

Added new status constants for strict workflow enforcement:
- `SUBMITTED_TO_ACCREDITATION_OFFICER` - Initial submission
- `APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT` - After officer approval
- `AWAITING_ACCOUNTS_VERIFICATION` - Payment submitted, awaiting verification
- `REGISTRAR_RAISED_FIX_REQUEST` - Registrar requests fixes
- `PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL` - Special cases from registrar
- `REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT` - Media house two-stage
- `PRODUCED_READY_FOR_COLLECTION` - Production complete

### 2. StatusTransitionValidator Service
**File**: `app/Services/StatusTransitionValidator.php`

**Purpose**: Enforces strict status transition rules - no skipping allowed.

**Key Features**:
- Complete transition map defining all valid status changes
- `isValidTransition()` - Validates if a transition is allowed
- `validateOrFail()` - Throws exception on invalid transition
- `getAllowedTransitions()` - Returns allowed next statuses
- Helper methods: `requiresPayment()`, `isAccountsStage()`, `isProductionStage()`

**Transition Rules**:
- Standard flow: Submitted → Officer Review → Payment → Accounts → Production
- Waiver flow: Officer → Forward without approval → Registrar → Accounts
- Media house: App Fee → Officer → Registrar → Reg Fee → Accounts → Production
- Fix requests: Registrar can send back to Officer
- No status skipping - all transitions validated

### 3. ApplicationWorkflowService
**File**: `app/Services/ApplicationWorkflowService.php`

**Purpose**: Handles all application workflow transitions with validation.

**Methods**:
- `submitApplication()` - Applicant submits application
- `approveApplication()` - Officer approves (triggers payment prompt)
- `returnToApplicant()` - Officer requests corrections
- `forwardWithoutApproval()` - Officer forwards special/waiver cases
- `registrarRaiseFixRequest()` - Registrar sends fix request to officer
- `registrarApprove()` - Registrar approves (media house flow)
- `registrarPushToAccounts()` - Registrar pushes special case to accounts
- `sendToProduction()` - System sends to production after payment verified
- `markProduced()` - Production complete
- `markIssued()` - Final issuance

**Features**:
- All methods use DB transactions
- Status transition validation on every action
- Automatic audit logging
- Updates current_stage and timestamps
- Returns fresh application instance

### 4. PaymentWorkflowService
**File**: `app/Services/PaymentWorkflowService.php`

**Purpose**: Handles payment submission and verification workflow.

**Methods**:
- `submitPayNowPayment()` - Submit PayNow reference
- `submitProofPayment()` - Upload payment proof
- `submitWaiver()` - Submit waiver request
- `verifyPayment()` - Accounts verifies payment (auto-sends to production)
- `rejectPayment()` - Accounts rejects payment
- `areBothPaymentStagesVerified()` - Check two-stage payment completion

**Features**:
- Creates PaymentSubmission records
- Handles single and two-stage payments
- Updates application status with validation
- Automatic transition to production after verification
- Comprehensive audit logging

### 5. Middleware - EnforceWorkflowTransitions
**File**: `app/Http/Middleware/EnforceWorkflowTransitions.php`

**Purpose**: Monitors and logs all workflow transitions.

**Features**:
- Intercepts requests that modify application status
- Logs all status changes for audit trail
- Records user, role, and route information
- Non-blocking - validation happens in service layer

### 6. Middleware - EnforceRoleBasedAccess
**File**: `app/Http/Middleware/EnforceRoleBasedAccess.php`

**Purpose**: Enforces role-based access control at API level.

**Role Permissions**:
- **Applicant**: submit, upload_payment, view_status, withdraw
- **Accreditation Officer**: review, approve, return, forward_without_approval, request_correction, production, generate_document, mark_produced, mark_issued
- **Registrar**: review, raise_fix_request, approve_media_house, push_to_accounts, view_payment_oversight (READ-ONLY)
- **Accounts**: verify_payment, reject_payment, view_submissions
- **Director**: view_reports, view_analytics, view_oversight

**Features**:
- Checks active staff role from session
- Validates action permissions
- Special check: Registrar cannot verify payments (read-only)
- Returns 403 Forbidden on unauthorized access

## Workflow Enforcement Rules

### Standard Accreditation Flow
```
Applicant → SUBMITTED_TO_ACCREDITATION_OFFICER
         ↓
Officer Review → APPROVED_BY_ACCREDITATION_OFFICER_AWAITING_PAYMENT
         ↓
Payment Submission → AWAITING_ACCOUNTS_VERIFICATION
         ↓
Accounts Verification → PAYMENT_VERIFIED
         ↓
Auto-send → PRODUCTION_QUEUE
         ↓
Production → PRODUCED_READY_FOR_COLLECTION
         ↓
Issuance → ISSUED
```

### Waiver/Special Case Flow
```
Officer → FORWARDED_TO_REGISTRAR_NO_APPROVAL
       ↓
Registrar Review → PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR_SPECIAL
       ↓
Accounts Verification → PAYMENT_VERIFIED
       ↓
Production → PRODUCED_READY_FOR_COLLECTION
```

### Media House Two-Stage Payment
```
Applicant → SUBMITTED_WITH_APP_FEE (app fee paid)
         ↓
Officer Review → VERIFIED_BY_OFFICER_PENDING_REGISTRAR
         ↓
Registrar Review + Official Letter → REGISTRAR_APPROVED_PENDING_REGISTRATION_FEE_PAYMENT
         ↓
Registration Fee Payment → REG_FEE_SUBMITTED_AWAITING_VERIFICATION
         ↓
Accounts Verification → PAYMENT_VERIFIED
         ↓
Production → PRODUCED_READY_FOR_COLLECTION
```

### Fix Request Flow
```
Registrar → REGISTRAR_RAISED_FIX_REQUEST
         ↓
Officer Fixes → SUBMITTED_TO_ACCREDITATION_OFFICER
         ↓
(Resume normal flow)
```

## Key Enforcement Points

### 1. No Status Skipping
- All transitions validated by StatusTransitionValidator
- Invalid transitions throw exceptions
- Service layer enforces rules before database updates

### 2. RBAC at API Level
- Middleware checks permissions before controller actions
- Role-based action matrix enforced
- Registrar has read-only payment access

### 3. Audit Logging
- All workflow actions logged via ActivityLogger
- Includes: actor_role, actor_user_id, from_status, to_status, metadata
- Middleware logs all status transitions

### 4. Automatic Routing
- Payment verification auto-sends to production
- No manual intervention needed for standard flow
- System handles stage transitions

### 5. Payment Verification Authority
- Only Accounts can verify payments
- Registrar has oversight dashboard (read-only)
- Payment verification triggers production queue

## Next Steps - Phase 2

### Controller Refactoring
1. Update AccreditationOfficerController to use ApplicationWorkflowService
2. Update RegistrarController to use ApplicationWorkflowService
3. Update AccountsPaymentsController to use PaymentWorkflowService
4. Add middleware to routes for RBAC enforcement

### Database Updates
1. Add migration for new status values
2. Update existing applications to new status constants (data migration)
3. Add indexes for new status queries

### UI Updates
1. Update status display labels
2. Add workflow visualization
3. Update dashboards to filter by new statuses
4. Add permission checks in views

### Testing
1. Unit tests for StatusTransitionValidator
2. Integration tests for workflow services
3. RBAC middleware tests
4. End-to-end workflow tests

## Files Created
- `app/Models/Application.php` (updated)
- `app/Services/StatusTransitionValidator.php`
- `app/Services/ApplicationWorkflowService.php`
- `app/Services/PaymentWorkflowService.php`
- `app/Http/Middleware/EnforceWorkflowTransitions.php`
- `app/Http/Middleware/EnforceRoleBasedAccess.php`
- `.kiro/specs/zmc-workflow-enforcement-v2/IMPLEMENTATION-PHASE1.md`

## Usage Examples

### Officer Approves Application
```php
use App\Services\ApplicationWorkflowService;

// In controller
public function approve(Request $request, Application $application)
{
    $application = ApplicationWorkflowService::approveApplication(
        $application,
        ['notes' => $request->input('notes')]
    );
    
    return back()->with('success', 'Application approved. Applicant prompted for payment.');
}
```

### Accounts Verifies Payment
```php
use App\Services\PaymentWorkflowService;

public function verifyPayment(Request $request, Application $application)
{
    $application = PaymentWorkflowService::verifyPayment(
        $application,
        [
            'notes' => $request->input('notes'),
            'payment_submission_id' => $request->input('payment_submission_id'),
        ]
    );
    
    // Application automatically sent to production
    return back()->with('success', 'Payment verified. Application sent to production.');
}
```

### Apply Middleware to Routes
```php
// In routes/web.php
Route::middleware(['auth', 'role.access:approve'])->group(function () {
    Route::post('/staff/officer/applications/{application}/approve', 
        [AccreditationOfficerController::class, 'approve']
    )->name('staff.officer.applications.approve');
});
```

## Status
✅ Phase 1 Complete - Foundation established
⏳ Phase 2 Pending - Controller refactoring and integration
⏳ Phase 3 Pending - UI updates and testing

## Notes
- All services use DB transactions for data integrity
- Status transitions are atomic operations
- Audit logging is automatic and comprehensive
- RBAC enforcement happens at middleware level
- Services can be used independently or chained
- Backward compatibility maintained with legacy statuses
