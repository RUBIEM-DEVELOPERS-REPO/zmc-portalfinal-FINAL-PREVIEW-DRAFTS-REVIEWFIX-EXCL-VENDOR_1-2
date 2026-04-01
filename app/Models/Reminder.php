<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'target_type',
        'target_id',
        'bulk_criteria',
        'priority',
        'reminder_type',
        'title',
        'message',
        'related_application_id',
        'link_url',
        'expires_at',
    ];

    protected $casts = [
        'bulk_criteria' => 'array',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function relatedApplication()
    {
        return $this->belongsTo(Application::class, 'related_application_id');
    }

    public function reads()
    {
        return $this->hasMany(ReminderRead::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where(function($subQ) use ($userId) {
                $subQ->where('target_type', 'media_practitioner')
                     ->where('target_id', $userId);
            })
            ->orWhere(function($subQ) use ($userId) {
                $subQ->where('target_type', 'media_house')
                     ->where('target_id', $userId);
            })
            ->orWhere('target_type', 'bulk');
        });
    }

    // Helper methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isHighPriority()
    {
        return $this->priority === 'high';
    }

    public function hasBeenReadBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->whereNotNull('read_at')->exists();
    }

    public function hasBeenAcknowledgedBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->whereNotNull('acknowledged_at')->exists();
    }
}
