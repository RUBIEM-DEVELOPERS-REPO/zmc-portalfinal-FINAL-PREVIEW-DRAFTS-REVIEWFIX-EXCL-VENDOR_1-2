<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_type',
        'template_name',
        'version',
        'year',
        'background_image_path',
        'layout_config',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'layout_config' => 'array',
        'is_active' => 'boolean',
        'year' => 'integer',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function printLogs()
    {
        return $this->hasMany(PrintLog::class, 'template_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAccreditationCards($query)
    {
        return $query->where('template_type', 'accreditation_card');
    }

    public function scopeRegistrationCertificates($query)
    {
        return $query->where('template_type', 'registration_certificate');
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Helper methods
    public function activate()
    {
        // Deactivate all other templates of the same type and year
        static::where('template_type', $this->template_type)
              ->where('year', $this->year)
              ->where('id', '!=', $this->id)
              ->update(['is_active' => false]);

        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function isAccreditationCard()
    {
        return $this->template_type === 'accreditation_card';
    }

    public function isRegistrationCertificate()
    {
        return $this->template_type === 'registration_certificate';
    }
}
