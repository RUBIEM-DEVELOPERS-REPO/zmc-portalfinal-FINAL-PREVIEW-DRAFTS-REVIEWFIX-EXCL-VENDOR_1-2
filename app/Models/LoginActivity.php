<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_name',
        'ip_address',
        'user_agent',
        'device_identifier',
        'operating_system',
        'browser_name',
        'browser_version',
        'login_at',
        'logout_at',
        'session_duration',
        'login_successful',
        'failure_reason',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'login_successful' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('login_successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('login_successful', false);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('login_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function calculateSessionDuration()
    {
        if ($this->logout_at) {
            $this->session_duration = $this->logout_at->diffInSeconds($this->login_at);
            $this->save();
        }
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->session_duration) {
            return 'Active';
        }

        $hours = floor($this->session_duration / 3600);
        $minutes = floor(($this->session_duration % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }
}
