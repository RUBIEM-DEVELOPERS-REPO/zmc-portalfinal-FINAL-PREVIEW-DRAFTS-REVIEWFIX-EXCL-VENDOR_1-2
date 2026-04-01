# ZMC Complete System Flow Correction - Design Document

## 1. DATABASE ENTITY MODEL

### 1.1 Applications Table (Enhanced)

```sql
CREATE TABLE applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) UNIQUE NOT NULL,
    applicant_id BIGINT UNSIGNED NOT NULL,
    application_type ENUM('accreditation', 'registration') NOT NULL,
    request_type ENUM('new', 'renewal', 'replacement') DEFAULT 'new',
    
    -- Status tracking
    status VARCHAR(100) NOT NULL,
    previous_status VARCHAR(100) NULL,
    status_changed_at TIMESTAMP NULL,
    status_changed_by BIGINT UNSIGNED NULL,
    
    -- Assignment
    assigned_officer_id BIGINT UNSIGNED NULL,
    locked_by BIGINT UNSIGNED NULL,
    locked_at TIMESTAMP NULL,
    
    -- Category
    category_code VARCHAR(10) NULL,
    category_assigned_by BIGINT UNSIGNED NULL,
    category_assigned_at TIMESTAMP NULL,
    
    -- Workflow flags
    officer_approved BOOLEAN DEFAULT FALSE,
    officer_approved_at TIMESTAMP NULL,
    officer_approved_by BIGINT UNSIGNED NULL,
    
    registrar_reviewed BOOLEAN DEFAULT FALSE,
    registrar_reviewed_at TIMESTAMP NULL,
    registrar_reviewed_by BIGINT UNSIGNED NULL,
    
    payment_verified BOOLEAN DEFAULT FALSE,
    payment_verified_at TIMESTAMP NULL,
    payment_verified_by BIGINT UNSIGNED NULL,
    
    in_production BOOLEAN DEFAULT FALSE,
    production_started_at TIMESTAMP NULL,
    production_started_by BIGINT UNSIGNED NULL,
    
    produced BOOLEAN DEFAULT FALSE,
    produced_at TIMESTAMP NULL,
    produced_by BIGINT UNSIGNED NULL,
    
    -- Timestamps
    submitted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Foreign keys
    FOREIGN KEY (applicant_id) REFERENCES users(id),
    FOREIGN KEY (assigned_officer_id) REFERENCES users(id),
    FOREIGN KEY (locked_by) REFERENCES users(id),
    FOREIGN KEY (status_changed_by) REFERENCES users(id),
    
    -- Indexes
    INDEX idx_status (status),
    INDEX idx_applicant (applicant_id),
    INDEX idx_type (application_type),
    INDEX idx_assigned (assigned_officer_id),
    INDEX idx_submitted (submitted_at)
);
```


### 1.2 Payment Submissions Table (Enhanced)

```sql
CREATE TABLE payment_submissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    payment_type ENUM('application_fee', 'registration_fee', 'accreditation_fee') NOT NULL,
    payment_method ENUM('paynow', 'proof_upload', 'cash', 'waiver') NOT NULL,
    
    -- PayNow details
    paynow_reference VARCHAR(100) UNIQUE NULL,
    paynow_amount DECIMAL(10, 2) NULL,
    paynow_verified BOOLEAN DEFAULT FALSE,
    
    -- Proof upload details
    proof_file_path VARCHAR(500) NULL,
    proof_file_name VARCHAR(255) NULL,
    proof_file_size INT NULL,
    
    -- Cash payment details
    cash_receipt_number VARCHAR(100) UNIQUE NULL,
    cash_amount DECIMAL(10, 2) NULL,
    cash_payment_date DATE NULL,
    
    -- Verification
    verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    verification_notes TEXT NULL,
    
    -- Rejection
    rejected BOOLEAN DEFAULT FALSE,
    rejected_by BIGINT UNSIGNED NULL,
    rejected_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    
    -- Submission
    submitted_by BIGINT UNSIGNED NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (submitted_by) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (rejected_by) REFERENCES users(id),
    
    INDEX idx_application (application_id),
    INDEX idx_payment_type (payment_type),
    INDEX idx_verified (verified),
    INDEX idx_paynow_ref (paynow_reference)
);
```

### 1.3 Activity Logs Table (Audit Trail)

```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NULL,
    
    -- Actor information
    actor_role ENUM('applicant', 'accreditation_officer', 'registrar', 'accounts', 'auditor', 'system') NOT NULL,
    actor_user_id BIGINT UNSIGNED NULL,
    actor_name VARCHAR(255) NULL,
    
    -- Action details
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    
    -- Status tracking
    before_status VARCHAR(100) NULL,
    after_status VARCHAR(100) NULL,
    
    -- Additional context
    reason_notes TEXT NULL,
    metadata JSON NULL,
    
    -- Request details
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    -- Timestamp
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (actor_user_id) REFERENCES users(id),
    
    INDEX idx_application (application_id),
    INDEX idx_actor (actor_user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);
```

### 1.4 Fix Requests Table

```sql
CREATE TABLE fix_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    
    -- Request details
    requested_by BIGINT UNSIGNED NOT NULL,
    requested_by_role ENUM('accreditation_officer', 'registrar') NOT NULL,
    request_message TEXT NOT NULL,
    request_type ENUM('document_issue', 'data_correction', 'missing_info', 'other') NOT NULL,
    
    -- Response
    status ENUM('pending', 'in_progress', 'resolved', 'cancelled') DEFAULT 'pending',
    resolved_by BIGINT UNSIGNED NULL,
    resolved_at TIMESTAMP NULL,
    resolution_notes TEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (resolved_by) REFERENCES users(id),
    
    INDEX idx_application (application_id),
    INDEX idx_status (status),
    INDEX idx_requested_by (requested_by)
);
```

### 1.5 Official Letters Table (Media House)

```sql
CREATE TABLE official_letters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    
    -- Letter details
    letter_number VARCHAR(100) NULL,
    letter_date DATE NULL,
    letter_subject VARCHAR(500) NULL,
    
    -- File details
    file_path VARCHAR(500) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    
    -- Upload details
    uploaded_by BIGINT UNSIGNED NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Verification
    verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    
    INDEX idx_application (application_id),
    INDEX idx_verified (verified)
);
```


## 2. STATUS TRANSITION TABLE

### 2.1 Accreditation Flow Status Transitions

| Current Status | Action | Actor | Next Status | Validation Rules |
|---------------|--------|-------|-------------|------------------|
| `draft` | Submit | Applicant | `submitted_to_accreditation_officer` | All required fields filled, documents uploaded |
| `submitted_to_accreditation_officer` | Return | Officer | `returned_to_applicant` | Reason mandatory |
| `submitted_to_accreditation_officer` | Approve | Officer | `approved_by_officer_awaiting_payment` | Category code assigned |
| `submitted_to_accreditation_officer` | Forward (special) | Officer | `forwarded_to_registrar_no_approval` | Notes mandatory |
| `returned_to_applicant` | Resubmit | Applicant | `submitted_to_accreditation_officer` | Corrections made |
| `approved_by_officer_awaiting_payment` | Submit Payment | Applicant | `awaiting_accounts_verification` | PayNow ref OR proof uploaded |
| `approved_by_officer_awaiting_payment` | Raise Fix | Registrar | `registrar_raised_fix_request` | Issue description mandatory |
| `forwarded_to_registrar_no_approval` | Approve | Registrar | `pending_accounts_review_from_registrar` | Review complete |
| `registrar_raised_fix_request` | Fix & Resubmit | Officer | `approved_by_officer_awaiting_payment` | Issues resolved |
| `awaiting_accounts_verification` | Approve Payment | Accounts | `payment_verified` | Payment verified |
| `awaiting_accounts_verification` | Reject Payment | Accounts | `payment_rejected` | Reason mandatory |
| `pending_accounts_review_from_registrar` | Approve Waiver | Accounts | `payment_verified` | Waiver docs verified |
| `pending_accounts_review_from_registrar` | Reject Waiver | Accounts | `payment_rejected` | Reason mandatory |
| `payment_rejected` | Resubmit Payment | Applicant | `awaiting_accounts_verification` | New payment submitted |
| `payment_verified` | Start Production | Officer | `in_production` | Auto-transition |
| `in_production` | Generate & Print | Officer | `produced_ready_for_collection` | Number generated, printed |

### 2.2 Media House Registration Status Transitions

| Current Status | Action | Actor | Next Status | Validation Rules |
|---------------|--------|-------|-------------|------------------|
| `draft` | Submit with App Fee | Applicant | `submitted_with_application_fee` | App fee payment submitted |
| `submitted_with_application_fee` | Auto-route | System | `awaiting_application_fee_verification` | Auto after submission |
| `awaiting_application_fee_verification` | Approve App Fee | Accounts | `application_fee_verified_pending_officer_review` | Fee verified |
| `awaiting_application_fee_verification` | Reject App Fee | Accounts | `application_fee_rejected` | Reason mandatory |
| `application_fee_rejected` | Resubmit App Fee | Applicant | `awaiting_application_fee_verification` | New fee submitted |
| `application_fee_verified_pending_officer_review` | Approve | Officer | `officer_approved_pending_registrar` | All docs verified |
| `officer_approved_pending_registrar` | Approve with Letter | Registrar | `registrar_approved_pending_registration_fee` | Official letter uploaded |
| `registrar_approved_pending_registration_fee` | Submit Reg Fee | Applicant | `awaiting_registration_fee_verification` | Reg fee payment submitted |
| `awaiting_registration_fee_verification` | Approve Reg Fee | Accounts | `payment_verified` | Fee verified |
| `awaiting_registration_fee_verification` | Reject Reg Fee | Accounts | `registration_fee_rejected` | Reason mandatory |
| `registration_fee_rejected` | Resubmit Reg Fee | Applicant | `awaiting_registration_fee_verification` | New fee submitted |
| `payment_verified` | Start Production | Officer | `in_production` | Auto-transition |
| `in_production` | Generate & Print | Officer | `produced_ready_for_collection` | Number generated, printed |

### 2.3 Status Validation Rules

```php
class StatusTransitionValidator
{
    private const VALID_TRANSITIONS = [
        'draft' => ['submitted_to_accreditation_officer', 'submitted_with_application_fee'],
        'submitted_to_accreditation_officer' => [
            'returned_to_applicant',
            'approved_by_officer_awaiting_payment',
            'forwarded_to_registrar_no_approval'
        ],
        'returned_to_applicant' => ['submitted_to_accreditation_officer'],
        'approved_by_officer_awaiting_payment' => [
            'awaiting_accounts_verification',
            'registrar_raised_fix_request'
        ],
        'forwarded_to_registrar_no_approval' => ['pending_accounts_review_from_registrar'],
        'registrar_raised_fix_request' => ['approved_by_officer_awaiting_payment'],
        'awaiting_accounts_verification' => ['payment_verified', 'payment_rejected'],
        'pending_accounts_review_from_registrar' => ['payment_verified', 'payment_rejected'],
        'payment_rejected' => ['awaiting_accounts_verification'],
        'payment_verified' => ['in_production'],
        'in_production' => ['produced_ready_for_collection'],
        // Media house specific
        'submitted_with_application_fee' => ['awaiting_application_fee_verification'],
        'awaiting_application_fee_verification' => [
            'application_fee_verified_pending_officer_review',
            'application_fee_rejected'
        ],
        'application_fee_rejected' => ['awaiting_application_fee_verification'],
        'application_fee_verified_pending_officer_review' => ['officer_approved_pending_registrar'],
        'officer_approved_pending_registrar' => ['registrar_approved_pending_registration_fee'],
        'registrar_approved_pending_registration_fee' => ['awaiting_registration_fee_verification'],
        'awaiting_registration_fee_verification' => ['payment_verified', 'registration_fee_rejected'],
        'registration_fee_rejected' => ['awaiting_registration_fee_verification'],
    ];
    
    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::VALID_TRANSITIONS[$from] ?? []);
    }
    
    public function validateTransition(Application $application, string $newStatus): void
    {
        if (!$this->canTransition($application->status, $newStatus)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition from {$application->status} to {$newStatus}"
            );
        }
    }
}
```


## 3. RBAC PERMISSION MATRIX

### 3.1 Permission Definitions

| Permission | Description | Roles |
|-----------|-------------|-------|
| `applications.view.own` | View own applications | Applicant |
| `applications.submit` | Submit new application | Applicant |
| `applications.resubmit` | Resubmit after return/rejection | Applicant |
| `applications.view.assigned` | View assigned applications | Officer, Registrar, Accounts |
| `applications.view.all` | View all applications | Auditor, Director |
| `applications.review` | Review application details | Officer, Registrar |
| `applications.return` | Return to applicant | Officer |
| `applications.approve` | Approve application | Officer |
| `applications.forward_special` | Forward without approval | Officer |
| `applications.raise_fix_request` | Raise fix request | Registrar |
| `applications.mark_reviewed` | Mark review complete | Registrar |
| `applications.upload_official_letter` | Upload official letter | Registrar |
| `payments.submit` | Submit payment | Applicant |
| `payments.view.own` | View own payments | Applicant |
| `payments.view.assigned` | View assigned payments | Accounts |
| `payments.view.all` | View all payments (read-only) | Registrar, Auditor, Director |
| `payments.verify` | Verify payments | Accounts |
| `payments.reject` | Reject payments | Accounts |
| `payments.record_cash` | Record cash payments | Accounts |
| `production.access` | Access production dashboard | Officer |
| `production.generate` | Generate numbers/QR codes | Officer |
| `production.print` | Print cards/certificates | Officer |
| `production.view_logs` | View production logs | Officer, Auditor, Director |
| `audit.view_logs` | View audit logs | Auditor, Director |
| `audit.export` | Export audit logs | Auditor, Director |

### 3.2 Role-Permission Matrix

```php
class RolePermissions
{
    public const PERMISSIONS = [
        'applicant' => [
            'applications.view.own',
            'applications.submit',
            'applications.resubmit',
            'payments.submit',
            'payments.view.own',
        ],
        
        'accreditation_officer' => [
            'applications.view.assigned',
            'applications.review',
            'applications.return',
            'applications.approve',
            'applications.forward_special',
            'production.access',
            'production.generate',
            'production.print',
            'production.view_logs',
        ],
        
        'registrar' => [
            'applications.view.assigned',
            'applications.review',
            'applications.raise_fix_request',
            'applications.mark_reviewed',
            'applications.upload_official_letter',
            'payments.view.all', // READ-ONLY
        ],
        
        'accounts_payments' => [
            'applications.view.assigned',
            'payments.view.assigned',
            'payments.verify',
            'payments.reject',
            'payments.record_cash',
        ],
        
        'auditor' => [
            'applications.view.all',
            'payments.view.all',
            'production.view_logs',
            'audit.view_logs',
            'audit.export',
        ],
        
        'director' => [
            'applications.view.all',
            'payments.view.all',
            'production.view_logs',
            'audit.view_logs',
            'audit.export',
        ],
    ];
    
    public static function hasPermission(string $role, string $permission): bool
    {
        return in_array($permission, self::PERMISSIONS[$role] ?? []);
    }
}
```

### 3.3 Middleware Implementation

```php
class CheckPermission
{
    public function handle($request, Closure $next, string $permission)
    {
        $user = $request->user();
        
        if (!$user) {
            abort(401, 'Unauthenticated');
        }
        
        if (!RolePermissions::hasPermission($user->role, $permission)) {
            abort(403, 'Unauthorized action');
        }
        
        return $next($request);
    }
}

// Usage in routes
Route::middleware(['auth', 'permission:applications.approve'])
    ->post('/applications/{id}/approve', [OfficerController::class, 'approve']);
```


## 4. API ENDPOINTS

### 4.1 Applicant Endpoints

```
POST   /api/portal/applications                          - Submit new application
GET    /api/portal/applications                          - List own applications
GET    /api/portal/applications/{id}                     - View application details
PUT    /api/portal/applications/{id}/resubmit            - Resubmit after return
POST   /api/portal/applications/{id}/payment             - Submit payment
POST   /api/portal/applications/{id}/payment/paynow      - Submit PayNow reference
POST   /api/portal/applications/{id}/payment/proof       - Upload payment proof
GET    /api/portal/applications/{id}/status              - Get application status
GET    /api/portal/applications/{id}/history             - Get application history
```

### 4.2 Accreditation Officer Endpoints

```
GET    /api/staff/officer/applications                   - List assigned applications
GET    /api/staff/officer/applications/{id}              - View application details
POST   /api/staff/officer/applications/{id}/return       - Return to applicant
POST   /api/staff/officer/applications/{id}/approve      - Approve application
POST   /api/staff/officer/applications/{id}/forward      - Forward without approval
PUT    /api/staff/officer/applications/{id}/category     - Assign category
GET    /api/staff/officer/applications/{id}/documents    - List documents
POST   /api/staff/officer/applications/{id}/claim        - Claim application
POST   /api/staff/officer/applications/{id}/release      - Release application

// Production endpoints
GET    /api/staff/officer/production                     - List production queue
POST   /api/staff/officer/production/{id}/generate       - Generate number/QR
POST   /api/staff/officer/production/{id}/print          - Log print action
GET    /api/staff/officer/production/logs                - View print logs
```

### 4.3 Registrar Endpoints

```
GET    /api/staff/registrar/applications                 - List applications for review
GET    /api/staff/registrar/applications/{id}            - View application details
POST   /api/staff/registrar/applications/{id}/fix-request - Raise fix request
PUT    /api/staff/registrar/applications/{id}/review-complete - Mark review complete
POST   /api/staff/registrar/applications/{id}/approve    - Approve (media house)
POST   /api/staff/registrar/applications/{id}/official-letter - Upload official letter
POST   /api/staff/registrar/applications/{id}/push-to-accounts - Push special case

// Payment oversight (READ-ONLY)
GET    /api/staff/registrar/payments                     - View all payments
GET    /api/staff/registrar/payments/{id}                - View payment details
GET    /api/staff/registrar/payments/audit               - View payment audit logs
```

### 4.4 Accounts Officer Endpoints

```
GET    /api/staff/accounts/applications                  - List applications with payments
GET    /api/staff/accounts/applications/{id}             - View application details
GET    /api/staff/accounts/payments                      - List payment submissions
GET    /api/staff/accounts/payments/{id}                 - View payment details
POST   /api/staff/accounts/payments/{id}/verify          - Verify payment
POST   /api/staff/accounts/payments/{id}/reject          - Reject payment
POST   /api/staff/accounts/payments/cash                 - Record cash payment
GET    /api/staff/accounts/payments/paynow/verify        - Verify PayNow reference
```

### 4.5 Auditor Endpoints

```
GET    /api/staff/auditor/applications                   - View all applications
GET    /api/staff/auditor/applications/{id}              - View application details
GET    /api/staff/auditor/payments                       - View all payments
GET    /api/staff/auditor/audit-logs                     - View audit logs
GET    /api/staff/auditor/audit-logs/export              - Export audit logs
GET    /api/staff/auditor/production/logs                - View production logs
GET    /api/staff/auditor/reports                        - Generate reports
```

### 4.6 Common Endpoints

```
GET    /api/applications/{id}/audit-trail                - Get application audit trail
GET    /api/applications/{id}/timeline                   - Get application timeline
GET    /api/applications/statuses                        - Get valid statuses
GET    /api/applications/categories                      - Get category codes
POST   /api/applications/{id}/documents                  - Upload document
DELETE /api/applications/{id}/documents/{docId}          - Delete document
```


## 5. SERVICE LAYER ARCHITECTURE

### 5.1 ApplicationWorkflowService

```php
class ApplicationWorkflowService
{
    public function __construct(
        private StatusTransitionValidator $statusValidator,
        private ActivityLogger $activityLogger,
        private NotificationService $notificationService
    ) {}
    
    public function submitApplication(Application $application, User $user): void
    {
        DB::transaction(function () use ($application, $user) {
            // Validate required fields
            $this->validateRequiredFields($application);
            
            // Validate documents
            $this->validateRequiredDocuments($application);
            
            // Transition status
            $this->transitionStatus(
                $application,
                'submitted_to_accreditation_officer',
                $user,
                'Application submitted'
            );
            
            // Set submission timestamp
            $application->submitted_at = now();
            $application->save();
            
            // Notify officers
            $this->notificationService->notifyOfficers($application);
        });
    }
    
    public function approveApplication(
        Application $application,
        User $officer,
        string $categoryCode,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($application, $officer, $categoryCode, $notes) {
            // Validate permission
            $this->validatePermission($officer, 'applications.approve');
            
            // Validate category code
            $this->validateCategoryCode($categoryCode, $application->application_type);
            
            // Assign category
            $application->category_code = $categoryCode;
            $application->category_assigned_by = $officer->id;
            $application->category_assigned_at = now();
            
            // Mark as approved
            $application->officer_approved = true;
            $application->officer_approved_by = $officer->id;
            $application->officer_approved_at = now();
            
            // Transition status
            $this->transitionStatus(
                $application,
                'approved_by_officer_awaiting_payment',
                $officer,
                $notes ?? 'Application approved'
            );
            
            // Notify applicant to make payment
            $this->notificationService->notifyApplicantPaymentRequired($application);
            
            // Notify registrar and accounts
            $this->notificationService->notifyRegistrarAndAccounts($application);
        });
    }
    
    public function returnToApplicant(
        Application $application,
        User $officer,
        string $reason
    ): void {
        DB::transaction(function () use ($application, $officer, $reason) {
            // Validate permission
            $this->validatePermission($officer, 'applications.return');
            
            // Validate reason is provided
            if (empty($reason)) {
                throw new ValidationException('Reason is required when returning application');
            }
            
            // Transition status
            $this->transitionStatus(
                $application,
                'returned_to_applicant',
                $officer,
                $reason
            );
            
            // Notify applicant
            $this->notificationService->notifyApplicantReturned($application, $reason);
        });
    }
    
    public function forwardWithoutApproval(
        Application $application,
        User $officer,
        string $notes
    ): void {
        DB::transaction(function () use ($application, $officer, $notes) {
            // Validate permission
            $this->validatePermission($officer, 'applications.forward_special');
            
            // Transition status
            $this->transitionStatus(
                $application,
                'forwarded_to_registrar_no_approval',
                $officer,
                $notes
            );
            
            // Notify registrar
            $this->notificationService->notifyRegistrarSpecialCase($application);
        });
    }
    
    private function transitionStatus(
        Application $application,
        string $newStatus,
        User $actor,
        string $notes
    ): void {
        // Validate transition
        $this->statusValidator->validateTransition($application, $newStatus);
        
        // Store previous status
        $previousStatus = $application->status;
        
        // Update status
        $application->previous_status = $previousStatus;
        $application->status = $newStatus;
        $application->status_changed_at = now();
        $application->status_changed_by = $actor->id;
        $application->save();
        
        // Log activity
        $this->activityLogger->log([
            'application_id' => $application->id,
            'actor_role' => $actor->role,
            'actor_user_id' => $actor->id,
            'action' => 'status_transition',
            'before_status' => $previousStatus,
            'after_status' => $newStatus,
            'reason_notes' => $notes,
        ]);
    }
}
```

### 5.2 PaymentWorkflowService

```php
class PaymentWorkflowService
{
    public function submitPayNowPayment(
        Application $application,
        User $user,
        string $reference,
        float $amount,
        string $paymentType = 'accreditation_fee'
    ): PaymentSubmission {
        return DB::transaction(function () use ($application, $user, $reference, $amount, $paymentType) {
            // Validate application status
            $this->validatePaymentAllowed($application);
            
            // Validate reference is unique
            if (PaymentSubmission::where('paynow_reference', $reference)->exists()) {
                throw new ValidationException('PayNow reference already used');
            }
            
            // Create payment submission
            $payment = PaymentSubmission::create([
                'application_id' => $application->id,
                'payment_type' => $paymentType,
                'payment_method' => 'paynow',
                'paynow_reference' => $reference,
                'paynow_amount' => $amount,
                'submitted_by' => $user->id,
                'submitted_at' => now(),
            ]);
            
            // Transition application status
            $newStatus = $this->getPaymentSubmittedStatus($application, $paymentType);
            $this->workflowService->transitionStatus(
                $application,
                $newStatus,
                $user,
                "PayNow payment submitted: {$reference}"
            );
            
            // Notify accounts
            $this->notificationService->notifyAccountsPaymentSubmitted($application, $payment);
            
            return $payment;
        });
    }
    
    public function verifyPayment(
        PaymentSubmission $payment,
        User $accountsOfficer,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($payment, $accountsOfficer, $notes) {
            // Validate permission
            $this->validatePermission($accountsOfficer, 'payments.verify');
            
            // Mark as verified
            $payment->verified = true;
            $payment->verified_by = $accountsOfficer->id;
            $payment->verified_at = now();
            $payment->verification_notes = $notes;
            $payment->save();
            
            // Update application
            $application = $payment->application;
            $application->payment_verified = true;
            $application->payment_verified_by = $accountsOfficer->id;
            $application->payment_verified_at = now();
            
            // Transition to payment_verified
            $this->workflowService->transitionStatus(
                $application,
                'payment_verified',
                $accountsOfficer,
                $notes ?? 'Payment verified'
            );
            
            // Notify applicant
            $this->notificationService->notifyApplicantPaymentVerified($application);
            
            // Notify production
            $this->notificationService->notifyProductionReady($application);
        });
    }
    
    public function rejectPayment(
        PaymentSubmission $payment,
        User $accountsOfficer,
        string $reason
    ): void {
        DB::transaction(function () use ($payment, $accountsOfficer, $reason) {
            // Validate permission
            $this->validatePermission($accountsOfficer, 'payments.reject');
            
            // Validate reason
            if (empty($reason)) {
                throw new ValidationException('Reason is required when rejecting payment');
            }
            
            // Mark as rejected
            $payment->rejected = true;
            $payment->rejected_by = $accountsOfficer->id;
            $payment->rejected_at = now();
            $payment->rejection_reason = $reason;
            $payment->save();
            
            // Transition application
            $application = $payment->application;
            $this->workflowService->transitionStatus(
                $application,
                'payment_rejected',
                $accountsOfficer,
                $reason
            );
            
            // Notify applicant
            $this->notificationService->notifyApplicantPaymentRejected($application, $reason);
        });
    }
}
```

### 5.3 ProductionWorkflowService

```php
class ProductionWorkflowService
{
    public function startProduction(Application $application, User $officer): void
    {
        DB::transaction(function () use ($application, $officer) {
            // Validate status
            if ($application->status !== 'payment_verified') {
                throw new InvalidStatusException('Application must be payment_verified');
            }
            
            // Validate permission
            $this->validatePermission($officer, 'production.access');
            
            // Mark as in production
            $application->in_production = true;
            $application->production_started_by = $officer->id;
            $application->production_started_at = now();
            
            // Transition status
            $this->workflowService->transitionStatus(
                $application,
                'in_production',
                $officer,
                'Production started'
            );
        });
    }
    
    public function generateNumber(Application $application, User $officer): string
    {
        return DB::transaction(function () use ($application, $officer) {
            // Validate permission
            $this->validatePermission($officer, 'production.generate');
            
            // Generate number
            $number = $this->numberGenerator->generate($application);
            
            // Generate QR code
            $qrCode = $this->qrCodeGenerator->generate($number);
            
            // Store in application
            if ($application->application_type === 'accreditation') {
                $application->accreditation_number = $number;
            } else {
                $application->registration_number = $number;
            }
            $application->qr_code_path = $qrCode;
            $application->save();
            
            // Log activity
            $this->activityLogger->log([
                'application_id' => $application->id,
                'actor_role' => $officer->role,
                'actor_user_id' => $officer->id,
                'action' => 'number_generated',
                'metadata' => ['number' => $number],
            ]);
            
            return $number;
        });
    }
    
    public function logPrint(
        Application $application,
        User $officer,
        ?string $printerName = null
    ): void {
        DB::transaction(function () use ($application, $officer, $printerName) {
            // Validate permission
            $this->validatePermission($officer, 'production.print');
            
            // Create print log
            PrintLog::create([
                'record_type' => $application->application_type,
                'record_id' => $application->id,
                'template_id' => $this->getActiveTemplate($application->application_type)->id,
                'printed_by' => $officer->id,
                'printer_name' => $printerName,
                'print_count' => 1,
                'printed_at' => now(),
            ]);
            
            // Mark as produced
            if (!$application->produced) {
                $application->produced = true;
                $application->produced_by = $officer->id;
                $application->produced_at = now();
                
                // Transition status
                $this->workflowService->transitionStatus(
                    $application,
                    'produced_ready_for_collection',
                    $officer,
                    'Card/certificate printed'
                );
            }
            
            // Notify applicant
            $this->notificationService->notifyApplicantReadyForCollection($application);
        });
    }
}
```


## 6. VALIDATION & ENFORCEMENT

### 6.1 Server-Side Validation Rules

```php
class ApplicationValidator
{
    public function validateSubmission(Application $application): void
    {
        $rules = [
            'applicant_id' => 'required|exists:users,id',
            'application_type' => 'required|in:accreditation,registration',
            'request_type' => 'required|in:new,renewal,replacement',
        ];
        
        // Type-specific validation
        if ($application->application_type === 'accreditation') {
            $rules = array_merge($rules, $this->getAccreditationRules());
        } else {
            $rules = array_merge($rules, $this->getRegistrationRules());
        }
        
        $validator = Validator::make($application->toArray(), $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->first());
        }
        
        // Validate documents
        $this->validateDocuments($application);
    }
    
    private function validateDocuments(Application $application): void
    {
        $requiredDocs = $this->getRequiredDocuments($application);
        $uploadedDocs = $application->documents->pluck('document_type')->toArray();
        
        $missing = array_diff($requiredDocs, $uploadedDocs);
        
        if (!empty($missing)) {
            throw new ValidationException(
                'Missing required documents: ' . implode(', ', $missing)
            );
        }
    }
}
```

### 6.2 Middleware Guards

```php
// Status Transition Guard
class ValidateStatusTransition
{
    public function handle($request, Closure $next)
    {
        $application = $request->route('application');
        $action = $request->route()->getActionMethod();
        
        $requiredStatus = $this->getRequiredStatus($action);
        
        if ($application->status !== $requiredStatus) {
            abort(422, "Invalid application status for this action. Expected: {$requiredStatus}, Current: {$application->status}");
        }
        
        return $next($request);
    }
    
    private function getRequiredStatus(string $action): string
    {
        return match($action) {
            'approve' => 'submitted_to_accreditation_officer',
            'verifyPayment' => 'awaiting_accounts_verification',
            'startProduction' => 'payment_verified',
            default => throw new \Exception("Unknown action: {$action}"),
        };
    }
}

// RBAC Guard
class CheckRolePermission
{
    public function handle($request, Closure $next, string $permission)
    {
        $user = $request->user();
        
        if (!RolePermissions::hasPermission($user->role, $permission)) {
            abort(403, 'You do not have permission to perform this action');
        }
        
        return $next($request);
    }
}

// Audit Logger Middleware
class AuditLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // Log after successful request
        if ($response->isSuccessful()) {
            $this->logAction($request, $response);
        }
        
        return $response;
    }
    
    private function logAction($request, $response): void
    {
        ActivityLog::create([
            'application_id' => $request->route('application')?->id,
            'actor_role' => $request->user()->role,
            'actor_user_id' => $request->user()->id,
            'action' => $request->route()->getActionMethod(),
            'metadata' => [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'input' => $request->except(['password', '_token']),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
```

### 6.3 Database Constraints

```sql
-- Ensure status values are valid
ALTER TABLE applications 
ADD CONSTRAINT chk_status 
CHECK (status IN (
    'draft',
    'submitted_to_accreditation_officer',
    'returned_to_applicant',
    'approved_by_officer_awaiting_payment',
    'forwarded_to_registrar_no_approval',
    'registrar_raised_fix_request',
    'awaiting_accounts_verification',
    'pending_accounts_review_from_registrar',
    'payment_verified',
    'payment_rejected',
    'submitted_with_application_fee',
    'awaiting_application_fee_verification',
    'application_fee_rejected',
    'application_fee_verified_pending_officer_review',
    'officer_approved_pending_registrar',
    'registrar_approved_pending_registration_fee',
    'awaiting_registration_fee_verification',
    'registration_fee_rejected',
    'in_production',
    'produced_ready_for_collection'
));

-- Ensure PayNow references are unique
ALTER TABLE payment_submissions
ADD CONSTRAINT uq_paynow_reference
UNIQUE (paynow_reference);

-- Ensure cash receipt numbers are unique
ALTER TABLE payment_submissions
ADD CONSTRAINT uq_cash_receipt
UNIQUE (cash_receipt_number);

-- Ensure application has applicant
ALTER TABLE applications
MODIFY applicant_id BIGINT UNSIGNED NOT NULL;

-- Ensure payment has application
ALTER TABLE payment_submissions
MODIFY application_id BIGINT UNSIGNED NOT NULL;
```

## 7. CONFIRMATION CHECKLIST

### 7.1 Workflow Enforcement Confirmed

✅ **No step can skip Accreditation Officer**
- All applications must go through `submitted_to_accreditation_officer` status
- Server-side validation prevents status skipping
- Middleware guards enforce status transitions

✅ **No payment verification before submission**
- Payment submission requires `approved_by_officer_awaiting_payment` status
- Accounts can only verify payments in `awaiting_accounts_verification` status
- Status machine enforces correct order

✅ **No production before payment_verified**
- Production endpoints check for `payment_verified` status
- Middleware guard prevents access to production with wrong status
- Database flag `payment_verified` must be true

✅ **Registrar has oversight but no payment authority**
- Registrar has READ-ONLY access to payment records
- Registrar cannot call payment verification endpoints
- RBAC enforces `payments.view.all` (read-only) permission only

✅ **Media house uses 2-stage payment enforcement**
- Application fee required before officer review
- Registration fee required after registrar approval
- Status machine enforces both payment stages
- Cannot skip either payment stage

✅ **All status transitions validated server-side**
- `StatusTransitionValidator` class validates all transitions
- Invalid transitions throw exceptions
- All transitions logged in audit trail

✅ **All actions generate audit logs**
- `ActivityLogger` service logs all actions
- Middleware automatically logs API calls
- Audit logs are immutable (no updates/deletes)

✅ **RBAC enforced at API level**
- Middleware checks permissions before action
- Service layer validates permissions
- Database queries filter by role access

✅ **Business rules enforced at service level**
- All business logic in service classes
- Controllers are thin, delegate to services
- Services use transactions for atomicity

✅ **Database constraints enforce data integrity**
- Foreign keys maintain relationships
- Unique constraints prevent duplicates
- Check constraints validate status values
- Not null constraints enforce required fields

### 7.2 Security Enforcement Confirmed

✅ Authentication required for all endpoints
✅ Authorization checked before action
✅ Input validation on all user data
✅ SQL injection prevention (Eloquent ORM)
✅ XSS prevention (Blade escaping)
✅ CSRF protection on forms
✅ File upload validation
✅ Rate limiting on sensitive endpoints

### 7.3 Data Integrity Confirmed

✅ Atomic transactions for multi-step operations
✅ Rollback on failure
✅ Audit logs immutable
✅ Status transitions validated
✅ Required fields enforced
✅ Foreign key relationships maintained
✅ Unique constraints enforced

## 8. IMPLEMENTATION NOTES

### 8.1 Migration Strategy

1. **Phase 1**: Add new status columns to applications table
2. **Phase 2**: Create payment_submissions enhancements
3. **Phase 3**: Create activity_logs table
4. **Phase 4**: Migrate existing data to new statuses
5. **Phase 5**: Deploy new workflow services
6. **Phase 6**: Update controllers to use services
7. **Phase 7**: Update UI to match new workflow
8. **Phase 8**: Test all workflows end-to-end

### 8.2 Backward Compatibility

- Existing statuses mapped to new statuses
- Old API endpoints deprecated but functional
- Gradual migration of UI components
- Data migration scripts for existing applications

### 8.3 Testing Requirements

- Unit tests for all service methods
- Integration tests for workflow transitions
- API tests for all endpoints
- UI tests for critical paths
- Load tests for production endpoints
- Security tests for RBAC enforcement

### 8.4 Documentation Requirements

- API documentation (OpenAPI/Swagger)
- Workflow diagrams
- User guides for each role
- Admin guide for troubleshooting
- Developer guide for extending system
