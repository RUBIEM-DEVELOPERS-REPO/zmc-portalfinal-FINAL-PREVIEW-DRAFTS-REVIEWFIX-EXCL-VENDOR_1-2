<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalIntake extends Model
{
    use HasFactory;

    protected $fillable = [
        'accreditation_number',
        'registration_number',
        'intake_type',
        'applicant_name',
        'receipt_number',
        'processed_by',
        'confirmed_at',
        'application_id',
        'production_record_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    // Relationships
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeInProduction($query)
    {
        return $query->where('status', 'in_production');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAccreditation($query)
    {
        return $query->where('intake_type', 'accreditation');
    }

    public function scopeRegistration($query)
    {
        return $query->where('intake_type', 'registration');
    }

    // Helper methods
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function moveToProduction($productionRecordId = null)
    {
        $this->update([
            'status' => 'in_production',
            'production_record_id' => $productionRecordId,
        ]);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }

    public function getNumberAttribute()
    {
        return $this->accreditation_number ?? $this->registration_number;
    }
}
