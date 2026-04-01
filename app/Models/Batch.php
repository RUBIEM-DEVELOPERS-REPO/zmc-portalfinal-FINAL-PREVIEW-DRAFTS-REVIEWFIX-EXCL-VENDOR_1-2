<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'reference',
        'media_house_user_id',
        'amount',
        'status',
        'payment_method',
        'proof_path',
        'metadata', // JSON: e.g. { selected_journalist_ids: [...] }
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * The Media House (User) that created this batch.
     */
    public function mediaHouse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'media_house_user_id');
    }

    /**
     * Applications included in this batch.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
