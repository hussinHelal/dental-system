@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
    <h3 class="mb-4">{{ __('messages.dashboard') }}</h3>

    <div class="row g-3 mb-4 shadow-sm">
        <div class="col-6 col-md-3">
            <div class="card zedan-stat-card h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('messages.todays_appointments') }}</div>
                    <div class="fs-3 fw-bold">{{ $todaysAppointments->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card zedan-stat-card h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('messages.low_stock_items') }}</div>
                    <div class="fs-3 fw-bold {{ $lowStockItems->count() ? 'text-danger' : '' }}">{{ $lowStockItems->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card zedan-stat-card h-100">
                <div class="card-body">
                    <div class="text-secondary small">{{ __('messages.active_doctors') }}</div>
                    <div class="fs-3 fw-bold">{{ $activeDoctorsCount }}</div>
                </div>
            </div>
        </div>
        @if($financials)
            <div class="col-6 col-md-3 shadow-sm">
                <div class="card zedan-stat-card h-100">
                    <div class="card-body">
                        <div class="text-secondary small">{{ __('messages.todays_revenue') }}</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($financials['todays_revenue'], 2) }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($financials)
        <div class="row g-3 mb-4 shadow-sm">
            <div class="col-md-4">
                <div class="card zedan-card">
                    <div class="card-body">
                        <div class="text-secondary small">{{ __('messages.pending_payments') }}</div>
                        <div class="fs-4 fw-bold text-danger">{{ number_format($financials['pending_payments'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card">
                    <div class="card-body">
                        <div class="text-secondary small">{{ __('messages.installment_totals') }}</div>
                        <div class="fs-4 fw-bold text-warning">{{ number_format($financials['installment_totals'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card zedan-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-secondary small">{{ __('messages.last_backup') }}</div>
                            <div class="fw-semibold">
                                {{ $lastBackup?->generated_at?->format('Y-m-d H:i') ?? __('messages.never') }}
                            </div>
                        </div>
                        @can('create', \App\Models\Backup::class)
                            <a href="{{ route('backups.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-cloud-arrow-up"></i> {{ __('messages.backup_now') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="card zedan-card mb-4 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">{{ __('messages.weekly_revenue') }}</h6>
                <canvas id="weeklyRevenueChart" height="90" data-points='@json($weeklyRevenue)'></canvas>
            </div>
        </div>
    @endif

    <div class="card zedan-card shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span>{{ __('messages.todays_appointments') }}</span>
            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view_schedule') }}</a>
        </div>
        <div class="card-body p-0">
            @if($todaysAppointments->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.time') }}</th>
                                <th>{{ __('messages.patient') }}</th>
                                <th>{{ __('messages.doctor') }}</th>
                                <th>{{ __('messages.room') }}</th>
                                <th>{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todaysAppointments as $appointment)
                                <tr>
                                    <td data-label="{{ __('messages.time') }}">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                                    <td data-label="{{ __('messages.patient') }}">{{ $appointment->patient->full_name }}</td>
                                    <td data-label="{{ __('messages.doctor') }}">{{ $appointment->doctor->name }}</td>
                                    <td data-label="{{ __('messages.room') }}">{{ $appointment->room->name }}</td>
                                    <td data-label="{{ __('messages.status') }}">
                                        <span class="badge text-bg-secondary">{{ __('messages.status_'.$appointment->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
