<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Patient extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'full_name', 'phone', 'date_of_birth', 'age',
        'address', 'gender', 'notes', 'photo', 'xray_photo',
        'tooth_chart','crown_color', 'created_by',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'tooth_chart'   => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('patients')
            ->logOnly([
                'full_name', 'phone', 'date_of_birth', 'age',
                'address', 'gender', 'notes', 'tooth_chart',
                'crown_color', 'xray_photo'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderByDesc('appointment_date');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function toothRecords(): HasMany
    {
        return $this->hasMany(ToothRecord::class)->orderBy('tooth_number');
    }

    public function toothTimelineEvents(): HasMany
    {
        return $this->hasMany(ToothTimelineEvent::class)->latest();
    }

    public function pictureHistory(): HasMany
    {
        return $this->hasMany(PatientPictureHistory::class)->orderByDesc('created_at');
    }

    public function getDisplayAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age ?? $this->age;
    }

    public function paymentSummary(): array
    {
        $aggregates = $this->payments()
            ->selectRaw('COALESCE(SUM(total_amount), 0) as total_cost, COALESCE(SUM(amount_paid), 0) as paid')
            ->first();

        $totalCost = (float) ($aggregates?->total_cost ?? 0);
        $paid = (float) ($aggregates?->paid ?? 0);

        return [
            'total_cost' => (float) $totalCost,
            'paid' => (float) $paid,
            'remaining' => (float) max(0, $totalCost - $paid),
        ];
    }

    public function photoUrl(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/default-patient.svg');
    }

    public function xrayPhotoUrl(): ?string
    {
        return $this->xray_photo
            ? asset('storage/'.$this->xray_photo)
            : null;
    }

    public function crownColor(): ?string
    {
        return $this->crown_color;
    }

    public function getToothChartAttribute($value): array
    {
        $chart = $value ? json_decode($value, true) : [];
        if (! is_array($chart)) {
            $chart = [];
        }

        $defaults = [];
        for ($i = 1; $i <= 32; $i++) {
            $defaults[(string) $i] = $chart[(string) $i] ?? 'healthy';
        }

        return $defaults;
    }

    public function scopeSearch($query, ?string $term)
    {
        return $term
            ? $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            })
            : $query;
    }
}