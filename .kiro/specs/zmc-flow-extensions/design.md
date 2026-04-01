# ZMC Flow Extensions - Technical Design Document

## Document Information
**Version**: 1.0  
**Date**: 2026-02-25  
**Project**: ZMC Flow Extensions - Waivers + Registrar Oversight + Media House Two-Stage Payments  
**Status**: Technical Design

---

## Architecture Overview

### System Components
1. **Database Layer**: New tables and fields for payment submissions, official letters
2. **Model Layer**: PaymentSubmission, OfficialLetter models with relationships
3. **Service Layer**: Enhanced ApplicationWorkflow with new status transitions
4. **Controller Layer**: Enhanced Officer, Registrar, Accounts controllers
5. **View Layer**: New UI components for two-stage payments, waiver handling, oversight

### Design Principles
- **Backward Compatibility**: No breaking changes to existing workflows
- **Server-Side Enforcement**: All status transitions validated at service layer
- **RBAC Enforcement**: Role-based access control at controller/middleware level
- **Complete Audit Trail**: Every action logged with full context
- **Data Integrity**: Foreign keys, constraints, and validations

---

## Database Design

### New Status Constants (Application Model)

```php
// Waiver/Special Path
public const FORWARDED_TO_REGISTRAR_NO_APPROVAL = 'forwarded_to_registrar_no_approval';
public const PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR = 'pending_accounts_review_from_registrar';

// Media House Two-Stage Path
public const SUBMITTED_WITH_APP_FEE = 'submitted_with_app_fee';
public const VERIFIED_BY_OFFICER_PENDING_REGISTRAR = 'verified_by_officer_pending_registrar';
public const REGISTRAR_APPROVED_PENDING_REG_FEE = 'registrar_approved_pending_reg_fee';
public const REG_FEE_SUBMITTED_AWAITING_VERIFICATION = 'reg_fee_submitted_awaiting_verification';
```

### Migration 1: payment_submissions Table

```php
Schema::create('payment_submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('application_id')->constrained()->cascadeOnDelete();
    
    // Payment stage: 'application_fee' | 'registration_fee'
    $table->enum('payment_stage', ['application_fee', 'registration_fee']);
    
    // Method: 'PAYNOW' | 'PROOF_UPLOAD' | 'WAIVER'
    $table->enum('method', ['PAYNOW', 'PROOF_UPLOAD', 'WAIVER']);
    
    // Reference/tracking
    $table->string('reference')->nullable(); // PayNow reference or receipt number
    $table->decimal('amount', 10, 2)->nullable();
    $table->string('currency', 3)->default('USD');
    
    // Status: 'submitted' | 'verified' | 'rejected'
    $table->enum('status', ['submitted', 'verified', 'rejected'])->default('submitted');
    
    // Timestamps
    $table->timestamp('submitted_at')->nullable();
    $table->timestamp('verified_at')->nullable();
    $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
    
    // Rejection
    $table->text('rejection_reason')->nullable();
    
    // File paths
    $table->string('proof_path')->nullable(); // For proof uploads
    $table->json('proof_metadata')->nullable(); // payer_name, date_paid, etc.
    $table->string('waiver_path')->nullable(); // For waivers
    $table->json('waiver_metadata')->nullable(); // beneficiary, offered_by, etc.
    
    $table->timestamps();
    
    // Indexes
    $table->index(['application_id', 'payment_stage']);
    $table->index(['status', 'payment_stage']);
    $table->index('submitted_at');
});
```

### Migration 2: official_letters Table

```php
Schema::create('official_letters', function (Blueprint $table) {
    $table->id();
    $table->foreignId('application_id')->constrained()->cascadeOnDelete();
    $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
    
    // File details
    $table->string('file_path');
    $table->string('file_name');
    $table->unsignedBigInteger('file_size');
    $table->string('file_hash', 64); // SHA256
    
    // Timestamps
    $table->timestamp('uploaded_at');
    $table->timestamps();
    
    // Indexes
    $table->index('application_id');
    $table->index('uploaded_by');
});
```

### Migration 3: Add Fields to applications Table

```php
Schema::table('applications', function (Blueprint $table) {
    // Forward without approval
    $table->text('forward_no_approval_reason')->nullable()->after('decision_notes');
    
    // Official letter reference (for quick access)
    $table->foreignId('official_letter_id')->nullable()->constrained('official_letters')->nullOnDelete();
    
    // Payment stage tracking
    $table->enum('current_payment_stage', ['none', 'application_fee', 'registration_fee'])->default('none');
});
```

---

## Model Design

### PaymentSubmission Model

```php
class PaymentSubmission extends Model
{
    protected $fillable = [
        'application_id', 'payment_stage', 'method', 'reference',
        'amount', 'currency', 'status', 'submitted_at', 'verified_at',
        'verified_by', 'rejection_reason', 'proof_path', 'proof_metadata',
        'waiver_path', 'waiver_metadata'
    ];
    
    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'proof_metadata' => 'array',
        'waiver_metadata' => 'array',
        'amount' => 'decimal:2',
    ];
    
    // Relationships
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    
    // Scopes
    public function scopeApplicationFee($query)
    {
        return $query->where('payment_stage', 'application_fee');
    }
    
    public function scopeRegistrationFee($query)
    {
        return $query->where('payment_stage', 'registration_fee');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }
}
```

### OfficialLetter Model

```php
class OfficialLetter extends Model
{
    protected $fillable = [
        'application_id', 'uploaded_by', 'file_path',
        'file_name', 'file_size', 'file_hash', 'uploaded_at'
    ];
    
    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];
    
    // Relationships
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    // Helpers
    public function getDownloadUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
```

### Application Model Updates

```php
// Add to Application model

// Relationships
public function paymentSubmissions(): HasMany
{
    return $this->hasMany(PaymentSubmission::class);
}

public function applicationFeePayment(): HasOne
{
    return $this->hasOne(PaymentSubmission::class)
        ->where('payment_stage', 'application_fee');
}

public function registrationFeePayment(): HasOne
{
    return $this->hasOne(PaymentSubmission::class)
        ->where('payment_stage', 'registration_fee');
}

public function officialLetter(): HasOne
{
    return $this->hasOne(OfficialLetter::class);
}

// Helper methods
public function requiresApplicationFee(): bool
{
    return $this->application_type === 'registration';
}

public function hasApplicationFeePaid(): bool
{
    return $this->applicationFeePayment()
        ->where('status', 'verified')
        ->exists();
}

public function hasRegistrationFeePaid(): bool
{
    return $this->registrationFeePayment()
        ->where('status', 'verified')
        ->exists();
}
```

---

## Service Layer Design

### ApplicationWorkflow Updates

```php
// Add to allowed() method transitions

Application::OFFICER_REVIEW => [
    Application::OFFICER_APPROVED,
    Application::OFFICER_REJECTED,
    Application::CORRECTION_REQUESTED,
    Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL, // NEW
],

Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL => [
    Application::REGISTRAR_REJECTED,
    Application::RETURNED_TO_OFFICER,
    Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR, // NEW
],

Application::SUBMITTED_WITH_APP_FEE => [
    Application::RETURNED_TO_APPLICANT,
    Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR, // NEW
],

Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR => [
    Application::RETURNED_TO_OFFICER, // via fix request
    Application::REGISTRAR_APPROVED_PENDING_REG_FEE, // NEW
],

Application::REGISTRAR_APPROVED_PENDING_REG_FEE => [
    Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION, // NEW
],

Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION => [
    Application::PAYMENT_VERIFIED,
    Application::PAYMENT_REJECTED,
],

Application::PENDING_ACCOUNTS_REVIEW_FROM_REGISTRAR => [
    Application::PAYMENT_VERIFIED,
    Application::PAYMENT_REJECTED,
],
```

---

## Controller Design

### AccreditationOfficerController Enhancements

**New Method: forwardWithoutApproval()**
```php
public function forwardWithoutApproval(Request $request, Application $application)
{
    $data = $request->validate([
        'reason' => ['required', 'string', 'max:5000'],
    ]);
    
    $from = $application->status;
    
    // Save reason
    $application->forward_no_approval_reason = $data['reason'];
    $application->save();
    
    // Transition
    ApplicationWorkflow::transition(
        $application,
        Application::FORWARDED_TO_REGISTRAR_NO_APPROVAL,
        'officer_forward_without_approval',
        ['reason' => $data['reason']]
    );
    
    // Audit
    ActivityLogger::log('officer_forward_without_approval', $application, $from, $application->status, [
        'reason' => $data['reason'],
    ]);
    
    return back()->with('success', 'Application forwarded to Registrar without approval.');
}
```

### RegistrarController Enhancements

**New Method: approveWithOfficialLetter()**
```php
public function approveWithOfficialLetter(Request $request, Application $application)
{
    $data = $request->validate([
        'official_letter' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        'decision_notes' => ['nullable', 'string', 'max:5000'],
    ]);
    
    // Validate: Media house only
    if ($application->application_type !== 'registration') {
        return back()->with('error', 'Official letter is only required for media house registrations.');
    }
    
    $from = $application->status;
    
    DB::transaction(function() use ($application, $data, $from) {
        // Upload official letter
        $file = $data['official_letter'];
        $path = $file->store('official_letters', 'public');
        $hash = hash_file('sha256', Storage::disk('public')->path($path));
        
        $officialLetter = OfficialLetter::create([
            'application_id' => $application->id,
            'uploaded_by' => Auth::id(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_hash' => $hash,
            'uploaded_at' => now(),
        ]);
        
        // Link to application
        $application->official_letter_id = $officialLetter->id;
        if (!empty($data['decision_notes'])) {
            $application->decision_notes = $data['decision_notes'];
        }
        $application->save();
        
        // Transition to pending registration fee
        ApplicationWorkflow::transition(
            $application,
            Application::REGISTRAR_APPROVED_PENDING_REG_FEE,
            'registrar_approve_with_official_letter',
            ['official_letter_id' => $officialLetter->id]
        );
        
        // Audit
        ActivityLogger::log('registrar_upload_official_letter', $application, $from, $application->status, [
            'official_letter_id' => $officialLetter->id,
            'file_name' => $file->getClientOriginalName(),
            'file_hash' => $hash,
        ]);
    });
    
    return back()->with('success', 'Application approved. Official letter uploaded. Applicant will be prompted to pay registration fee.');
}

**New Method: paymentOversight()**
```php
public function paymentOversight(Request $request)
{
    // READ-ONLY payment oversight
    $query = PaymentSubmission::query()
        ->with(['application.applicant', 'verifier'])
        ->latest('submitted_at');
    
    // Filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('method')) {
        $query->where('method', $request->method);
    }
    if ($request->filled('date_from')) {
        $query->whereDate('submitted_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('submitted_at', '<=', $request->date_to);
    }
    
    $payments = $query->paginate(20)->withQueryString();
    
    // KPIs
    $kpis = [
        'pending' => PaymentSubmission::where('status', 'submitted')->count(),
        'verified' => PaymentSubmission::where('status', 'verified')->count(),
        'rejected' => PaymentSubmission::where('status', 'rejected')->count(),
        'paynow' => PaymentSubmission::where('method', 'PAYNOW')->count(),
        'proof' => PaymentSubmission::where('method', 'PROOF_UPLOAD')->count(),
        'waiver' => PaymentSubmission::where('method', 'WAIVER')->count(),
    ];
    
    return view('staff.registrar.payment_oversight', compact('payments', 'kpis'));
}
```

### AccountsPaymentsController Enhancements

**New Method: verifyPaymentSubmission()**
```php
public function verifyPaymentSubmission(Request $request, PaymentSubmission $paymentSubmission)
{
    $data = $request->validate([
        'action' => ['required', 'in:verify,reject'],
        'notes' => ['nullable', 'string', 'max:5000'],
    ]);
    
    $application = $paymentSubmission->application;
    $from = $application->status;
    
    DB::transaction(function() use ($paymentSubmission, $application, $data, $from) {
        if ($data['action'] === 'verify') {
            // Verify payment
            $paymentSubmission->update([
                'status' => 'verified',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);
            
            // Check if all required payments are verified
            if ($application->requiresApplicationFee()) {
                // Media house: need both fees
                $appFeeVerified = $application->hasApplicationFeePaid();
                $regFeeVerified = $application->hasRegistrationFeePaid();
                
                if ($appFeeVerified && $regFeeVerified) {
                    // Both verified -> PAYMENT_VERIFIED
                    ApplicationWorkflow::transition(
                        $application,
                        Application::PAYMENT_VERIFIED,
                        'accounts_verify_both_payments'
                    );
                }
            } else {
                // Single payment -> PAYMENT_VERIFIED
                ApplicationWorkflow::transition(
                    $application,
                    Application::PAYMENT_VERIFIED,
                    'accounts_verify_payment'
                );
            }
            
            ActivityLogger::log('accounts_verify_payment', $application, $from, $application->status, [
                'payment_submission_id' => $paymentSubmission->id,
                'payment_stage' => $paymentSubmission->payment_stage,
            ]);
            
        } else {
            // Reject payment
            $paymentSubmission->update([
                'status' => 'rejected',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
                'rejection_reason' => $data['notes'],
            ]);
            
            ApplicationWorkflow::transition(
                $application,
                Application::PAYMENT_REJECTED,
                'accounts_reject_payment',
                ['reason' => $data['notes']]
            );
            
            ActivityLogger::log('accounts_reject_payment', $application, $from, $application->status, [
                'payment_submission_id' => $paymentSubmission->id,
                'payment_stage' => $paymentSubmission->payment_stage,
                'reason' => $data['notes'],
            ]);
        }
    });
    
    return back()->with('success', $data['action'] === 'verify' ? 'Payment verified.' : 'Payment rejected.');
}
```

---

## UI Design

### Officer Dashboard - New Action Button

```blade
<!-- In show.blade.php -->
@if($application->status === Application::OFFICER_REVIEW)
    <div class="flex gap-2">
        <!-- Standard approve -->
        <button onclick="showApproveModal()" class="btn-primary">
            Approve & Route
        </button>
        
        <!-- Forward without approval -->
        <button onclick="showForwardNoApprovalModal()" class="btn-warning">
            Forward to Registrar (No Approval)
        </button>
    </div>
@endif

<!-- Modal for forward without approval -->
<div id="forwardNoApprovalModal" class="modal hidden">
    <form method="POST" action="{{ route('staff.officer.forward-no-approval', $application) }}">
        @csrf
        <label>Reason (Required)</label>
        <select name="reason_type">
            <option value="waiver_submitted">Waiver Submitted</option>
            <option value="special_case">Special Case</option>
            <option value="other">Other</option>
        </select>
        <textarea name="reason" required></textarea>
        <button type="submit">Forward</button>
    </form>
</div>
```

### Registrar Dashboard - Official Letter Upload

```blade
<!-- In show.blade.php for media house applications -->
@if($application->application_type === 'registration' && 
    $application->status === Application::VERIFIED_BY_OFFICER_PENDING_REGISTRAR)
    
    <form method="POST" action="{{ route('staff.registrar.approve-with-letter', $application) }}" 
          enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label>Official Letter (Required) *</label>
            <input type="file" name="official_letter" accept=".pdf,.jpg,.jpeg,.png" required>
            <small>Upload the official approval letter for this media house registration.</small>
        </div>
        
        <div class="form-group">
            <label>Decision Notes (Optional)</label>
            <textarea name="decision_notes"></textarea>
        </div>
        
        <button type="submit" class="btn-primary">
            Approve & Upload Letter
        </button>
    </form>
@endif
```

### Registrar - Payment Oversight Page

```blade
<!-- resources/views/staff/registrar/payment_oversight.blade.php -->
<div class="dashboard">
    <h1>Payments Oversight (Read-Only)</h1>
    
    <!-- KPIs -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <span class="kpi-value">{{ $kpis['pending'] }}</span>
            <span class="kpi-label">Pending</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value">{{ $kpis['verified'] }}</span>
            <span class="kpi-label">Verified</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value">{{ $kpis['rejected'] }}</span>
            <span class="kpi-label">Rejected</span>
        </div>
    </div>
    
    <!-- Filters -->
    <form method="GET" class="filters">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="submitted">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
        </select>
        <select name="method">
            <option value="">All Methods</option>
            <option value="PAYNOW">PayNow</option>
            <option value="PROOF_UPLOAD">Proof Upload</option>
            <option value="WAIVER">Waiver</option>
        </select>
        <button type="submit">Filter</button>
    </form>
    
    <!-- Payment list (read-only) -->
    <table>
        <thead>
            <tr>
                <th>Application</th>
                <th>Stage</th>
                <th>Method</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Verified By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->application->reference }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $payment->payment_stage)) }}</td>
                <td>{{ $payment->method }}</td>
                <td>
                    <span class="badge badge-{{ $payment->status }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td>{{ $payment->submitted_at->format('Y-m-d H:i') }}</td>
                <td>{{ $payment->verifier->name ?? '—' }}</td>
                <td>
                    <a href="{{ route('staff.registrar.payment-detail', $payment) }}">
                        View Details
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

### Applicant Portal - Two-Stage Payment

```blade
<!-- After Registrar approval with official letter -->
@if($application->status === Application::REGISTRAR_APPROVED_PENDING_REG_FEE)
    <div class="alert alert-info">
        <h3>Registration Fee Payment Required</h3>
        <p>Your application has been approved by the Registrar. Please download your official letter and proceed to pay the registration fee.</p>
        
        <a href="{{ route('portal.download-official-letter', $application) }}" class="btn-primary">
            Download Official Letter
        </a>
        
        <button onclick="showRegistrationFeePaymentModal()" class="btn-success">
            Pay Registration Fee
        </button>
    </div>
@endif

<!-- Registration fee payment modal -->
<div id="regFeePaymentModal" class="modal hidden">
    <h3>Registration Fee Payment</h3>
    <div class="payment-options">
        <button onclick="payWithPaynow('registration_fee')">Pay with PayNow</button>
        <button onclick="showProofUpload('registration_fee')">Upload Proof of Payment</button>
    </div>
</div>
```

---

## Security & Validation

### Server-Side Validations

1. **Official Letter Upload**: Only Registrar can upload, only for media house applications
2. **Forward Without Approval**: Only Officer can use, requires mandatory reason
3. **Payment Oversight**: Registrar has READ-ONLY access (no write operations)
4. **Two-Stage Payment**: Media house applications CANNOT be submitted without application fee
5. **Status Transitions**: All transitions validated in ApplicationWorkflow::allowed()

### RBAC Enforcement

```php
// In routes/web.php
Route::middleware(['auth', 'role:accreditation_officer'])->group(function() {
    Route::post('/staff/officer/{application}/forward-no-approval', [AccreditationOfficerController::class, 'forwardWithoutApproval'])
        ->name('staff.officer.forward-no-approval');
});

Route::middleware(['auth', 'role:registrar'])->group(function() {
    Route::post('/staff/registrar/{application}/approve-with-letter', [RegistrarController::class, 'approveWithOfficialLetter'])
        ->name('staff.registrar.approve-with-letter');
    
    Route::get('/staff/registrar/payment-oversight', [RegistrarController::class, 'paymentOversight'])
        ->name('staff.registrar.payment-oversight');
});

Route::middleware(['auth', 'role:accounts_payments'])->group(function() {
    Route::post('/staff/accounts/payment-submission/{paymentSubmission}/verify', [AccountsPaymentsController::class, 'verifyPaymentSubmission'])
        ->name('staff.accounts.verify-payment-submission');
});
```

---

## Testing Strategy

### Unit Tests
- PaymentSubmission model relationships
- OfficialLetter model file handling
- ApplicationWorkflow status transitions
- Application helper methods (requiresApplicationFee, hasApplicationFeePaid, etc.)

### Integration Tests
- Officer forward without approval workflow
- Registrar approve with official letter workflow
- Two-stage payment submission workflow
- Accounts verification of both payment stages
- Registrar read-only payment oversight

### Edge Cases
- Duplicate payment submissions
- Missing official letter validation
- Payment rejection and resubmission
- Waiver acceptance/rejection flow
- Fix request loop limits

---

## Deployment Checklist

1. **Database Migrations**
   - Run payment_submissions migration
   - Run official_letters migration
   - Run applications table updates migration
   - Verify indexes created

2. **Code Deployment**
   - Deploy models (PaymentSubmission, OfficialLetter)
   - Deploy updated Application model
   - Deploy ApplicationWorkflow updates
   - Deploy controller enhancements
   - Deploy views

3. **Configuration**
   - Update .env if needed
   - Clear cache: `php artisan cache:clear`
   - Clear config: `php artisan config:clear`
   - Clear views: `php artisan view:clear`

4. **Verification**
   - Test officer forward without approval
   - Test registrar official letter upload
   - Test two-stage payment flow
   - Test registrar payment oversight (read-only)
   - Verify audit logs

---

**Document Version**: 1.0  
**Status**: Ready for Implementation  
**Next Step**: Create task breakdown and begin Phase 1
