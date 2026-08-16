@extends('layouts.app')

@section('title', __('agenda.title'))

@section('content')
    <script src="{{ asset('js/agenda.js') }}" defer></script>

    <div class="container-fluid px-0">
        @if ($loadError)
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>{{ $loadError }}
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle p-2">
                    <i class="bi bi-calendar-week text-primary"></i>
                </div>
                <h3 class="mb-0">{{ __('agenda.title') }}</h3>
            </div>

            <form method="GET" id="agendaFilterForm" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end">
                <button type="button" id="agendaPrevDay" class="btn btn-outline-secondary" aria-label="{{ __('agenda.previous_day') }}">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <input type="date" id="agendaDateInput" name="date" value="{{ $date }}" class="form-control" style="max-width: 170px;">

                <button type="button" id="agendaNextDay" class="btn btn-outline-secondary" aria-label="{{ __('agenda.next_day') }}">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <button type="button" id="agendaToday" class="btn btn-outline-primary text-nowrap">
                    {{ __('agenda.today') }}
                </button>

                <select id="agendaDoctorFilter" name="doctor_id" class="form-select" style="max-width: 220px;">
                    <option value="">{{ __('agenda.all_doctors') }}</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected((string) $selectedDoctorId === (string) $doctor->id)>
                            {{ $doctor->name }}
                        </option>
                    @endforeach
                </select>

                <noscript>
                    <button type="submit" class="btn btn-outline-primary">{{ __('agenda.filter') }}</button>
                </noscript>
            </form>
        </div>

        <div id="agendaStatusBanner" class="mb-3" aria-live="polite"></div>

        <div id="agendaContent">
            @if (! $loadError)
                @forelse ($doctors as $doctor)
                    @php $doctorAppointments = $groupedAppointments->get($doctor->id, collect())->sortBy('start_time'); @endphp

                    @if (is_null($selectedDoctorId) || (string) $selectedDoctorId === '' || (string) $selectedDoctorId === (string) $doctor->id)
                        <div class="card zedan-card shadow-sm mb-3" data-doctor-card="{{ $doctor->id }}">
                            <div class="card-header bg-transparent d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge text-primary"></i>
                                <span class="fw-semibold">{{ $doctor->name }}</span>
                                <span class="badge text-bg-light border ms-auto">{{ $doctorAppointments->count() }}</span>
                            </div>
                            <div class="card-body p-0">
                                @if ($doctorAppointments->isEmpty())
                                    <div class="text-muted text-center py-4">{{ __('agenda.no_appointments_for_doctor') }}</div>
                                @else
                                    <div class="list-group list-group-flush">
                                        @foreach ($doctorAppointments as $appointment)
                                            <div class="list-group-item d-flex flex-wrap align-items-center gap-3">
                                                <div class="text-nowrap fw-semibold" style="min-width: 100px;">
                                                    {{ optional($appointment->start_time)->format('H:i') ?? $appointment->start_time }}
                                                    @if ($appointment->end_time)
                                                        &ndash; {{ optional($appointment->end_time)->format('H:i') ?? $appointment->end_time }}
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div>{{ $appointment->patient->full_name ?? $appointment->patient->name ?? __('agenda.unknown_patient') }}</div>
                                                    @if ($appointment->room)
                                                        <div class="text-muted small">
                                                            <i class="bi bi-door-open"></i> {{ $appointment->room->name }}
                                                        </div>
                                                    @endif
                                                    @if ($appointment->notes)
                                                        <div class="text-muted small">{{ $appointment->notes }}</div>
                                                    @endif
                                                </div>
                                                @if ($appointment->status)
                                                    <span class="badge {{ match (strtolower((string) $appointment->status)) {
                                                        'completed' => 'text-bg-success',
                                                        'cancelled' => 'text-bg-danger',
                                                        'no_show', 'no-show' => 'text-bg-secondary',
                                                        'confirmed' => 'text-bg-primary',
                                                        default => 'text-bg-light border',
                                                    } }}">
                                                        {{ $appointment->status }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                        {{ __('agenda.no_doctors') }}
                    </div>
                @endforelse

                @php $unassigned = $groupedAppointments->get('unassigned', collect())->sortBy('start_time'); @endphp
                @if ($unassigned->isNotEmpty() && (is_null($selectedDoctorId) || (string) $selectedDoctorId === ''))
                    <div class="card zedan-card shadow-sm mb-3">
                        <div class="card-header bg-transparent d-flex align-items-center gap-2">
                            <i class="bi bi-question-circle text-secondary"></i>
                            <span class="fw-semibold">{{ __('agenda.unassigned') }}</span>
                            <span class="badge text-bg-light border ms-auto">{{ $unassigned->count() }}</span>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach ($unassigned as $appointment)
                                <div class="list-group-item d-flex flex-wrap align-items-center gap-3">
                                    <div class="text-nowrap fw-semibold" style="min-width: 100px;">
                                        {{ optional($appointment->start_time)->format('H:i') ?? $appointment->start_time }}
                                        @if ($appointment->end_time)
                                            &ndash; {{ optional($appointment->end_time)->format('H:i') ?? $appointment->end_time }}
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        {{ $appointment->patient->full_name ?? $appointment->patient->name ?? __('agenda.unknown_patient') }}
                                        @if ($appointment->notes)
                                            <div class="text-muted small">{{ $appointment->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        window.agendaConfig = {
            dataUrl: @json(route('agenda.data')),
            initialDate: @json($date),
            initialDoctorId: @json($selectedDoctorId),
            doctors: @json($doctors->map(fn ($doctor) => ['id' => (string) $doctor->id, 'name' => $doctor->name])->values()),
            labels: {
                loading: @json(__('agenda.loading')),
                loadFailed: @json(__('agenda.load_failed')),
                retry: @json(__('agenda.retry')),
                noAppointmentsForDoctor: @json(__('agenda.no_appointments_for_doctor')),
                noDoctors: @json(__('agenda.no_doctors')),
                unknownPatient: @json(__('agenda.unknown_patient')),
                unassigned: @json(__('agenda.unassigned')),
            },
        };
    </script>
@endsection
