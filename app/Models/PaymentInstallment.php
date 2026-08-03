<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentInstallment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'payment_id',
        'amount',
        'paid_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payments')
            ->logOnly(['payment_id', 'amount', 'paid_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        // Keep the parent payment's running balance/status in sync
        // whenever an installment is added, changed, or removed.
        static::saved(fn (self $installment) => $installment->payment->recalculate());
        static::deleted(fn (self $installment) => $installment->payment->recalculate());
    }
}
