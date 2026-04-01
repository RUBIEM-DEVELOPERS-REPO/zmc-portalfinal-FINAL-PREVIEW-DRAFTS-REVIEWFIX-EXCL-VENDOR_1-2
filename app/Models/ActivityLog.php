<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_role',
        'action',
        'entity_type',
        'entity_id',
        'from_status',
        'to_status',
        'ip',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * High-risk actions that require cache invalidation
     */
    protected static array $highRiskActions = [
        'registrar_reassign_category',
        'manual_payment_override',
        'certificate_edit_after_approval',
        'application_reopened',
        'system_override',
        'excessive_reprint'
    ];

    /**
     * Boot method to register model event listeners for cache invalidation.
     */
    protected static function booted(): void
    {
        // Invalidate director compliance cache when high-risk actions are logged
        static::created(function (ActivityLog $log) {
            if (in_array($log->action, self::$highRiskActions)) {
                Cache::forget('director.compliance');
            }
        });
    }

    /**
     * Enables morph relations using:
     * activity_logs.entity_type + activity_logs.entity_id
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
