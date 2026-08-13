<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'type',
        'category',
        'amount',
        'payment_method',
        'description',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'type' => TransactionType::class,
        'amount' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $q) => $q->whereDate('transaction_date', '>=', $from))
            ->when($to, fn (Builder $q) => $q->whereDate('transaction_date', '<=', $to));
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', TransactionType::Income->value);
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', TransactionType::Expense->value);
    }
}
