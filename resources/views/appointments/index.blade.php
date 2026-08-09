@extends('layouts.app')

@section('title', __('messages.appointments'))

@section('content')
    @php
        $prevDate = \Illuminate\Support\Carbon::parse($date)->subDay()->toDateString();
        $nextDate = \Illuminate\Support\Carbon::parse($date)->addDay()->toDateString();
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm p-3 rounded-4 zedan-page-header">
        <h3 class="mb-0">{{ __('messages.appointments') }}</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('appointments.search') }}" class="btn btn-outline-secondary">
                <i class="bi bi-search"></i> {{ __('messages.advanced_search') }}
            </a>
            @can('create', \App\Models\Appointment::class)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.book_appointment') }}
                </button>
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#quickPatientModal">
                    <i class="bi bi-person-plus"></i> {{ __('messages.new_patient') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card zedan-card mb-3 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <a href="{{ route('appointments.index', array_merge(request()->query(), ['date' => $prevDate])) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} icon-mirror-rtl"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()">
                </div>
                <div class="col-auto">
                    <a href="{{ route('appointments.index', array_merge(request()->query(), ['date' => $nextDate])) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }} icon-mirror-rtl"></i>
                    </a>
                </div>
                <div class="col-auto flex-grow-1">
                    <select name="doctor_id" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_doctors') }}</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(request('doctor_id') == $doctor->id)>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto flex-grow-1">
                    <select name="room_id" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_rooms') }}</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" @selected(request('room_id') == $room->id)>{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($appointments->isEmpty())
                <x-empty-state :message="__('messages.no_appointments_today')" />
            @else
                <div class="list-group list-group-flush">
                    @foreach($appointments as $appointment)
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center flex-wrap gap-2"
                                data-bs-toggle="modal" data-bs-target="#viewAppointmentModal{{ $appointment->id }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fw-bold" style="min-width: 100px;">{{ $appointment->time_range_formatted }}</div>
                                <div>
                                    <div class="fw-semibold">{{ $appointment->patient->full_name }}</div>
                                    <div class="small text-secondary">
                                        {{ __('messages.visit_type_'.$appointment->visit_type) }}
                                        &middot; {{ $appointment->doctor->name }}
                                        &middot; {{ $appointment->room->name }}
                                    </div>
                                </div>
                            </div>
                            <span class="badge text-bg-secondary">{{ __('messages.status_'.$appointment->status) }}</span>
                        </button>

                        <x-modal :id="'viewAppointmentModal'.$appointment->id" :title="__('messages.appointment_details')">
                            <dl class="row mb-0">
                                <dt class="col-4">{{ __('messages.patient') }}</dt>
                                <dd class="col-8">{{ $appointment->patient->full_name }} ({{ $appointment->patient->phone }})</dd>
                                <dt class="col-4">{{ __('messages.doctor') }}</dt>
                                <dd class="col-8">{{ $appointment->doctor->name }}</dd>
                                <dt class="col-4">{{ __('messages.room') }}</dt>
                                <dd class="col-8">{{ $appointment->room->name }}</dd>
                                <dt class="col-4">{{ __('messages.treatment') }}</dt>
                                <dd class="col-8">{{ $appointment->treatment?->name ?? '-' }}</dd>
                                <dt class="col-4">{{ __('messages.notes') }}</dt>
                                <dd class="col-8">{{ $appointment->notes ?: '-' }}</dd>
                            </dl>
                            <hr>
                            @can('update', $appointment)
                                <form data-ajax-form method="POST" action="{{ route('appointments.update', $appointment) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="patient_id" value="{{ $appointment->patient_id }}">
                                    <input type="hidden" name="doctor_id" value="{{ $appointment->doctor_id }}">
                                    <input type="hidden" name="room_id" value="{{ $appointment->room_id }}">
                                    <input type="hidden" name="treatment_id" value="{{ $appointment->treatment_id }}">
                                    <input type="hidden" name="visit_type" value="{{ $appointment->visit_type }}">
                                    <input type="hidden" name="appointment_date" value="{{ $appointment->appointment_date->toDateString() }}">
                                    <input type="hidden" name="start_time" value="{{ $appointment->start_time }}">
                                    <input type="hidden" name="end_time" value="{{ $appointment->end_time }}">
                                    <x-form-select name="status" :label="__('messages.status')" :value="$appointment->status" :options="[
                                        'scheduled' => __('messages.status_scheduled'),
                                        'in_progress' => __('messages.status_in_progress'),
                                        'completed' => __('messages.status_completed'),
                                        'cancelled' => __('messages.status_cancelled'),
                                        'no_show' => __('messages.status_no_show'),
                                    ]" />
                                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.update_status') }}</button>
                                </form>
                            @endcan
                            @can('delete', $appointment)
                                <form data-ajax-form method="POST" action="{{ route('appointments.destroy', $appointment) }}" class="mt-2" data-confirm="{{ __('messages.confirm_delete') }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger w-100"><i class="bi bi-trash"></i> {{ __('messages.delete') }}</button>
                                </form>
                            @endcan
                        </x-modal>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @can('create', \App\Models\Appointment::class)
        <x-modal id="createAppointmentModal" :title="__('messages.book_appointment')" size="lg" data-availability-url="{{ route('appointments.availability') }}">
            <form data-ajax-form method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <div class="row">
                    <div class="mb-3 col-md-6 position-relative">
                        <label class="form-label">{{ __('messages.patient') }}</label>
                        <input type="text"
                               id="appointmentPatientSearch"
                               class="form-control"
                               autocomplete="off"
                               placeholder="{{ __('messages.select_patient') }}"
                               value="{{ $bookForName ?? '' }}"
                               required>
                        <input type="hidden" name="patient_id" id="appointmentPatientId" value="{{ $bookFor }}">
                        <div id="appointmentPatientResults"
                             class="list-group position-absolute w-100 shadow-sm"
                             style="z-index: 1060; max-height: 220px; overflow-y: auto; display: none;"></div>
                        <div class="invalid-feedback">{{ __('messages.select_patient_from_list') }}</div>
                    </div>
                    <div class="col-md-6">
                        <x-form-select name="visit_type" :label="__('messages.visit_type')" required
                            :options="['initial_consultation' => __('messages.visit_type_initial_consultation'), 'follow_up' => __('messages.visit_type_follow_up')]" />
                    </div>
                    <div class="col-md-6">
                        <x-form-select name="doctor_id" :label="__('messages.doctor')" required :placeholder="__('messages.select_doctor')"
                            :options="$doctors->pluck('name', 'id')" />
                    </div>
                    <div class="col-md-6">
                        <x-form-select name="room_id" :label="__('messages.room')" required :placeholder="__('messages.select_room')"
                            :options="$rooms->pluck('name', 'id')" />
                    </div>
                    <div class="col-md-6">
                        <x-form-select name="treatment_id" :label="__('messages.treatment_optional')" :placeholder="__('messages.none')"
                            :options="\App\Models\Treatment::active()->orderBy('name')->pluck('name', 'id')" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input type="number" name="session_number" :label="__('messages.session_number_optional')" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input type="date" name="appointment_date" :label="__('messages.date')" :value="$date" required />
                    </div>
                    <div class="col-md-4">
                        <x-form-input type="text" name="start_time" :label="__('messages.start_time')" required data-time-autofill="true" placeholder="{{ __('messages.appointment_time_placeholder') }}" dir="ltr" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input type="text" name="end_time" :label="__('messages.end_time')" required data-time-autofill="true" placeholder="{{ __('messages.appointment_time_placeholder') }}" dir="ltr" />
                    </div>
                    <div class="col-12">
                        <div class="form-text mb-2">{{ __('messages.appointment_time_hint') }}</div>
                        <x-form-textarea name="notes" :label="__('messages.notes')" />
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan

    @push('scripts')
    @if($bookFor)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('createAppointmentModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        });
    </script>
    @endif
    @endpush

<!-- Quick Add Patient Modal (only entry point: the "New Patient" header button) -->
<x-modal id="quickPatientModal" title="{{ __('messages.quick_add_patient') }}">
    <form id="quickPatientForm" method="POST" data-ajax-form action="{{ route('appointments.quick-patient') }}">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('messages.full_name') }} *</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('messages.phone') }} *</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('messages.date_of_birth') }}</label>
                <input type="date" name="date_of_birth" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('messages.age') }}</label>
                <input type="number" name="age" class="form-control" min="0" max="130">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('messages.gender') }}</label>
                <select name="gender" class="form-select">
                    <option value="">{{ __('messages.select') }}</option>
                    <option value="male">{{ __('messages.male') }}</option>
                    <option value="female">{{ __('messages.female') }}</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('messages.address') }}</label>
                <input type="text" name="address" class="form-control">
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('messages.create_patient') }}</button>
        </div>
    </form>
</x-modal>

<!-- rely on the global `data-ajax-form` handler in resources/js/app.js -->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('createAppointmentModal');
    if (!modalEl) return;

    // Ensure any stray backdrop is removed when the modal is hidden
    modalEl.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
    });

    modalEl.addEventListener('hide.bs.modal', function () {
        setTimeout(() => {
            if (!document.querySelector('.modal.show')) {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
            }
        }, 100);
    });
});
</script>
@endpush

@push('scripts')
<script>
// Patient live search for the "Book Appointment" modal
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('appointmentPatientSearch');
    const hiddenInput = document.getElementById('appointmentPatientId');
    const resultsBox = document.getElementById('appointmentPatientResults');
    if (!searchInput || !hiddenInput || !resultsBox) return;

    let debounceTimer = null;
    let lastQuery = '';

    function closeResults() {
        resultsBox.style.display = 'none';
        resultsBox.innerHTML = '';
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[s]));
    }

    function renderResults(patients) {
        if (!patients.length) {
            resultsBox.innerHTML = `<div class="list-group-item text-secondary">{{ __('messages.no_results') }}</div>`;
            resultsBox.style.display = 'block';
            return;
        }
        resultsBox.innerHTML = patients.map(p => `
            <button type="button" class="list-group-item list-group-item-action"
                    data-id="${p.id}" data-name="${escapeHtml(p.full_name)}">
                <div class="fw-semibold">${escapeHtml(p.full_name)}</div>
                <div class="small text-secondary">${escapeHtml(p.phone ?? '')}</div>
            </button>
        `).join('');
        resultsBox.style.display = 'block';
    }

    searchInput.addEventListener('input', function () {
        hiddenInput.value = '';
        searchInput.classList.remove('is-invalid');
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            closeResults();
            return;
        }

        debounceTimer = setTimeout(() => {
            lastQuery = query;
            fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(res => {
                    if (!res.ok) throw new Error(`Search request failed with status ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    // Accept either a plain array or a Laravel API Resource / paginated
                    // response shape like { data: [...] }.
                    const patients = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);
                    if (query === lastQuery) renderResults(patients);
                })
                .catch(err => {
                    console.error('Patient search failed:', err);
                    closeResults();
                });
        }, 300);
    });

    resultsBox.addEventListener('click', function (e) {
        const item = e.target.closest('button[data-id]');
        if (!item) return;
        searchInput.value = item.dataset.name;
        hiddenInput.value = item.dataset.id;
        closeResults();
    });

    document.addEventListener('click', function (e) {
        if (e.target !== searchInput && !resultsBox.contains(e.target)) {
            closeResults();
        }
    });

    const appointmentForm = searchInput.closest('form');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function (e) {
            if (!hiddenInput.value) {
                e.preventDefault();
                e.stopPropagation();
                searchInput.classList.add('is-invalid');
                searchInput.focus();
            }
        });
    }
});
</script>
@endpush

@endsection