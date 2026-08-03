<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
