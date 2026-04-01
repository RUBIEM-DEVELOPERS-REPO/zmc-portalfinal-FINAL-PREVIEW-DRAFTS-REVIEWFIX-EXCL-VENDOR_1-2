# ZMC Workflow Enforcement - Technical Design

## Database Schema

### 1. Applications Table (Enhanced)
```sql
applications:
  id: bigint primary key
  reference: string unique (auto-generated: ZMC-ACC-2024-0001)
  application_type: enum('accreditation', 'registration')
  request_type: enum('new', 'renewal', 'replacement')
  
  -- Applicant Info
  applicant_user_id: bigint foreign key (users)
  applicant_type: enum('media_practitioner', 'media_house')
  
  -- Status Machine
  status: enum (see status list below)
  previous_status: enum nullable
  status_changed_at: timestamp
  status_changed_by: bigint foreign key (users)
  
  -- Category (Official Codes)
  category: enum('JE','JF','JO','JS','JM','JP','JD','JT','MC','MA','MF','MN','DG','MP','MS','MV') nullable
  category_assigned_by: bigint foreign key (users) nullable
  category_assigned_at: timestamp nullable
  
  -- Workflow Tracking
  submitted_at: timestamp nullable
  accreditation_officer_approved_at: timestamp nullable
  accreditation_officer_approved_by: bigint foreign key (users) nullable
  registrar_reviewed_at: timestamp nullable
  registrar_reviewed_by: bigint foreign key (users) nullable
  payment_verified_at: timestamp nullable
  payment_verified_by: bigint foreign key (users) nullable
  produced_at: timestamp nullable
  produced_by: bigint foreign key (users) nullable
  collected_at: timestamp nullable
  
  -- Payment Info
  payment_method: enum('paynow', 'proof_upload') nullable
  paynow_reference: string nullable
  payment_amount: decimal nullable
  payment_currency: string default 'USD'
  
  -- Return/Rejection
  return_reason: text nullable
  returned_at: timestamp nullable
  returned_by: bigint foreign key (users) nullable
  rejection_reason: text nullable
  rejected_at: timestamp nullable
  rejected_by: bigint foreign key (users) nullable
  
  -- Production
  accreditation_number: string unique nullable
  qr_code_data: text nullable
  print_count: integer default 0
  last_printed_at: timestamp nullable
  last_printed_by: bigint foreign key (users) nullable
  collection_office: string nullable
  
  -- Form Data
  form_data: json
  
  -- Metadata
  created_at: timestamp
  updated_at: timestamp
  deleted_at: timestamp nullable
  version: integer default 1 (for optimistic locking)
  
  -- Indexes
  index(status)
  index(applicant_user_id)
  index(accreditation_officer_approved_by)
  index(payment_verified_at)
  index(reference)
  index(accreditation_number)
```

### Status Enum Values
```php
'draft',
'submitted_to_accreditation_officer',
'returned_to_applicant',
'approved_by_accreditation_officer',
'awaiting_applicant_payment_action',
'paynow_reference_submitted',
'proof_of_payment_submitted',
'awaiting_accounts_verification',
'payment_verified',
'payment_rejected',
'registrar_raised_fix_request',
'fix_applied_by_accreditation_officer',
'registrar_review_completed',
'in_production',
'produced_ready_for_collection',
'collected',
'rejected_final'
```

### 2. Audit Logs Table
```sql
audit_logs:
  id: bigint primary key
  application_id: bigint foreign key (applications)
  
  -- Actor Info
  actor_role: enum('applicant','accreditation_officer','registrar','accounts','production','auditor','director','system')
  actor_user_id: bigint foreign key (users) nullable
  actor_name: string
  
  -- Action Details
  action_type: enum('submit','return','approve','assign_category','raise_fix','apply_fix',
                    'submit_payment','verify_payment','reject_payment','start_production',
                    'print','mark_collected','reject_final','resubmit','status_change')
  
  -- State Tracking
  before_state: json nullable (status + key fields)
  after_state: json nullable (status + key fields)
  
  -- Context
  reason: text nullable
  notes: text nullable
  metadata: json nullable
  
  -- Request Info
  ip_address: string nullable
  user_agent: text nullable
  
  -- Timestamp
  created_at: timestamp
  
  -- Indexes
  index(application_id)
  index(actor_user_id)
  index(action_type)
  index(created_at)
```

### 3. Fix Requests Table
```sql
fix_requests:
  id: bigint primary key
  application_id: bigint foreign key (applications)
  
  -- Request Details
  requested_by: bigint foreign key (users) (Registrar)
  requested_at: timestamp
  issue_description: text
  fields_to_fix: json (list of field names)
  
  -- Resolution
  status: enum('pending','in_progress','resolved','cancelled')
  assigned_to: bigint foreign key (users) (Accreditation Officer)
  resolved_at: timestamp nullable
  resolved_by: bigint foreign key (users) nullable
  resolution_notes: text nullable
  
  -- Metadata
  created_at: timestamp
  updated_at: timestamp
  
  -- Indexes
  index(application_id)
  index(requested_by)
  index(assigned_to)
  index(status)
```

### 4. Payment Submissions Table
```sql
payment_submissions:
  id: bigint primary key
  application_id: bigint foreign key (applications)
  
  -- Submission Details
  payment_method: enum('paynow','proof_upload')
  submitted_by: bigint foreign key (users)
  submitted_at: timestamp
  
  -- PayNow Details
  paynow_reference: string nullable
  paynow_poll_reference: string nullable
  paynow_status: string nullable
  
  -- Proof Details
  proof_file_path: string nullable
  proof_file_name: string nullable
  proof_file_size: integer nullable
  
  -- Verification
  verification_status: enum('pending','verified','rejected')
  verified_by: bigint foreign key (users) nullable
  verified_at: timestamp nullable
  rejection_reason: text nullable
  
  -- Amount
  amount: decimal
  currency: string default 'USD'
  
  -- Metadata
  created_at: timestamp
  updated_at: timestamp
  
  -- Indexes
  index(application_id)
  index(verification_status)
  index(paynow_reference)
```

### 5. Production Print Logs Table
```sql
production_print_logs:
  id: bigint primary key
  application_id: bigint foreign key (applications)
  
  -- Print Details
  print_type: enum('card','certificate','reprint')
  printed_by: bigint foreign key (users)
  printed_at: timestamp
  print_number: integer (1st print, 2nd print, etc.)
  
  -- Document Info
  document_path: string nullable
  document_format: enum('pdf','png')
  
  -- Reason (for reprints)
  reprint_reason: text nullable
  
  -- Collection Office
  collection_office: string
  
  -- QR Code
  qr_code_generated: boolean default false
  qr_code_data: text nullable
  
  -- Metadata
  created_at: timestamp
  
  -- Indexes
  index(application_id)
  index(printed_by)
  index(printed_at)
```

### 6. Messages/Notifications Table
```sql
application_messages:
  id: bigint primary key
  application_id: bigint foreign key (applications)
  
  -- Message Details
  from_role: enum('system','accreditation_officer','registrar','accounts','production')
  from_user_id: bigint foreign key (users) nullable
  to_user_id: bigint foreign key (users)
  
  -- Content
  subject: string
  message: text
  message_type: enum('return','rejection','fix_request','payment_prompt','general')
  
  -- Status
  read_at: timestamp nullable
  
  -- Metadata
  created_at: timestamp
  
  -- Indexes
  index(application_id)
  index(to_user_id)
  index(read_at)
```

## Status Machine Implementation

### ApplicationStatusMachine Service
```php
class ApplicationStatusMachine
{
    // Valid transitions map
    private const TRANSITIONS = [
        'draft' => ['submitted_to_accreditation_officer'],
        'submitted_to_accreditation_officer' => [
            'returned_to_applicant',
            'approved_by_accreditation_officer',
            'rejected_final'
        ],
        'returned_to_applicant' => ['submitted_to_accreditation_officer'],
        'approved_by_accreditation_officer' => [
            'awaiting_applicant_payment_action',
            'registrar_raised_fix_request',
            'registrar_review_completed'
        ],
        'awaiting_applicant_payment_action' => [
            'paynow_reference_submitted',
            'proof_of_payment_submitted'
        ],
        'paynow_reference_submitted' => [
            'payment_verified',
            'payment_rejected'
        ],
        'proof_of_payment_submitted' => [
            'payment_verified',
            'payment_rejected'
        ],
        'payment_rejected' => [
            'paynow_reference_submitted',
            'proof_of_payment_submitted'
        ],
        'registrar_raised_fix_request' => ['fix_applied_by_accreditation_officer'],
        'fix_applied_by_accreditation_officer' => ['approved_by_accreditation_officer'],
        'registrar_review_completed' => ['payment_verified'], // if payment already done
        'payment_verified' => ['in_production'],
        'in_production' => ['produced_ready_for_collection'],
        'produced_ready_for_collection' => ['collected'],
        'collected' => [], // Terminal state
        'rejected_final' => [] // Terminal state
    ];
    
    public function canTransition(Application $application, string $toStatus): bool
    {
        $fromStatus = $application->status;
        return in_array($toStatus, self::TRANSITIONS[$fromStatus] ?? []);
    }
    
    public function transition(Application $application, string $toStatus, User $actor, ?string $reason = null): bool
    {
        if (!$this->canTransition($application, $toStatus)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition from {$application->status} to {$toStatus}"
            );
        }
        
        // Validate transition requirements
        $this->validateTransitionRequirements($application, $toStatus, $actor);
        
        // Capture before state
        $beforeState = $this->captureState($application);
        
        // Update status
        $application->previous_status = $application->status;
        $application->status = $toStatus;
        $application->status_changed_at = now();
        $application->status_changed_by = $actor->id;
        $application->version++; // Optimistic locking
        
        // Update specific fields based on transition
        $this->updateTransitionFields($application, $toStatus, $actor);
        
        $application->save();
        
        // Capture after state
        $afterState = $this->captureState($application);
        
        // Log audit trail
        $this->logAudit($application, $actor, 'status_change', $beforeState, $afterState, $reason);
        
        // Trigger notifications
        $this->triggerNotifications($application, $toStatus);
        
        return true;
    }
    
    private function validateTransitionRequirements(Application $application, string $toStatus, User $actor): void
    {
        switch ($toStatus) {
            case 'approved_by_accreditation_officer':
                if (!$actor->hasRole('accreditation_officer')) {
                    throw new UnauthorizedException('Only Accreditation Officer can approve');
                }
                if (empty($application->category)) {
                    throw new ValidationException('Category must be assigned before approval');
                }
                break;
                
            case 'payment_verified':
                if (!$actor->hasRole('accounts_payments')) {
                    throw new UnauthorizedException('Only Accounts Officer can verify payment');
                }
                if (!in_array($application->status, ['paynow_reference_submitted', 'proof_of_payment_submitted'])) {
                    throw new ValidationException('Payment must be submitted before verification');
                }
                break;
                
            case 'in_production':
                if (!$actor->hasRole('accreditation_officer')) {
                    throw new UnauthorizedException('Only Accreditation Officer can start production');
                }
                if ($application->status !== 'payment_verified') {
                    throw new ValidationException('Payment must be verified before production');
                }
                break;
                
            case 'returned_to_applicant':
            case 'rejected_final':
                if (empty($reason)) {
                    throw new ValidationException('Reason is required for returns and rejections');
                }
                break;
        }
    }
}
```

## RBAC Implementation

### Middleware: CheckApplicationAccess
```php
class CheckApplicationAccess
{
    public function handle(Request $request, Closure $next, string $action)
    {
        $user = $request->user();
        $application = $request->route('application');
        
        $permissions = [
            'view' => [
                'applicant' => fn($app) => $app->applicant_user_id === $user->id,
                'accreditation_officer' => fn($app) => true,
                'registrar' => fn($app) => in_array($app->status, [
                    'approved_by_accreditation_officer',
                    'registrar_raised_fix_request',
                    'registrar_review_completed',
                    'payment_verified',
                    'in_production',
                    'produced_ready_for_collection'
                ]),
                'accounts_payments' => fn($app) => in_array($app->status, [
                    'paynow_reference_submitted',
                    'proof_of_payment_submitted',
                    'payment_verified',
                    'payment_rejected'
                ]),
                'auditor' => fn($app) => true,
                'director' => fn($app) => true,
            ],
            'approve' => [
                'accreditation_officer' => fn($app) => $app->status === 'submitted_to_accreditation_officer',
            ],
            'return' => [
                'accreditation_officer' => fn($app) => $app->status === 'submitted_to_accreditation_officer',
            ],
            'verify_payment' => [
                'accounts_payments' => fn($app) => in_array($app->status, [
                    'paynow_reference_submitted',
                    'proof_of_payment_submitted'
                ]),
            ],
            'start_production' => [
                'accreditation_officer' => fn($app) => $app->status === 'payment_verified',
            ],
            'raise_fix_request' => [
                'registrar' => fn($app) => $app->status === 'approved_by_accreditation_officer',
            ],
        ];
        
        foreach ($user->roles as $role) {
            $rolePermissions = $permissions[$action][$role->name] ?? null;
            if ($rolePermissions && $rolePermissions($application)) {
                return $next($request);
            }
        }
        
        abort(403, 'Unauthorized action');
    }
}
```

### Policy: ApplicationPolicy
```php
class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            'accreditation_officer',
            'registrar',
            'accounts_payments',
            'production',
            'auditor',
            'director'
        ]);
    }
    
    public function view(User $user, Application $application): bool
    {
        // Applicant can view own
        if ($application->applicant_user_id === $user->id) {
            return true;
        }
        
        // Staff roles based on status
        if ($user->hasRole('accreditation_officer')) {
            return true;
        }
        
        if ($user->hasRole('registrar')) {
            return in_array($application->status, [
                'approved_by_accreditation_officer',
                'registrar_raised_fix_request',
                'registrar_review_completed',
                'payment_verified',
                'in_production',
                'produced_ready_for_collection'
            ]);
        }
        
        if ($user->hasRole('accounts_payments')) {
            return in_array($application->status, [
                'paynow_reference_submitted',
                'proof_of_payment_submitted',
                'payment_verified',
                'payment_rejected'
            ]);
        }
        
        return $user->hasAnyRole(['auditor', 'director']);
    }
    
    public function approve(User $user, Application $application): bool
    {
        return $user->hasRole('accreditation_officer') 
            && $application->status === 'submitted_to_accreditation_officer';
    }
    
    public function verifyPayment(User $user, Application $application): bool
    {
        return $user->hasRole('accounts_payments')
            && in_array($application->status, [
                'paynow_reference_submitted',
                'proof_of_payment_submitted'
            ]);
    }
    
    public function startProduction(User $user, Application $application): bool
    {
        return $user->hasRole('accreditation_officer')
            && $application->status === 'payment_verified';
    }
    
    public function raiseFix Request(User $user, Application $application): bool
    {
        return $user->hasRole('registrar')
            && $application->status === 'approved_by_accreditation_officer';
    }
}
```

## Category Validation

### CategoryValidator Service
```php
class CategoryValidator
{
    private const MEDIA_PRACTITIONER_CATEGORIES = ['JE', 'JF', 'JO', 'JS', 'JM', 'JP', 'JD', 'JT'];
    private const MASS_MEDIA_CATEGORIES = ['MC', 'MA', 'MF', 'MN', 'DG', 'MP', 'MS', 'MV'];
    
    public function validate(string $category, string $applicationType): bool
    {
        if ($applicationType === 'accreditation') {
            return in_array($category, self::MEDIA_PRACTITIONER_CATEGORIES);
        }
        
        if ($applicationType === 'registration') {
            return in_array($category, self::MASS_MEDIA_CATEGORIES);
        }
        
        return false;
    }
    
    public function getCategories(string $applicationType): array
    {
        return $applicationType === 'accreditation'
            ? self::MEDIA_PRACTITIONER_CATEGORIES
            : self::MASS_MEDIA_CATEGORIES;
    }
}
```

## Audit Logging

### AuditLogger Service
```php
class AuditLogger
{
    public function log(
        Application $application,
        User $actor,
        string $actionType,
        ?array $beforeState = null,
        ?array $afterState = null,
        ?string $reason = null,
        ?string $notes = null
    ): AuditLog {
        return AuditLog::create([
            'application_id' => $application->id,
            'actor_role' => $this->determineActorRole($actor),
            'actor_user_id' => $actor->id,
            'actor_name' => $actor->name,
            'action_type' => $actionType,
            'before_state' => $beforeState,
            'after_state' => $afterState,
            'reason' => $reason,
            'notes' => $notes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    private function determineActorRole(User $user): string
    {
        if ($user->hasRole('accreditation_officer')) return 'accreditation_officer';
        if ($user->hasRole('registrar')) return 'registrar';
        if ($user->hasRole('accounts_payments')) return 'accounts';
        if ($user->hasRole('production')) return 'production';
        if ($user->hasRole('auditor')) return 'auditor';
        if ($user->hasRole('director')) return 'director';
        return 'applicant';
    }
}
```

## Production Number Generation

### AccreditationNumberGenerator Service
```php
class AccreditationNumberGenerator
{
    /**
     * Generate secure accreditation/registration number
     * Format: ZMC-[TYPE]-[YEAR]-[SEQUENCE]-[CHECKSUM]
     * Example: ZMC-ACC-2024-00123-A7
     */
    public function generate(Application $application): string
    {
        $type = $application->application_type === 'accreditation' ? 'ACC' : 'REG';
        $year = now()->year;
        
        // Get next sequence number for this year and type
        $sequence = $this->getNextSequence($type, $year);
        
        // Generate checksum
        $checksum = $this->generateChecksum($type, $year, $sequence);
        
        return sprintf('ZMC-%s-%d-%05d-%s', $type, $year, $sequence, $checksum);
    }
    
    private function getNextSequence(string $type, int $year): int
    {
        $prefix = "ZMC-{$type}-{$year}-";
        
        $lastNumber = Application::where('accreditation_number', 'like', "{$prefix}%")
            ->orderBy('accreditation_number', 'desc')
            ->value('accreditation_number');
        
        if (!$lastNumber) {
            return 1;
        }
        
        // Extract sequence from last number
        preg_match('/-(\d{5})-/', $lastNumber, $matches);
        return isset($matches[1]) ? (int)$matches[1] + 1 : 1;
    }
    
    private function generateChecksum(string $type, int $year, int $sequence): string
    {
        $data = "{$type}{$year}{$sequence}";
        $hash = hash('sha256', $data);
        return strtoupper(substr($hash, 0, 2));
    }
}
```

## QR Code Generation

### QRCodeGenerator Service
```php
class QRCodeGenerator
{
    public function generate(Application $application): string
    {
        $data = [
            'reference' => $application->reference,
            'number' => $application->accreditation_number,
            'type' => $application->application_type,
            'category' => $application->category,
            'issued_at' => $application->produced_at?->format('Y-m-d'),
            'verify_url' => route('public.verify', ['reference' => $application->reference]),
        ];
        
        $qrCode = QrCode::size(200)
            ->format('png')
            ->generate(json_encode($data));
        
        return base64_encode($qrCode);
    }
}
```

## Dashboard Query Builders

### AccreditationOfficerDashboard
```php
class AccreditationOfficerDashboard
{
    public function getNewSubmissions()
    {
        return Application::where('status', 'submitted_to_accreditation_officer')
            ->with(['applicant', 'documents'])
            ->orderBy('submitted_at', 'asc')
            ->paginate(20);
    }
    
    public function getReturnedItems()
    {
        return Application::where('status', 'returned_to_applicant')
            ->with(['applicant'])
            ->orderBy('returned_at', 'desc')
            ->paginate(20);
    }
    
    public function getFixRequests()
    {
        return Application::where('status', 'registrar_raised_fix_request')
            ->with(['applicant', 'fixRequests' => fn($q) => $q->where('status', 'pending')])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
    }
    
    public function getProductionQueue()
    {
        return Application::where('status', 'payment_verified')
            ->with(['applicant'])
            ->orderBy('payment_verified_at', 'asc')
            ->paginate(20);
    }
}
```

### RegistrarDashboard
```php
class RegistrarDashboard
{
    public function getPendingReview()
    {
        return Application::where('status', 'approved_by_accreditation_officer')
            ->whereDoesntHave('fixRequests', fn($q) => $q->where('status', 'pending'))
            ->with(['applicant', 'documents', 'accreditationOfficer'])
            ->orderBy('accreditation_officer_approved_at', 'asc')
            ->paginate(20);
    }
    
    public function getFixRequestsRaised()
    {
        return Application::where('status', 'registrar_raised_fix_request')
            ->with(['applicant', 'fixRequests' => fn($q) => $q->latest()])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
    }
}
```

### AccountsDashboard
```php
class AccountsDashboard
{
    public function getAwaitingVerification()
    {
        return Application::whereIn('status', [
                'paynow_reference_submitted',
                'proof_of_payment_submitted'
            ])
            ->with(['applicant', 'paymentSubmissions' => fn($q) => $q->latest()])
            ->orderBy('updated_at', 'asc')
            ->paginate(20);
    }
    
    public function getPaymentRejected()
    {
        return Application::where('status', 'payment_rejected')
            ->with(['applicant', 'paymentSubmissions'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
    }
}
```

## API Endpoints

### Routes Structure
```php
// Applicant Routes
Route::middleware(['auth', 'role:applicant'])->group(function () {
    Route::post('/applications', [ApplicationController::class, 'store']);
    Route::post('/applications/{application}/submit', [ApplicationController::class, 'submit']);
    Route::post('/applications/{application}/resubmit', [ApplicationController::class, 'resubmit']);
    Route::post('/applications/{application}/payment/paynow', [PaymentController::class, 'submitPaynowReference']);
    Route::post('/applications/{application}/payment/proof', [PaymentController::class, 'uploadProof']);
});

// Accreditation Officer Routes
Route::middleware(['auth', 'role:accreditation_officer'])->group(function () {
    Route::get('/staff/officer/queue/new', [AccreditationOfficerController::class, 'newSubmissions']);
    Route::post('/staff/officer/applications/{application}/approve', [AccreditationOfficerController::class, 'approve']);
    Route::post('/staff/officer/applications/{application}/return', [AccreditationOfficerController::class, 'return']);
    Route::post('/staff/officer/applications/{application}/assign-category', [AccreditationOfficerController::class, 'assignCategory']);
    Route::post('/staff/officer/applications/{application}/apply-fix', [AccreditationOfficerController::class, 'applyFix']);
    
    // Production
    Route::get('/staff/production/queue', [ProductionController::class, 'queue']);
    Route::post('/staff/production/applications/{application}/generate', [ProductionController::class, 'generate']);
    Route::post('/staff/production/applications/{application}/print', [ProductionController::class, 'print']);
});

// Registrar Routes
Route::middleware(['auth', 'role:registrar'])->group(function () {
    Route::get('/staff/registrar/queue/pending', [RegistrarController::class, 'pendingReview']);
    Route::post('/staff/registrar/applications/{application}/raise-fix', [RegistrarController::class, 'raiseFix']);
    Route::post('/staff/registrar/applications/{application}/complete-review', [RegistrarController::class, 'completeReview']);
});

// Accounts Routes
Route::middleware(['auth', 'role:accounts_payments'])->group(function () {
    Route::get('/staff/accounts/queue/pending', [AccountsController::class, 'awaitingVerification']);
    Route::post('/staff/accounts/applications/{application}/verify-payment', [AccountsController::class, 'verifyPayment']);
    Route::post('/staff/accounts/applications/{application}/reject-payment', [AccountsController::class, 'rejectPayment']);
});

// Audit Routes
Route::middleware(['auth', 'role:auditor,director'])->group(function () {
    Route::get('/staff/audit/logs', [AuditController::class, 'index']);
    Route::get('/staff/audit/applications/{application}/timeline', [AuditController::class, 'applicationTimeline']);
    Route::get('/staff/audit/export', [AuditController::class, 'export']);
});
```

## UI Components

### Sidebar Production Link (Accreditation Officer Only)
```blade
@if($user->hasRole('accreditation_officer'))
  <li class="menu-title">Production</li>
  <li class="{{ request()->routeIs('staff.production.*') ? 'active' : '' }}">
    <a href="{{ route('staff.production.queue') }}">
      <i class="ri-printer-line"></i> <span>Production Queue</span>
    </a>
  </li>
@endif
```

### Status Badge Component
```blade
@php
$statusColors = [
    'submitted_to_accreditation_officer' => 'primary',
    'returned_to_applicant' => 'warning',
    'approved_by_accreditation_officer' => 'success',
    'payment_verified' => 'success',
    'payment_rejected' => 'danger',
    'in_production' => 'info',
    'produced_ready_for_collection' => 'success',
    'rejected_final' => 'danger',
];
$color = $statusColors[$status] ?? 'secondary';
@endphp

<span class="badge bg-{{ $color }}">
    {{ str_replace('_', ' ', ucwords($status)) }}
</span>
```

## Testing Strategy

### Unit Tests
- Status transition validation
- Category validation
- Permission checks
- Number generation
- QR code generation

### Integration Tests
- Complete workflow from submission to collection
- Payment verification flows
- Fix request cycles
- Audit logging

### Feature Tests
- Dashboard queue filtering
- RBAC enforcement
- Concurrent access handling
- Edge cases (rejections, loops)

## Deployment Checklist

- [ ] Run migrations
- [ ] Seed official categories
- [ ] Configure PayNow integration
- [ ] Set up QR code library
- [ ] Configure notification channels
- [ ] Set up audit log retention policy
- [ ] Train staff on new workflow
- [ ] Create user documentation
- [ ] Set up monitoring and alerts
- [ ] Configure backup strategy
