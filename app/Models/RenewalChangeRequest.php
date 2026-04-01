<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RenewalChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'renewal_application_id',
        'field_name',
        'old_value',
        'new_value',
        'supporting_document_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the renewal application this change belongs to
     */
    public function renewalApplication(): BelongsTo
    {
        return $this->belongsTo(RenewalApplication::class);
    }

    /**
     * Get the user who reviewed this change
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the field label
     */
    public function getFieldLabel(): string
    {
        return ucwords(str_replace('_', ' ', $this->field_name));
    }

    /**
     * Get the status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if change is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if change is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if change is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Scope: Pending changes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Approved changes
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Rejected changes
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
