<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PatientPictureHistory extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'patient_picture_history';

    protected $fillable = [
        'patient_id',
        'picture_type',
        'picture_path',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('patient_pictures')
            ->logOnly(['patient_id', 'picture_type', 'picture_path', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the URL for the picture
     */
    public function getPictureUrl(): string
    {
        if (!$this->picture_path) {
            return '';
        }
        return Storage::url($this->picture_path);
    }

    /**
     * Delete the picture file from storage
     * Safe to call even if file doesn't exist
     */
    public function deletePictureFile(): void
    {
        if ($this->picture_path) {
            try {
                if (Storage::exists($this->picture_path)) {
                    Storage::delete($this->picture_path);
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to delete picture file: {$this->picture_path}", ['error' => $e->getMessage()]);
                // Don't throw - allow database deletion to proceed
            }
        }
    }

    /**
     * Scope to get the latest picture of a specific type
     */
    public function scopeLatestOfType($query, $type)
    {
        return $query->where('picture_type', $type)
            ->orderByDesc('created_at')
            ->limit(1);
    }

    /**
     * Scope to get pictures of a specific type (newest first)
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('picture_type', $type)
            ->orderByDesc('created_at');
    }

    /**
     * Boot method - automatically clean up files when record is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($picture) {
            $picture->deletePictureFile();
        });
    }
}
