<?php

namespace App\Models;

use App\Enums\PurchasePaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id', 'purchase_date', 'total_amount', 'payment_status', 'notes', 'created_by',
    ];

    protected $casts = [
        'payment_status' => PurchasePaymentStatus::class,
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recalculateTotal(): void
    {
        $total = $this->items()->sum('subtotal');

        // Keep the persisted total authoritative and normalized to the DB's 2-decimal
        // money precision.
        $this->forceFill(['total_amount' => round((float) $total, 2)])->save();
    }
}
