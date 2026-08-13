<?php

namespace App\Models;

use App\Enums\LabCaseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'dental_lab_id', 'patient_id', 'case_type', 'sent_date', 'expected_return_date',
        'actual_return_date', 'cost', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'status' => LabCaseStatus::class,
        'sent_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function lab()
    {
        return $this->belongsTo(DentalLab::class, 'dental_lab_id');
    }

    // Adjust to your actual Patient model if the relation name/columns differ.
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
