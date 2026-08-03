<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable;

    public const ROLE_DOCTOR = 'Doctor';
    public const ROLE_RECEPTIONIST = 'Receptionist';

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'username',
        'password',
        'avatar',
        'theme',
        'is_active',
        'working_hours',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'working_hours' => 'array',
        ];
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

    /**
     * Never log password/remember_token/theme (theme is a UI
     * preference, not an accountability-relevant change) - name,
     * username, and is_active (activate/deactivate) are what matter for
     * a staff-account audit trail.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('staff')
            ->logOnly(['name', 'username', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function isDoctor(): bool
    {
        return $this->hasRole(self::ROLE_DOCTOR);
    }

    public function isReceptionist(): bool
    {
        return $this->hasRole(self::ROLE_RECEPTIONIST);
    }

    public function avatarUrl(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : asset('images/default-avatar.png');
    }
}
