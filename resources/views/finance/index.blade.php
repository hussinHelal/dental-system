@extends('layouts.app')

@section('title', __('finance.title'))

@section('content')
    <script src="{{ asset('js/dental-ui.js') }}" defer></script>

    <div class="container-fluid px-0">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle p-2">
                    <i class="bi bi-cash-coin text-primary"></i>
                </div>
                <h3 class="mb-0">{{ __('finance.title') }}</h3>
            </div>

            <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
                <i class="bi bi-plus-lg me-1"></i>{{ __('finance.add_transaction') }}
            </button>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card zedan-card border-success shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('finance.total_income') }}</div>
                        <div class="fs-4 fw-bold text-success">{{ number_format($totals['income'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card border-danger shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('finance.total_expense') }}</div>
                        <div class="fs-4 fw-bold text-danger">{{ number_format($totals['expense'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card border-primary shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('finance.net_balance') }}</div>
                        <div class="fs-4 fw-bold {{ $totals['net'] >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ number_format($totals['net'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
            <select name="type" class="form-select" style="max-width: 160px;">
                <option value="">{{ __('finance.all') }}</option>
                <option value="income" @selected(request('type') === 'income')>{{ __('reports.income') }}</option>
                <option value="expense" @selected(request('type') === 'expense')>{{ __('reports.expense') }}</option>
            </select>
            <input type="text" name="category" value="{{ request('category') }}" class="form-control" placeholder="{{ __('finance.category') }}" style="max-width: 200px;">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control" style="max-width: 170px;">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control" style="max-width: 170px;">
            <button class="btn btn-outline-primary text-nowrap" type="submit">{{ __('finance.filter') }}</button>
            @if (request()->filled('type') || request()->filled('category') || request()->filled('from') || request()->filled('to'))
                <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary" title="{{ __('finance.filter') }}">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('finance.date') }}</th>
                                <th>{{ __('finance.type') }}</th>
                                <th>{{ __('finance.category') }}</th>
                                <th>{{ __('finance.amount') }}</th>
                                <th>{{ __('finance.payment_method') }}</th>
                                <th>{{ __('finance.recorded_by') }}</th>
                                <th class="text-end">{{ __('finance.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td data-label="{{ __('finance.date') }}">{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                    <td data-label="{{ __('finance.type') }}">
                                        <span class="badge bg-{{ $transaction->type->badgeColor() }}">{{ $transaction->type->label() }}</span>
                                    </td>
                                    <td data-label="{{ __('finance.category') }}">{{ $transaction->category ?: '—' }}</td>
                                    <td data-label="{{ __('finance.amount') }}">
                                        <span class="fw-semibold" data-compact-money data-value="{{ $transaction->amount }}">{{ number_format($transaction->amount, 2) }}</span>
                                    </td>
                                    <td data-label="{{ __('finance.payment_method') }}">
                                        @php $knownMethods = ['cash', 'bank', 'other']; @endphp
                                        {{ $transaction->payment_method
                                            ? (in_array($transaction->payment_method, $knownMethods) ? __('finance.' . $transaction->payment_method) : $transaction->payment_method)
                                            : '—' }}
                                    </td>
                                    <td data-label="{{ __('finance.recorded_by') }}">{{ $transaction->creator?->name ?: '—' }}</td>
                                    <td data-label="{{ __('finance.actions') }}" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTransactionModal{{ $transaction->id }}" title="{{ __('finance.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('finance.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('finance.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="{{ __('finance.delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-cash-coin fs-2 d-block mb-2"></i>
                                            {{ __('finance.no_records') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($transactions->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    {{ $transactions->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @foreach ($transactions as $transaction)
        @include('finance.partials.edit-modal', ['transaction' => $transaction])
    @endforeach

    @include('finance.partials.create-modal')
@endsection
