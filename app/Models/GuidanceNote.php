<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuidanceNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'registrar_id',
        'officer_id',
        'note',
        'status',
        'responded_at',
        'response_note',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_SENT = 'sent';
    public const STATUS_RESPONDED = 'responded';

    /**
     * Get the related application.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the registrar who sent the guidance.
     */
    public function registrar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrar_id');
    }

    /**
     * Get the officer who received the guidance.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SENT => 'Sent',
            self::STATUS_RESPONDED => 'Responded',
            default => 'Unknown',
        };
    }

    /**
     * Check if the guidance is sent.
     */
    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Check if the guidance is responded.
     */
    public function isResponded(): bool
    {
        return $this->status === self::STATUS_RESPONDED;
    }

    /**
     * Scope a query to only include sent guidance.
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope a query to only include responded guidance.
     */
    public function scopeResponded($query)
    {
        return $query->where('status', self::STATUS_RESPONDED);
    }
}
