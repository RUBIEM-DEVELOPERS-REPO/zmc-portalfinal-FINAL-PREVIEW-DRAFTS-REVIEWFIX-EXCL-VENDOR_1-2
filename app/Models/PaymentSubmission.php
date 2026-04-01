<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'payment_stage',
        'method',
        'reference',
        'amount',
        'currency',
        'status',
        'submitted_at',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'proof_path',
        'proof_metadata',
        'waiver_path',
        'waiver_metadata',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'proof_metadata' => 'array',
        'waiver_metadata' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the application this payment belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who verified this payment
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope: Application fee payments only
     */
    public function scopeApplicationFee($query)
    {
        return $query->where('payment_stage', 'application_fee');
    }

    /**
     * Scope: Registration fee payments only
     */
    public function scopeRegistrationFee($query)
    {
        return $query->where('payment_stage', 'registration_fee');
    }

    /**
     * Scope: Pending payments (submitted but not verified)
     */
    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope: Verified payments
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope: Rejected payments
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if this is a PayNow payment
     */
    public function isPaynow(): bool
    {
        return $this->method === 'PAYNOW';
    }

    /**
     * Check if this is a proof upload
     */
    public function isProofUpload(): bool
    {
        return $this->method === 'PROOF_UPLOAD';
    }

    /**
     * Check if this is a waiver
     */
    public function isWaiver(): bool
    {
        return $this->method === 'WAIVER';
    }

    /**
     * Get payment stage label
     */
    public function getStageLabel(): string
    {
        return match($this->payment_stage) {
            'application_fee' => 'Application Fee',
            'registration_fee' => 'Registration Fee',
            default => ucwords(str_replace('_', ' ', $this->payment_stage)),
        };
    }

    /**
     * Get method label
     */
    public function getMethodLabel(): string
    {
        return match($this->method) {
            'PAYNOW' => 'PayNow',
            'PROOF_UPLOAD' => 'Proof Upload',
            'WAIVER' => 'Waiver',
            default => $this->method,
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'submitted' => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
