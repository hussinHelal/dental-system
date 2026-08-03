@extends('layouts.app')

@section('title', $treatment->name)

@section('content')
    <a href="{{ route('treatments.index') }}" class="btn btn-sm btn-link mb-2 ps-0 shadow-sm">
        <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }} icon-mirror-rtl"></i> {{ __('messages.back') }}
    </a>

    <div class="card zedan-card shadow-sm">
        <div class="card-body shadow-sm">
            <h4>{{ $treatment->name }}</h4>
            <p class="text-secondary">{{ $treatment->category }}</p>
            <p>{{ $treatment->description }}</p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-secondary small">{{ __('messages.default_cost') }}</div>
                    <div class="fw-bold">{{ number_format($treatment->default_cost, 2) }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-secondary small">{{ __('messages.duration_minutes') }}</div>
                    <div class="fw-bold">{{ $treatment->typical_duration_minutes ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-secondary small">{{ __('messages.multi_session') }}</div>
                    <div class="fw-bold">{{ $treatment->is_multi_session ? __('messages.yes') : __('messages.no') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
