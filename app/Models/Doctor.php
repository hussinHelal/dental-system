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
            'working_hours' => 'array',
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
            : asset('images/default-doctor.png');
    }

    public function getWorkingHoursSummaryAttribute(): string
    {
        if (empty($this->working_hours) || ! is_array($this->working_hours)) {
            return __('messages.closed');
        }

        return collect($this->working_hours)
            ->filter()
            ->map(fn ($hours, $day) => __('messages.day_'.$day).': '.$this->formatWorkingHoursRange($hours))
            ->implode(', ') ?: __('messages.closed');
    }

    public function getWorkingHoursForFormAttribute(): array
    {
        if (empty($this->working_hours) || ! is_array($this->working_hours)) {
            return [];
        }

        return collect($this->working_hours)
            ->mapWithKeys(fn ($hours, $day) => [$day => $this->formatWorkingHoursRange($hours)])
            ->all();
    }

    private function formatWorkingHoursRange(string $hours): string
    {
        $parts = explode('-', $hours);
        if (count($parts) !== 2) {
            return $hours;
        }

        return sprintf('%s - %s', $this->formatWorkingHoursTime(trim($parts[0])), $this->formatWorkingHoursTime(trim($parts[1])));
    }

    private function formatWorkingHoursTime(string $time): string
    {
        try {
            return Carbon::createFromFormat('H:i', $time)->format('g:i A');
        } catch (\Throwable $e) {
            return $time;
        }
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
