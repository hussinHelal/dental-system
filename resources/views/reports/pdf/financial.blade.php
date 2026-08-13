<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background: #f2f2f2; }
        .totals { margin-top: 15px; }
        .totals td { border: none; padding: 2px 6px; }
    </style>
</head>
<body>
    <h2>{{ __('reports.title') }}</h2>
    <p>{{ $from }} &mdash; {{ $to }}</p>

    <table>
        <thead>
            <tr>
                <th>{{ __('finance.date') }}</th>
                <th>{{ __('finance.type') }}</th>
                <th>{{ __('finance.category') }}</th>
                <th>{{ __('finance.amount') }}</th>
                <th>{{ __('finance.description') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $transaction->type->label() }}</td>
                    <td>{{ $transaction->category }}</td>
                    <td>{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td><strong>{{ __('reports.total_income') }}</strong></td><td>{{ number_format($totals['income'], 2) }}</td></tr>
        <tr><td><strong>{{ __('reports.total_expense') }}</strong></td><td>{{ number_format($totals['expense'], 2) }}</td></tr>
        <tr><td><strong>{{ __('reports.net_balance') }}</strong></td><td>{{ number_format($totals['income'] - $totals['expense'], 2) }}</td></tr>
    </table>
</body>
</html>
