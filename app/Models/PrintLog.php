<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_type',
        'record_id',
        'template_id',
        'printed_by',
        'printer_name',
        'print_count',
        'printed_at',
        'notes',
        // Legacy fields for backward compatibility
        'application_id',
        'document_type',
        'reason',
        'reprint_reason',
        'workstation',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
        'print_count' => 'integer',
    ];

    // Relationships
    public function template(): BelongsTo
    {
        return $this->belongsTo(DesignTemplate::class, 'template_id');
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    // Legacy relationship
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function printedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    // Scopes
    public function scopeAccreditation($query)
    {
        return $query->where('record_type', 'accreditation');
    }

    public function scopeRegistration($query)
    {
        return $query->where('record_type', 'registration');
    }

    public function scopeByPrinter($query, $userId)
    {
        return $query->where('printed_by', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('printed_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function isAccreditation()
    {
        return $this->record_type === 'accreditation';
    }

    public function isRegistration()
    {
        return $this->record_type === 'registration';
    }
}
