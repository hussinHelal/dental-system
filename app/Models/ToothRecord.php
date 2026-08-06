<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ToothRecord extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'patient_id', 'tooth_number', 'status', 'notes', 'treatment_id', 'recorded_by'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tooth_records')
            ->logOnly(['tooth_number', 'status', 'notes', 'treatment_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}