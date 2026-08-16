<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Models\FinancialTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = fn () => FinancialTransaction::query()
            ->betweenDates($request->input('from'), $request->input('to'))
            ->when($request->filled('category'), fn ($q) => $q->where('category', 'like', '%' . $request->input('category') . '%'));

        $transactions = $baseQuery()
            ->with('creator')
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->latest('transaction_date')
            ->paginate(20)
            ->withQueryString();

        $totals = [
            'income' => $baseQuery()->income()->sum('amount'),
            'expense' => $baseQuery()->expense()->sum('amount'),
        ];
        $totals['net'] = $totals['income'] - $totals['expense'];

        return view('finance.index', compact('transactions', 'totals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        FinancialTransaction::create($data);

        return back()->with('success', __('finance.created_successfully'));
    }

    public function update(Request $request, FinancialTransaction $transaction): RedirectResponse
    {
        $transaction->update($this->validated($request));

        return back()->with('success', __('finance.updated_successfully'));
    }

    public function destroy(FinancialTransaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return back()->with('success', __('finance.deleted_successfully'));
    }

    private function validated(Request $request): array
    {
        $payload = $request->all();
        $payload['amount'] = $this->normalizeAmount($payload['amount'] ?? null);
        $request->replace($payload);

        return $request->validate([
            'type' => ['required', 'in:' . implode(',', array_column(TransactionType::cases(), 'value'))],
            'category' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'decimal:0,2', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'transaction_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function normalizeAmount(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = strtolower(trim((string) $value));
        $value = str_replace(',', '', $value);
        $multiplier = 1;
        $suffix = substr($value, -1);

        if ($suffix === 'k' || $suffix === 'm' || $suffix === 'b') {
            $multiplier = match ($suffix) {
                'k' => 1000,
                'm' => 1000000,
                'b' => 1000000000,
            };
            $value = substr($value, 0, -1);
        }

        if (!preg_match('/^\d+(?:\.\d+)?$/', $value)) {
            return (string) $value;
        }

        return number_format((float) $value * $multiplier, 2, '.', '');
    }
}
