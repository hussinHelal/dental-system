<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Patient extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'full_name',
        'phone',
        'date_of_birth',
        'age',
        'address',
        'gender',
        'notes',
        'photo',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('patients')
            ->logOnly(['full_name', 'phone', 'date_of_birth', 'age', 'address', 'gender', 'notes'])
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

    /**
     * Computed age: prefers date_of_birth when known, otherwise falls
     * back to the manually entered age field.
     */
    public function getDisplayAgeAttribute(): ?int
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->age;
        }

        return $this->age;
    }

    public function photoUrl(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/default-patient.png');
    }

    /**
     * Running payment summary shown on the patient detail page.
     */
    public function paymentSummary(): array
    {
        $totalCost = $this->payments->sum('total_amount');
        $paid = $this->payments->sum('amount_paid');

        return [
            'total_cost' => $totalCost,
            'paid' => $paid,
            'remaining' => $totalCost - $paid,
        ];
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
