<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'region',
        'account_type',
        'designation',
        'locale',
        'phone_country_code',
        'phone_number',
        'phone2',
        'id_number',
        'passport_number',
        'social_media',
        'theme',
        'profile_data',
        'account_status',
        'approved_at',
        'approved_by',
        'otp_code',
        'otp_expires_at',
        'activation_token',
        'activated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'activated_at' => 'datetime',
            'password' => 'hashed',
            'profile_data' => 'array',
            'social_media' => 'array',
        ];
    }

    /**
     * Get the role attribute - fallback to first Spatie role if role field is null
     */
    public function getRoleAttribute($value)
    {
        // If role field is set, return it
        if ($value) {
            return $value;
        }

        // Otherwise, return the first Spatie role
        if ($this->relationLoaded('roles') && $this->roles->isNotEmpty()) {
            return $this->roles->first()->name;
        }

        // Load roles if not loaded and return first
        if (method_exists($this, 'roles')) {
            $firstRole = $this->roles()->first();
            return $firstRole ? $firstRole->name : null;
        }

        return null;
    }

    /* ==========================
     * Relationships
     * ========================== */

    public function assignedRegions()
    {
        return $this->belongsToMany(Region::class, 'officer_regions', 'user_id', 'region_id');
    }

    public function officerRegions()
    {
        return $this->hasMany(\App\Models\OfficerRegion::class);
    }

    public function assignedApplications()
    {
        return $this->hasMany(\App\Models\Application::class, 'assigned_officer_id');
    }

    public function processedApplications()
    {
        return $this->hasMany(\App\Models\Application::class, 'last_action_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(\App\Models\ActivityLog::class, 'user_id');
    }
}
