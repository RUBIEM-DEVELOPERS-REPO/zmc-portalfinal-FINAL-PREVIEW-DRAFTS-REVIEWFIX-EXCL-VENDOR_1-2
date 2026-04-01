<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'application_id',
        'applicant_id',
        'payment_reference',
        'payment_method',
        'transaction_id',
        'amount',
        'payment_date',
        'status',
        'verified_by',
        'verified_at',
        'notes',
        'qr_code_data',
        'generated_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_PAYNOW = 'paynow';
    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_POP = 'pop'; // Proof of Payment
    public const PAYMENT_METHOD_WAIVER = 'waiver';
    public const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    public const PAYMENT_METHOD_ECOCASH = 'ecocash';
    public const PAYMENT_METHOD_ONE_MONEY = 'one_money';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = 'ZMC-REC-' . date('Y') . '-' . str_pad(static::getNextReceiptNumber(), 6, '0', STR_PAD_LEFT);
            }
            
            if (empty($receipt->payment_reference)) {
                $receipt->payment_reference = 'PAY-' . date('Y') . '-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the next receipt number.
     */
    public static function getNextReceiptNumber(): int
    {
        $lastReceipt = static::orderBy('id', 'desc')->first();
        return $lastReceipt ? $lastReceipt->id + 1 : 1;
    }

    /**
     * Get the related application.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the related applicant.
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * Get the user who verified the receipt.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the user who generated the receipt.
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Verification',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_VERIFIED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            self::PAYMENT_METHOD_PAYNOW => 'PayNow',
            self::PAYMENT_METHOD_CASH => 'Cash',
            self::PAYMENT_METHOD_POP => 'Proof of Payment',
            self::PAYMENT_METHOD_WAIVER => 'Waiver',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::PAYMENT_METHOD_ECOCASH => 'EcoCash',
            self::PAYMENT_METHOD_ONE_MONEY => 'OneMoney',
            default => 'Other',
        };
    }

    /**
     * Check if the receipt is verified.
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Check if the receipt is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the receipt is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Mark receipt as verified.
     */
    public function markAsVerified(int $verifiedBy): bool
    {
        return $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
        ]);
    }

    /**
     * Cancel receipt.
     */
    public function cancel(): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Generate QR code data.
     */
    public function generateQRCodeData(): string
    {
        $data = [
            'receipt_number' => $this->receipt_number,
            'payment_reference' => $this->payment_reference,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date->format('Y-m-d H:i:s'),
            'payment_method' => $this->payment_method,
            'application_reference' => $this->application?->reference,
            'applicant_name' => $this->applicant?->name,
            'verified' => $this->isVerified(),
            'verification_url' => url('/verify-payment/' . $this->payment_reference),
        ];

        return json_encode($data);
    }

    /**
     * Get verification URL.
     */
    public function getVerificationUrlAttribute(): string
    {
        return url('/verify-payment/' . $this->payment_reference);
    }

    /**
     * Scope a query to only include verified receipts.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    /**
     * Scope a query to only include pending receipts.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include cancelled receipts.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to only include receipts for a specific payment method.
     */
    public function scopePaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope a query to only include receipts within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Get all available payment methods.
     */
    public static function getPaymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_PAYNOW => 'PayNow',
            self::PAYMENT_METHOD_CASH => 'Cash',
            self::PAYMENT_METHOD_POP => 'Proof of Payment',
            self::PAYMENT_METHOD_WAIVER => 'Waiver',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::PAYMENT_METHOD_ECOCASH => 'EcoCash',
            self::PAYMENT_METHOD_ONE_MONEY => 'OneMoney',
        ];
    }
}
