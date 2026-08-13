@extends('layouts.app')

@section('title', __('reports.title'))

@section('content')
    <div class="container-fluid px-0">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary-subtle p-2"><i class="bi bi-bar-chart-line text-primary"></i></div><h3 class="mb-0">{{ __('reports.title') }}</h3></div>
            <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
                <div><label class="form-label small mb-1">{{ __('reports.from') }}</label><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
                <div><label class="form-label small mb-1">{{ __('reports.to') }}</label><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
                <button class="btn btn-outline-primary" type="submit">{{ __('reports.filter') }}</button>
                <div class="d-flex gap-1"><a class="btn btn-outline-secondary" href="{{ route('reports.export-excel', ['from' => $from, 'to' => $to]) }}"><i class="bi bi-file-earmark-spreadsheet me-1"></i>{{ __('reports.export_excel') }}</a><a class="btn btn-outline-secondary" href="{{ route('reports.export-pdf', ['from' => $from, 'to' => $to]) }}"><i class="bi bi-file-earmark-pdf me-1"></i>{{ __('reports.export_pdf') }}</a></div>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ __('reports.total_income') }}</div><div class="fs-4 fw-bold text-success">{{ number_format($totals['income'], 2) }}</div></div></div></div>
            <div class="col-md-4"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ __('reports.total_expense') }}</div><div class="fs-4 fw-bold text-danger">{{ number_format($totals['expense'], 2) }}</div></div></div></div>
            <div class="col-md-4"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ __('reports.net_balance') }}</div><div class="fs-4 fw-bold {{ $totals['net'] >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($totals['net'], 2) }}</div></div></div></div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ __('reports.purchases_total') }}</div><div class="fs-5 fw-bold">{{ number_format($stats['purchases_total'], 2) }}</div></div></div></div>
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ __('reports.open_lab_cases') }}</div><div class="fs-5 fw-bold">{{ $stats['open_lab_cases'] }}</div></div></div></div>
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ __('reports.assets_book_value') }}</div><div class="fs-5 fw-bold">{{ number_format($stats['assets_book_value'], 2) }}</div></div></div></div>
            <div class="col-md-3 col-6"><div class="card zedan-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ __('reports.active_insurance_contracts') }}</div><div class="fs-5 fw-bold">{{ $stats['active_insurance_contracts'] }}</div></div></div></div>
        </div>

        <div class="card zedan-card shadow-sm"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2"><h5 class="card-title mb-0">{{ __('reports.monthly_trend') }}</h5><div class="d-flex gap-3 small"><span><span class="d-inline-block rounded-1 bg-success" style="width:12px;height:12px;"></span> {{ __('reports.income') }}</span><span><span class="d-inline-block rounded-1 bg-danger" style="width:12px;height:12px;"></span> {{ __('reports.expense') }}</span></div></div>
            <div class="w-100"><svg id="monthlyChart" viewBox="0 0 760 260" style="width:100%;height:auto;min-height:260px;" role="img" aria-label="{{ __('reports.monthly_trend') }}"></svg></div>
        </div></div>
    </div>

    <script src="{{ asset('js/reports-monthly-chart.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            renderMonthlyChart('monthlyChart', @json($monthly), { noData: @json(__('reports.no_data')) });
        });
    </script>
@endsection
