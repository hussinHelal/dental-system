<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'purchase_date', 'purchase_cost', 'salvage_value',
        'useful_life_years', 'notes', 'attachment_path', 'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Straight-line annual depreciation. */
    public function annualDepreciation(): float
    {
        if ($this->useful_life_years <= 0) {
            return 0;
        }

        return round(($this->purchase_cost - $this->salvage_value) / $this->useful_life_years, 2);
    }

    public function yearsElapsed(): float
    {
        return min(
            $this->useful_life_years,
            max(0, $this->purchase_date->diffInDays(now()) / 365)
        );
    }

    public function accumulatedDepreciation(): float
    {
        return round($this->annualDepreciation() * $this->yearsElapsed(), 2);
    }

    public function bookValue(): float
    {
        return max($this->salvage_value, round($this->purchase_cost - $this->accumulatedDepreciation(), 2));
    }
}
