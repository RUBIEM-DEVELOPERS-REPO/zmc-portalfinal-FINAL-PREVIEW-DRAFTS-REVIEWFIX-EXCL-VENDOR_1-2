<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Payment extends Model
{
    protected $fillable = [
        'application_id',
        'payer_user_id',
        'media_house_id',
        'method',
        'source',
        'reference',
        'status',
        'poll_url',
        'amount',
        'currency',
        'raw',
        'confirmed_at',
        'bank_name',
        'deposit_slip_ref',
        'proof_file_path',
        'gateway_response',
        'last_checked_at',
        'parent_payment_id',
        'reversal_reason',
        'reconciled',
        'reconciled_at',
        'reconciled_by',
        'applicant_category',
        'residency',
        'service_type',
        'receipt_number',
        'payment_date',
        'voided_at',
        'voided_by',
        'void_reason',
        'recorded_by',
    ];

    protected $casts = [
        'raw' => 'array',
        'gateway_response' => 'array',
        'confirmed_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'reconciled_at' => 'datetime',
        'reconciled' => 'boolean',
        'payment_date' => 'date',
        'voided_at' => 'datetime',
    ];

    /**
     * Boot method to register model event listeners for cache invalidation.
     */
    protected static function booted(): void
    {
        // Invalidate director dashboard caches when payment is confirmed (status changes to 'paid')
        static::updated(function (Payment $payment) {
            if ($payment->isDirty('status') && $payment->status === 'paid') {
                Cache::forget('director.kpis.executive_overview');
                Cache::forget('director.charts.revenue_breakdown');
            }
        });
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PaymentAuditLog::class);
    }

    public function parentPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'parent_payment_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }
}
