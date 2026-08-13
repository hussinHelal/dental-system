<?php

namespace App\Models;

use App\Enums\InsuranceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name', 'contract_number', 'start_date', 'end_date', 'coverage_details',
        'discount_percentage', 'contact_person', 'phone', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'decimal:2',
        'status' => InsuranceStatus::class,
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->end_date->isBeforeToday();
    }
}
