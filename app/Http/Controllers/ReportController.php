<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Exports\FinancialTransactionsExport;
use App\Models\Asset;
use App\Models\FinancialTransaction;
use App\Models\InsuranceContract;
use App\Models\LabCase;
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private const PDF_MAX_DAYS = 366;

    public function index(Request $request): View
    {
        [$from, $to] = $this->resolveDateRange($request);

        $hasFinancialTransactions = Schema::hasTable('financial_transactions');

        if ($hasFinancialTransactions) {
            $transactions = FinancialTransaction::query()->betweenDates($from, $to);

            $totals = [
                'income' => (clone $transactions)->income()->sum('amount'),
                'expense' => (clone $transactions)->expense()->sum('amount'),
            ];

            $monthly = FinancialTransaction::query()
                ->betweenDates(
                    now()->subMonths(5)->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString()
                )
                ->selectRaw($this->monthExpression().' as month, type, SUM(amount) as total')
                ->groupBy('month', 'type')
                ->orderBy('month')
                ->get()
                ->groupBy('month');
        } else {
            // The report remains usable before the optional transaction table is
            // migrated. Purchases are represented as expenses; income requires
            // a real transaction source and is therefore zero rather than guessed.
            $totals = [
                'income' => 0,
                'expense' => Purchase::query()
                    ->whereBetween('purchase_date', [$from, $to])
                    ->sum('total_amount'),
            ];

            $monthly = collect();
        }

        $totals['net'] = $totals['income'] - $totals['expense'];

        $assetsBookValue = 0.0;

        foreach (Asset::query()
            ->select(['id', 'purchase_cost', 'salvage_value', 'useful_life_years', 'purchase_date'])
            ->cursor() as $asset) {
            $assetsBookValue += $asset->bookValue();
        }

        $stats = [
            'purchases_total' => Purchase::query()
                ->whereBetween('purchase_date', [$from, $to])
                ->sum('total_amount'),
            'open_lab_cases' => LabCase::query()
                ->whereIn('status', ['sent', 'in_progress'])
                ->count(),
            'assets_book_value' => round($assetsBookValue, 2),
            'active_insurance_contracts' => InsuranceContract::query()
                ->where('status', 'active')
                ->count(),
        ];

        return view('reports.index', compact('totals', 'monthly', 'stats', 'from', 'to'));
    }

    public function exportExcel(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);

        return Excel::download(
            new FinancialTransactionsExport($from, $to),
            'financial-report.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);

        if (Carbon::parse($from)->diffInDays(Carbon::parse($to)) > self::PDF_MAX_DAYS) {
            return back()
                ->withErrors(['to' => __('reports.pdf_range_too_large')])
                ->withInput();
        }

        if (Schema::hasTable('financial_transactions')) {
            $transactions = FinancialTransaction::query()
                ->betweenDates($from, $to)
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->get();

            $totals = [
                'income' => $transactions->where('type', TransactionType::Income)->sum('amount'),
                'expense' => $transactions->where('type', TransactionType::Expense)->sum('amount'),
            ];
        } else {
            $purchases = Purchase::query()
                ->select(['id', 'purchase_date', 'total_amount', 'notes'])
                ->whereBetween('purchase_date', [$from, $to])
                ->orderBy('purchase_date')
                ->orderBy('id')
                ->get();

            // Present purchases as expense transactions in the PDF fallback.
            // This keeps the report useful before the transaction table migration
            // is applied, without inventing any income.
            $transactions = $purchases->map(function (Purchase $purchase): FinancialTransaction {
                $transaction = new FinancialTransaction();
                $transaction->forceFill([
                    'transaction_date' => $purchase->purchase_date,
                    'type' => TransactionType::Expense,
                    'category' => __('reports.category_purchase'),
                    'amount' => $purchase->total_amount,
                    'description' => $purchase->notes,
                    // payment_method is deliberately left unset here: it's a
                    // different concept from the purchase's payment_status
                    // (cash/bank/other vs paid/partial/unpaid), and assigning
                    // one to the other would put a PurchasePaymentStatus enum
                    // instance into a plain string column — harmless today
                    // since the PDF template doesn't render this field, but a
                    // landmine the moment someone adds it to the template.
                ]);

                return $transaction;
            });

            $totals = [
                'income' => 0,
                'expense' => $transactions->sum('amount'),
            ];
        }

        $pdf = Pdf::loadView('reports.pdf.financial', compact('transactions', 'totals', 'from', 'to'));

        return $pdf->download('financial-report.pdf');
    }

    private function resolveDateRange(Request $request): array
    {
        $defaultFrom = now()->startOfMonth()->toDateString();
        $defaultTo = now()->toDateString();

        $from = $this->safeDate($request->input('from'), $defaultFrom);
        $to = $this->safeDate($request->input('to'), $defaultTo);

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }

    private function safeDate(mixed $value, string $fallback): string
    {
        if (! is_string($value) || $value === '') {
            return $fallback;
        }

        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);

            return $date->format('Y-m-d') === $value
                ? $date->toDateString()
                : $fallback;
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function monthExpression(): string
    {
        return match (FinancialTransaction::query()->getConnection()->getDriverName()) {
            'mysql' => "DATE_FORMAT(transaction_date, '%Y-%m')",
            'pgsql' => "TO_CHAR(transaction_date, 'YYYY-MM')",
            default => "strftime('%Y-%m', transaction_date)",
        };
    }
}
