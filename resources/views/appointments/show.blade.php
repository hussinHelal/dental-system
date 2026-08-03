@extends('layouts.app')

@section('title', __('messages.appointment_details'))

@section('content')
    <a href="{{ route('appointments.index', ['date' => $appointment->appointment_date->toDateString()]) }}" class="btn btn-sm btn-link mb-2 ps-0">
        <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }} icon-mirror-rtl"></i> {{ __('messages.back') }}
    </a>

    <div class="card zedan-card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-3">{{ __('messages.patient') }}</dt>
                <dd class="col-9">{{ $appointment->patient->full_name }} ({{ $appointment->patient->phone }})</dd>
                <dt class="col-3">{{ __('messages.doctor') }}</dt>
                <dd class="col-9">{{ $appointment->doctor->name }}</dd>
                <dt class="col-3">{{ __('messages.room') }}</dt>
                <dd class="col-9">{{ $appointment->room->name }}</dd>
                <dt class="col-3">{{ __('messages.date') }}</dt>
                <dd class="col-9">{{ $appointment->appointment_date->toDateString() }}, {{ $appointment->start_time }} - {{ $appointment->end_time }}</dd>
                <dt class="col-3">{{ __('messages.treatment') }}</dt>
                <dd class="col-9">{{ $appointment->treatment?->name ?? '-' }}</dd>
                <dt class="col-3">{{ __('messages.status') }}</dt>
                <dd class="col-9"><span class="badge text-bg-secondary">{{ __('messages.status_'.$appointment->status) }}</span></dd>
                <dt class="col-3">{{ __('messages.notes') }}</dt>
                <dd class="col-9">{{ $appointment->notes ?: '-' }}</dd>
            </dl>
        </div>
    </div>
@endsection
