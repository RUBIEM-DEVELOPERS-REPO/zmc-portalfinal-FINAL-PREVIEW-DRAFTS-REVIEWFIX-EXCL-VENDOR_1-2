<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'file_type',
        'size',
        'download_url',
        'preview_url',
        'uploaded_by',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const CATEGORY_ACCREDITATION = 'accreditation';
    public const CATEGORY_REGISTRATION = 'registration';
    public const CATEGORY_SYSTEM = 'system';
    public const CATEGORY_REPORT = 'report';

    /**
     * Get the category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            self::CATEGORY_ACCREDITATION => 'Accreditation',
            self::CATEGORY_REGISTRATION => 'Registration',
            self::CATEGORY_SYSTEM => 'System',
            self::CATEGORY_REPORT => 'Report',
            default => 'Unknown',
        };
    }

    /**
     * Get the category badge color.
     */
    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            self::CATEGORY_ACCREDITATION => 'primary',
            self::CATEGORY_REGISTRATION => 'success',
            self::CATEGORY_SYSTEM => 'secondary',
            self::CATEGORY_REPORT => 'info',
            default => 'light',
        };
    }

    /**
     * Format file size for display.
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->size) {
            return '—';
        }

        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope a query to only include accreditation downloads.
     */
    public function scopeAccreditation($query)
    {
        return $query->where('category', self::CATEGORY_ACCREDITATION);
    }

    /**
     * Scope a query to only include registration downloads.
     */
    public function scopeRegistration($query)
    {
        return $query->where('category', self::CATEGORY_REGISTRATION);
    }

    /**
     * Scope a query to only include system downloads.
     */
    public function scopeSystem($query)
    {
        return $query->where('category', self::CATEGORY_SYSTEM);
    }

    /**
     * Scope a query to only include report downloads.
     */
    public function scopeReport($query)
    {
        return $query->where('category', self::CATEGORY_REPORT);
    }
}
