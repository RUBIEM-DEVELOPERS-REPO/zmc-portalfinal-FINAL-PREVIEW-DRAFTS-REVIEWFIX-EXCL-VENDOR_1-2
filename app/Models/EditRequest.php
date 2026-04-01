<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_type',
        'record_id',
        'requested_by',
        'approved_by',
        'edit_reason',
        'old_data',
        'new_data',
        'status',
        'approved_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'old_data' => 'json',
        'new_data' => 'json',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const RECORD_TYPES = [
        'accreditation' => 'Accredited Journalist',
        'registration' => 'Registered Media House',
    ];

    /**
     * Get the user who requested the edit.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved/rejected the edit.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the related accreditation record if applicable.
     */
    public function accreditationRecord()
    {
        return $this->belongsTo(AccreditationRecord::class, 'record_id')
                    ->where('record_type', 'accreditation');
    }

    /**
     * Get the related registration record if applicable.
     */
    public function registrationRecord()
    {
        return $this->belongsTo(RegistrationRecord::class, 'record_id')
                    ->where('record_type', 'registration');
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
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
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the record type label.
     */
    public function getRecordTypeLabelAttribute(): string
    {
        return self::RECORD_TYPES[$this->record_type] ?? 'Unknown';
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get the changes summary.
     */
    public function getChangesSummaryAttribute(): array
    {
        $changes = [];
        $oldData = $this->old_data ?? [];
        $newData = $this->new_data ?? [];

        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[] = [
                    'field' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Apply the approved changes to the record.
     */
    public function applyChanges(): bool
    {
        if (!$this->isApproved()) {
            return false;
        }

        try {
            if ($this->record_type === 'accreditation') {
                $record = $this->accreditationRecord;
                if ($record && $record->application) {
                    $mergedData = array_merge($record->application->form_data ?? [], $this->new_data);
                    $record->application->form_data = $mergedData;
                    $record->application->save();
                }
            } elseif ($this->record_type === 'registration') {
                $record = $this->registrationRecord;
                if ($record && $record->application) {
                    $mergedData = array_merge($record->application->form_data ?? [], $this->new_data);
                    $record->application->form_data = $mergedData;
                    $record->application->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to apply edit request changes: ' . $e->getMessage(), [
                'edit_request_id' => $this->id,
                'record_type' => $this->record_type,
                'record_id' => $this->record_id,
            ]);

            return false;
        }
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include requests for a specific record type.
     */
    public function scopeForRecordType($query, string $recordType)
    {
        return $query->where('record_type', $recordType);
    }
}
