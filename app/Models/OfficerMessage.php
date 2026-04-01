<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficerMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'sender_id',
        'receiver_id',
        'sender_type',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const SENDER_TYPE_REGISTRAR = 'registrar';
    public const SENDER_TYPE_OFFICER = 'officer';

    /**
     * Get the related application.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the sender type label.
     */
    public function getSenderTypeLabelAttribute(): string
    {
        return match($this->sender_type) {
            self::SENDER_TYPE_REGISTRAR => 'Registrar',
            self::SENDER_TYPE_OFFICER => 'Officer',
            default => 'Unknown',
        };
    }

    /**
     * Check if the message is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if the message is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): bool
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
            return true;
        }
        return false;
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read messages.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to only include messages from registrar.
     */
    public function scopeFromRegistrar($query)
    {
        return $query->where('sender_type', self::SENDER_TYPE_REGISTRAR);
    }

    /**
     * Scope a query to only include messages from officer.
     */
    public function scopeFromOfficer($query)
    {
        return $query->where('sender_type', self::SENDER_TYPE_OFFICER);
    }
}
