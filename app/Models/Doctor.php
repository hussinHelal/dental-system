<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Doctor extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'name',
        'specialty',
        'phone',
        'working_hours',
        'photo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('doctors')
            ->logOnly(['name', 'specialty', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function photoUrl(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/default-doctor.svg');
    }

    public function getWorkingHoursSummaryAttribute(): string
    {
        if (empty($this->working_hours) || ! is_string($this->working_hours)) {
            return __('messages.closed');
        }

        return trim($this->working_hours) ?: __('messages.closed');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        return $term
            ? $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('specialty', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            })
            : $query;
    }
}
