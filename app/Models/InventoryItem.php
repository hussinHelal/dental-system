<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryItem extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'category',
        'photo',
        'low_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inventory')
            ->logOnly(['name', 'quantity', 'unit', 'category', 'low_stock_threshold'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    public function photoUrl(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/default-item.svg');
    }

    public function scopeSearch($query, ?string $term)
    {
        return $term
            ? $query->where('name', 'like', "%{$term}%")
            : $query;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'low_stock_threshold');
    }
}
