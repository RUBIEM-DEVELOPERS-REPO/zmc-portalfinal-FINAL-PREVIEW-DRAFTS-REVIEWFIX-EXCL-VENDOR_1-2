<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RenewalApplication extends Model
{
    use HasFactory;

    // Status Constants
    public const RENEWAL_DRAFT = 'renewal_draft';
    public const RENEWAL_TYPE_SELECTED = 'renewal_type_selected';
    public const RENEWAL_NUMBER_ENTERED = 'renewal_number_entered';
    public const RENEWAL_RECORD_FOUND = 'renewal_record_found';
    public const RENEWAL_RECORD_NOT_FOUND = 'renewal_record_not_found';
    public const RENEWAL_CONFIRMED_NO_CHANGES = 'renewal_confirmed_no_changes';
    public const RENEWAL_CONFIRMED_WITH_CHANGES = 'renewal_confirmed_with_changes';
    public const RENEWAL_PAYMENT_INITIATED = 'renewal_payment_initiated';
    public const RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION = 'renewal_submitted_awaiting_accounts_verification';
    public const RENEWAL_PAYMENT_VERIFIED = 'renewal_payment_verified';
    public const RENEWAL_PAYMENT_REJECTED = 'renewal_payment_rejected';
    public const RENEWAL_IN_PRODUCTION = 'renewal_in_production';
    public const RENEWAL_PRODUCED_READY_FOR_COLLECTION = 'renewal_produced_ready_for_collection';
    public const RENEWAL_COLLECTED = 'renewal_collected';
    public const RENEWAL_CANCELLED = 'renewal_cancelled';

    protected $fillable = [
        'applicant_user_id',
        'renewal_type',
        'original_application_id',
        'original_number',
        'lookup_status',
        'looked_up_at',
        'has_changes',
        'change_requests',
        'confirmation_type',
        'confirmed_at',
        'payment_method',
        'payment_reference',
        'payment_amount',
        'payment_date',
        'payment_proof_path',
        'payment_metadata',
        'payment_submitted_at',
        'payment_verified_at',
        'payment_verified_by',
        'payment_rejection_reason',
        'status',
        'current_stage',
        'last_action_at',
        'last_action_by',
        'produced_at',
        'produced_by',
        'print_count',
        'collection_location',
        'collected_at',
    ];

    protected $casts = [
        'looked_up_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'has_changes' => 'boolean',
        'change_requests' => 'array',
        'payment_amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_metadata' => 'array',
        'payment_submitted_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'last_action_at' => 'datetime',
        'produced_at' => 'datetime',
        'collected_at' => 'datetime',
        'print_count' => 'integer',
    ];

    /**
     * Get the applicant user
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_user_id');
    }

    /**
     * Get the original application being renewed
     */
    public function originalApplication(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'original_application_id');
    }

    /**
     * Get the user who verified the payment
     */
    public function paymentVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    /**
     * Get the user who performed the last action
     */
    public function lastActionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_action_by');
    }

    /**
     * Get the user who produced the document
     */
    public function producer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'produced_by');
    }

    /**
     * Get all change requests for this renewal
     */
    public function changeRequests(): HasMany
    {
        return $this->hasMany(RenewalChangeRequest::class);
    }

    /**
     * Get pending change requests
     */
    public function pendingChangeRequests(): HasMany
    {
        return $this->hasMany(RenewalChangeRequest::class)->where('status', 'pending');
    }

    /**
     * Get the renewal type label
     */
    public function getRenewalTypeLabel(): string
    {
        return match($this->renewal_type) {
            'renewal' => 'Accreditation Card Renewal',
            'replacement' => 'Accreditation Card Replacement',
            // Legacy types (for backward compatibility)
            'accreditation' => 'Journalist/Media Practitioner Accreditation Renewal',
            'registration' => 'Media House Registration Renewal',
            'permission' => 'Permission Renewal',
            default => ucfirst($this->renewal_type) . ' Renewal',
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabel(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get the payment method label
     */
    public function getPaymentMethodLabel(): ?string
    {
        if (!$this->payment_method) {
            return null;
        }

        return match($this->payment_method) {
            'PAYNOW' => 'PayNow',
            'PROOF_UPLOAD' => 'Proof Upload',
            'WAIVER' => 'Waiver',
            default => $this->payment_method,
        };
    }

    /**
     * Check if renewal is awaiting payment
     */
    public function isAwaitingPayment(): bool
    {
        return in_array($this->status, [
            self::RENEWAL_CONFIRMED_NO_CHANGES,
            self::RENEWAL_CONFIRMED_WITH_CHANGES,
            self::RENEWAL_PAYMENT_INITIATED,
        ]);
    }

    /**
     * Check if renewal is awaiting accounts verification
     */
    public function isAwaitingAccountsVerification(): bool
    {
        return $this->status === self::RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION;
    }

    /**
     * Check if renewal is ready for production
     */
    public function isReadyForProduction(): bool
    {
        return $this->status === self::RENEWAL_PAYMENT_VERIFIED;
    }

    /**
     * Check if renewal is in production
     */
    public function isInProduction(): bool
    {
        return $this->status === self::RENEWAL_IN_PRODUCTION;
    }

    /**
     * Check if renewal is ready for collection
     */
    public function isReadyForCollection(): bool
    {
        return $this->status === self::RENEWAL_PRODUCED_READY_FOR_COLLECTION;
    }

    /**
     * Scope: Awaiting accounts verification
     */
    public function scopeAwaitingAccountsVerification($query)
    {
        return $query->where('status', self::RENEWAL_SUBMITTED_AWAITING_ACCOUNTS_VERIFICATION);
    }

    /**
     * Scope: Ready for production
     */
    public function scopeReadyForProduction($query)
    {
        return $query->where('status', self::RENEWAL_PAYMENT_VERIFIED);
    }

    /**
     * Scope: In production
     */
    public function scopeInProduction($query)
    {
        return $query->where('status', self::RENEWAL_IN_PRODUCTION);
    }

    /**
     * Scope: Ready for collection
     */
    public function scopeReadyForCollection($query)
    {
        return $query->where('status', self::RENEWAL_PRODUCED_READY_FOR_COLLECTION);
    }

    /**
     * Scope: By renewal type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('renewal_type', $type);
    }

    /**
     * Scope: By applicant
     */
    public function scopeForApplicant($query, int $userId)
    {
        return $query->where('applicant_user_id', $userId);
    }
}
