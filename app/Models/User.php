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

    public const ROLE_ADMIN_DOCTOR = 'Admin Doctor';
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
        ];
    }

    public function getWorkingHoursSummaryAttribute(): string
    {
        if (empty($this->working_hours) || ! is_string($this->working_hours)) {
            return __('messages.closed');
        }

        return trim($this->working_hours) ?: __('messages.closed');
    }

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
        return $this->hasRole([self::ROLE_DOCTOR, self::ROLE_ADMIN_DOCTOR]);
    }

    public function isAdminDoctor(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN_DOCTOR);
    }

    public function isReceptionist(): bool
    {
        return $this->hasRole(self::ROLE_RECEPTIONIST);
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->roles->first()?->name ?? __('messages.no_role_assigned');
    }

    public function avatarUrl(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : asset('images/default-avatar.svg');
    }
}