<?php

namespace App\Models;

use App\Enums\ToothStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToothChartEntry extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'tooth_number', 'status', 'notes', 'recorded_by'];

    protected $casts = [
        'tooth_number' => 'integer',
        'status' => ToothStatus::class,
    ];

    public function patient()
    {
        // Adjust if your Patient model's relation/column names differ.
        return $this->belongsTo(Patient::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
