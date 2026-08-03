@extends('layouts.app')

@section('title', __('messages.advanced_search'))

@section('content')
    <h3 class="mb-3">{{ __('messages.advanced_search') }}</h3>

    <div class="card zedan-card mb-3 shadow-sm">
        <div class="card-body shadow-sm">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="search" name="q" class="form-control" placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <select name="doctor_id" class="form-select">
                        <option value="">{{ __('messages.all_doctors') }}</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(request('doctor_id') == $doctor->id)>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="room_id" class="form-select">
                        <option value="">{{ __('messages.all_rooms') }}</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" @selected(request('room_id') == $room->id)>{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="visit_type" class="form-select">
                        <option value="">{{ __('messages.visit_type') }}</option>
                        <option value="initial_consultation" @selected(request('visit_type') === 'initial_consultation')>{{ __('messages.visit_type_initial_consultation') }}</option>
                        <option value="follow_up" @selected(request('visit_type') === 'follow_up')>{{ __('messages.visit_type_follow_up') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ __('messages.status') }}</option>
                        @foreach(['scheduled','in_progress','completed','cancelled','no_show'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('messages.status_'.$status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0">{{ __('messages.date_from') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0">{{ __('messages.date_to') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </form>
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($appointments->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.patient') }}</th>
                                <th>{{ __('messages.doctor') }}</th>
                                <th>{{ __('messages.room') }}</th>
                                <th>{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td data-label="{{ __('messages.date') }}">{{ $appointment->appointment_date->toDateString() }} {{ $appointment->start_time }}</td>
                                    <td data-label="{{ __('messages.patient') }}">{{ $appointment->patient->full_name }}</td>
                                    <td data-label="{{ __('messages.doctor') }}">{{ $appointment->doctor->name }}</td>
                                    <td data-label="{{ __('messages.room') }}">{{ $appointment->room->name }}</td>
                                    <td data-label="{{ __('messages.status') }}"><span class="badge text-bg-secondary">{{ __('messages.status_'.$appointment->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">{{ $appointments->links() }}</div>
@endsection
