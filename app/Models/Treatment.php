<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Treatment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'category',
        'description',
        'typical_duration_minutes',
        'default_cost',
        'is_multi_session',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_multi_session' => 'boolean',
            'is_active' => 'boolean',
            'default_cost' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('treatments')
            ->logOnly(['name', 'category', 'default_cost', 'is_multi_session', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * All sessions for one patient's course of this treatment, ordered
     * so session 1/2/3 can be listed with date, doctor, and notes.
     */
    public function sessionsForPatient(int $patientId)
    {
        return $this->appointments()
            ->where('patient_id', $patientId)
            ->orderBy('session_number')
            ->orderBy('appointment_date')
            ->with('doctor')
            ->get();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMultiSession($query, bool $multiSession = true)
    {
        return $query->where('is_multi_session', $multiSession);
    }

    public function scopeSearch($query, ?string $term)
    {
        return $term
            ? $query->where('name', 'like', "%{$term}%")
            : $query;
    }

    public function scopeCategory($query, ?string $category)
    {
        return $category
            ? $query->where('category', $category)
            : $query;
    }
}
