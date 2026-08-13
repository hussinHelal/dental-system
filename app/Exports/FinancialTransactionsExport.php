<?php

namespace App\Exports;

use App\Models\FinancialTransaction;
use App\Models\Purchase;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinancialTransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?string $from = null,
        private readonly ?string $to = null,
    ) {
    }

    public function query()
    {
        if (Schema::hasTable('financial_transactions')) {
            return FinancialTransaction::query()
                ->select([
                    'id', 'transaction_date', 'type', 'category', 'amount',
                    'payment_method', 'description',
                ])
                ->betweenDates($this->from, $this->to)
                ->orderBy('transaction_date')
                ->orderBy('id');
        }

        // Graceful fallback for installations that have not run the new
        // financial-transactions migration yet. Purchases are real expenses,
        // so exporting them is more useful than failing the entire report.
        return Purchase::query()
            ->select([
                'id',
                'purchase_date as transaction_date',
                'total_amount as amount',
                'payment_status as payment_method',
                'notes as description',
            ])
            ->selectRaw("'expense' as type")
            ->selectRaw("'Purchase' as category")
            ->when($this->from, fn ($q) => $q->whereDate('purchase_date', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('purchase_date', '<=', $this->to))
            ->orderBy('purchase_date')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return ['Date', 'Type', 'Category', 'Amount', 'Payment Method', 'Description'];
    }

    public function map($transaction): array
    {
        $type = $transaction->type;
        $typeValue = is_object($type) && property_exists($type, 'value')
            ? $type->value
            : (string) $type;

        $date = $transaction->transaction_date;
        $dateValue = $date instanceof \DateTimeInterface
            ? $date->format('Y-m-d')
            : (string) $date;

        return [
            $dateValue,
            $typeValue,
            $transaction->category,
            $transaction->amount,
            $transaction->payment_method,
            $transaction->description,
        ];
    }
}
