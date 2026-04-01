<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class OfficialLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'file_size',
        'file_hash',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Get the application this letter belongs to
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user who uploaded this letter (Registrar)
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the download URL for this letter
     */
    public function getDownloadUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Get the full file path
     */
    public function getFullPath(): string
    {
        return Storage::disk('public')->path($this->file_path);
    }

    /**
     * Check if file exists
     */
    public function fileExists(): bool
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get human-readable file size
     */
    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get file extension
     */
    public function getFileExtension(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is PDF
     */
    public function isPdf(): bool
    {
        return strtolower($this->getFileExtension()) === 'pdf';
    }

    /**
     * Check if file is image
     */
    public function isImage(): bool
    {
        $ext = strtolower($this->getFileExtension());
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true);
    }
}
