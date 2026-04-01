<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'receipt_number',
        'amount',
        'payment_date',
        'recorded_by',
        'verified_by',
        'verified_at',
        'status',
        'void_reason',
        'voided_by',
        'voided_at',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'verified_at' => 'datetime',
        'voided_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function voider()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeVoided($query)
    {
        return $query->where('status', 'voided');
    }

    // Helper methods
    public function verify($userId)
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Only pending cash payments can be verified.');
        }

        $this->update([
            'status' => 'verified',
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }

    public function void($userId, $reason)
    {
        if ($this->status === 'voided') {
            throw new \Exception('Cash payment is already voided.');
        }

        $this->update([
            'status' => 'voided',
            'void_reason' => $reason,
            'voided_by' => $userId,
            'voided_at' => now(),
        ]);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isVoided()
    {
        return $this->status === 'voided';
    }
}
