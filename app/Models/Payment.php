<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    public const TYPE_PAID_NOW = 'paid_now';
    public const TYPE_PAY_LATER = 'pay_later';
    public const TYPE_INSTALLMENT = 'installment';

    public const STATUS_PAID = 'paid';
    public const STATUS_PENDING = 'pending';
    public const STATUS_INSTALLMENT = 'installment';
    public const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'patient_id',
        'treatment_id',
        'appointment_id',
        'payment_type',
        'total_amount',
        'amount_paid',
        'remaining_balance',
        'payment_date',
        'due_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'payment_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payments')
            ->logOnly(['patient_id', 'treatment_id', 'payment_type', 'total_amount', 'amount_paid', 'remaining_balance', 'status'])
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    /**
     * Recomputes amount_paid, remaining_balance, and status from the
     * installments on record (or the flat paid/pending amount), then
     * saves. Call after every installment add/edit/delete.
     */
    public function recalculate(): void
    {
        if ($this->payment_type === self::TYPE_INSTALLMENT) {
            $this->amount_paid = $this->installments()->sum('amount');
        }

        $this->remaining_balance = max(0, $this->total_amount - $this->amount_paid);

        $this->status = match (true) {
            $this->remaining_balance <= 0 => self::STATUS_PAID,
            $this->payment_type === self::TYPE_INSTALLMENT => self::STATUS_INSTALLMENT,
            $this->due_date && $this->due_date->isPast() => self::STATUS_OVERDUE,
            default => self::STATUS_PENDING,
        };

        $this->save();
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => 'success',
            self::STATUS_INSTALLMENT => 'warning',
            self::STATUS_OVERDUE => 'danger',
            default => 'danger',
        };
    }
}
