<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Appointment extends Model
{
    use HasFactory, LogsActivity;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'room_id',
        'treatment_id',
        'session_number',
        'visit_type',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('appointments')
            ->logOnly(['patient_id', 'doctor_id', 'room_id', 'appointment_date', 'start_time', 'end_time', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Find an overlapping, still-active appointment for the same doctor
     * or the same room on the same date. Cancelled/no-show slots don't
     * block new bookings. Pass $ignoreId when editing an existing
     * appointment so it doesn't conflict with itself.
     *
     * $lock acquires a row lock (SELECT ... FOR UPDATE) so this can be
     * called a second time, inside a transaction, immediately before
     * the actual insert - the FormRequest's own call to this (without a
     * lock) is just a fast pre-check for a friendly error message; two
     * requests could both pass it in the same instant under real
     * concurrent load. The locked call in the controller is the actual
     * safety net. Harmless/no-op on SQLite (which already serializes
     * writes at the connection level), meaningfully protective on
     * MySQL.
     *
     * Returns the first conflicting appointment, or null if the slot
     * is free for both the doctor and the room.
     */
    public static function findConflict(
        int $doctorId,
        int $roomId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null,
        bool $lock = false,
    ): ?self {
        $query = static::query()
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_NO_SHOW])
            ->where(function ($q) use ($doctorId, $roomId) {
                $q->where('doctor_id', $doctorId)->orWhere('room_id', $roomId);
            })
            // Overlap test: existing.start < new.end AND existing.end > new.start
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->with(['doctor', 'room', 'patient'])->first();
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->whereHas('patient', fn ($p) => $p->where('full_name', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%"))
                ->orWhereHas('doctor', fn ($d) => $d->where('name', 'like', "%{$term}%"))
                ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$term}%"));
        });
    }
}
