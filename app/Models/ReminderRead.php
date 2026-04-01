<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_id',
        'user_id',
        'read_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    // Relationships
    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function acknowledge()
    {
        $this->update([
            'read_at' => $this->read_at ?? now(),
            'acknowledged_at' => now(),
        ]);
    }
}
