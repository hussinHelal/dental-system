@extends('layouts.app')

@section('title', $patient->full_name)

@section('content')
@php
$toothStatusColors = [
    'healthy' => 'success',
    'decayed' => 'danger',
    'filled' => 'primary',
    'crown' => 'warning',
    'root_canal' => 'info',
    'extracted' => 'secondary',
    'missing' => 'dark',
    'implant' => 'success',
    'fractured' => 'danger',
    'abscess' => 'danger',
    'braces' => 'primary',
    'veneer' => 'warning',
    'wisdom' => 'secondary',
];
@endphp
    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-primary mb-2 shadow-sm">
        <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }} icon-mirror-rtl me-1"></i>
        {{ __('messages.back') }}
    </a>

    {{-- Patient Header Card --}}
    <div class="card zedan-card mb-4 shadow-sm">
        <div class="card-body shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div class="d-flex gap-3 align-items-center">
                    <img src="{{ $patient->photoUrl() }}" width="64" height="64" class="rounded-circle" alt="{{ $patient->full_name }}" data-image-preview style="cursor: pointer;">
                    <div>
                        <h4 class="mb-1">{{ $patient->full_name }}</h4>
                        <div class="text-secondary">
                            {{ $patient->phone }}
                            @if($patient->display_age) &middot; {{ __('messages.age') }}: {{ $patient->display_age }} @endif
                            @if($patient->gender) &middot; {{ __('messages.gender_'.$patient->gender) }} @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('appointments.index', ['date' => now()->toDateString() , 'book_for' => $patient->id ]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-calendar-plus"></i> {{ __('messages.book_follow_up') }}
                    </a>
                    @can('update', $patient)
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPatientModal">
                            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                        </button>
                    @endcan
                    @can('delete', $patient)
                        <form data-ajax-form method="POST" action="{{ route('patients.destroy', $patient) }}" data-confirm="{{ __('messages.confirm_delete') }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    @endcan
                </div>
            </div>

            @if($patient->address || $patient->notes)
                <hr>
                <div class="row g-3">
                    @if($patient->address)
                        <div class="col-md-6">
                            <div class="text-secondary small">{{ __('messages.address') }}</div>
                            <div>{{ $patient->address }}</div>
                        </div>
                    @endif
                    @if($patient->notes)
                        <div class="col-md-6">
                            <div class="text-secondary small">{{ __('messages.notes') }}</div>
                            <div>{{ $patient->notes }}</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-grid-3x3-gap"></i> {{ __('messages.tooth_chart') }}</h5>
        <a href="{{ route('patients.tooth-chart', $patient) }}" class="btn btn-sm btn-primary">
            {{ __('messages.open_chart') }}
        </a>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-1 justify-content-center">
            @php $toothMap = $patient->toothRecords->keyBy('tooth_number'); @endphp
            @foreach(range(1, 32) as $num)
                @php $record = $toothMap[$num] ?? null; @endphp
                <span class="badge bg-{{ [
                    'healthy' => 'success', 'decayed' => 'danger', 'filled' => 'primary',
                    'crown' => 'warning', 'root_canal' => 'info', 'extracted' => 'secondary',
                    'missing' => 'dark', 'implant' => 'success', 'fractured' => 'danger',
                    'abscess' => 'danger', 'braces' => 'primary', 'veneer' => 'warning'
                ][$record?->status ?? 'healthy'] }}">
                    {{ $num }}
                </span>
            @endforeach
        </div>
    </div>
</div>

            {{-- X-Ray Section --}}
            @if($patient->xray_photo)
            <div class="card zedan-card mb-4">
                <div class="card-header bg-transparent">
                    <i class="bi bi-x-ray me-2"></i>{{ __('messages.xray_photo') }}
                </div>
                <div class="card-body text-center">
                    <a href="{{ $patient->xrayPhotoUrl() }}" target="_blank" class="d-inline-block position-relative">
                        <img src="{{ $patient->xrayPhotoUrl() }}" class="img-fluid rounded shadow-sm xray-image" alt="{{ __('messages.xray_photo') }}" style="max-height: 400px;">
                        <div class="position-absolute top-50 start-50 translate-middle bg-dark bg-opacity-50 rounded px-2 py-1 text-white small">
                            <i class="bi bi-zoom-in"></i> {{ __('messages.click_to_enlarge') }}
                        </div>
                    </a>
                </div>
            </div>
            @endif

            {{-- Appointments (PAGINATED) --}}
            <div class="card zedan-card mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar-week me-2"></i>{{ __('messages.appointments') }}</span>
                    <span class="badge bg-secondary">{{ $appointments->total() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($appointments->isEmpty())
                        <x-empty-state />
                    @else
                        <div class="table-responsive">
                            <table class="table zedan-responsive-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.date') }}</th>
                                        <th>{{ __('messages.doctor') }}</th>
                                        <th>{{ __('messages.room') }}</th>
                                        <th>{{ __('messages.treatment') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                        <tr>
                                            <td data-label="{{ __('messages.date') }}">
                                                {{ $appointment->appointment_date->toDateString() }} {{ $appointment->time_range_formatted }}
                                                @if($appointment->session_number)
                                                    <span class="badge text-bg-info">{{ __('messages.session') }} {{ $appointment->session_number }}</span>
                                                @endif
                                            </td>
                                            <td data-label="{{ __('messages.doctor') }}">{{ $appointment->doctor->name }}</td>
                                            <td data-label="{{ __('messages.room') }}">{{ $appointment->room->name }}</td>
                                            <td data-label="{{ __('messages.treatment') }}">{{ $appointment->treatment?->name ?? '-' }}</td>
                                            <td data-label="{{ __('messages.status') }}">
                                                <span class="badge text-bg-secondary">{{ __('messages.status_'.$appointment->status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-2">{{ $appointments->links() }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Payments --}}
        <div class="col-lg-5 shadow-sm">
            <div class="card zedan-card mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-credit-card me-2"></i>{{ __('messages.payments') }}</span>
                    @can('create', \App\Models\Payment::class)
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                            <i class="bi bi-plus-lg"></i> {{ __('messages.record_payment') }}
                        </button>
                    @endcan
                </div>
                <div class="card-body shadow-sm">
                    <div class="row text-center mb-3 g-2">
                        <div class="col-4">
                            <div class="text-secondary small">{{ __('messages.total_cost') }}</div>
                            <div class="fw-bold">{{ number_format($summary['total_cost'], 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-secondary small">{{ __('messages.paid') }}</div>
                            <div class="fw-bold text-success">{{ number_format($summary['paid'], 2) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-secondary small">{{ __('messages.remaining') }}</div>
                            <div class="fw-bold text-danger">{{ number_format($summary['remaining'], 2) }}</div>
                        </div>
                    </div>

                    @if($payments->isEmpty())
                        <x-empty-state />
                    @else
                        @foreach($payments as $payment)
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $payment->treatment->name }}</div>
                                        <div class="small text-secondary">
                                            {{ __('messages.total') }}: {{ number_format($payment->total_amount, 2) }}
                                            &middot; {{ __('messages.remaining') }}: {{ number_format($payment->remaining_balance, 2) }}
                                        </div>
                                    </div>
                                    <span class="badge text-bg-{{ $payment->statusBadgeClass() }}">
                                        {{ __('messages.payment_status_'.$payment->status) }}
                                    </span>
                                </div>

                                @if($payment->payment_type === 'installment')
                                    <div class="mt-2">
                                        @foreach($payment->installments as $installment)
                                            <div class="small text-secondary">
                                                {{ $installment->paid_date->toDateString() }} - {{ number_format($installment->amount, 2) }}
                                            </div>
                                        @endforeach
                                        @can('update', $payment)
                                            @if($payment->remaining_balance > 0)
                                                <button class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal" data-bs-target="#addInstallmentModal{{ $payment->id }}">
                                                    {{ __('messages.add_installment') }}
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                @endif

                                @can('delete', $payment)
                                    <form data-ajax-form method="POST" action="{{ route('payments.destroy', $payment) }}" class="mt-2" data-confirm="{{ __('messages.confirm_delete') }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> {{ __('messages.delete') }}</button>
                                    </form>
                                @endcan
                            </div>

                            @if($payment->payment_type === 'installment')
                                <x-modal :id="'addInstallmentModal'.$payment->id" :title="__('messages.add_installment')">
                                    <form data-ajax-form method="POST" action="{{ route('payments.installments.store', $payment) }}">
                                        @csrf
                                        <x-form-input type="number" step="0.01" name="amount" :label="__('messages.amount')" required />
                                        <x-form-input type="date" name="paid_date" :label="__('messages.date')" :value="now()->toDateString()" required />
                                        <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
                                    </form>
                                </x-modal>
                            @endif
                        @endforeach
                        <div class="mt-2">{{ $payments->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Patient Modal (includes X-ray + Tooth Chart) --}}
    @can('update', $patient)
        <x-modal id="editPatientModal" :title="__('messages.edit_patient')">
            <form data-ajax-form method="POST" action="{{ route('patients.update', $patient) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <x-form-input name="full_name" :label="__('messages.full_name')" :value="$patient->full_name" required />
                <x-form-input name="phone" :label="__('messages.phone')" :value="$patient->phone" required />
                <x-form-input type="date" name="date_of_birth" :label="__('messages.date_of_birth')" :value="optional($patient->date_of_birth)->toDateString()" />
                <x-form-input type="number" name="age" :label="__('messages.age_if_dob_unknown')" :value="$patient->age" />
                <x-form-select name="gender" :label="__('messages.gender')" :value="$patient->gender" :options="['male' => __('messages.gender_male'), 'female' => __('messages.gender_female')]" :placeholder="__('messages.select_gender')" />
                <x-form-textarea name="address" :label="__('messages.address')" :value="$patient->address" />
                <x-form-textarea name="notes" :label="__('messages.notes')" :value="$patient->notes" />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.photo') }}</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.xray_photo') }}</label>
                    <input type="file" name="xray_photo" class="form-control" accept="image/*">
                    @if($patient->xray_photo)
                        <div class="form-text text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ __('messages.xray_replace_hint') }}
                        </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan

    {{-- Edit Tooth Chart Modal --}}
    @can('update', $patient)
        <x-modal id="editToothModal" :title="__('messages.edit_tooth_chart')" size="lg">
            <form data-ajax-form method="POST" action="{{ route('patients.update', $patient) }}">
                @csrf @method('PUT')
                <div class="tooth-chart-editor">
                    <div class="tooth-arch upper">
                        @foreach(range(1, 16) as $num)
                            @php $status = $patient->tooth_chart[$num] ?? 'healthy'; @endphp
                            <div class="tooth-editor" data-tooth="{{ $num }}">
                                <div class="tooth-body tooth-{{ $status }}" onclick="cycleToothStatus(this, {{ $num }})" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')cycleToothStatus(this,{{ $num }})">
                                    <span class="tooth-num">{{ $num }}</span>
                                </div>
                                <input type="hidden" name="tooth_chart[{{ $num }}]" value="{{ $status }}" id="tooth-input-{{ $num }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="tooth-arch lower">
                        @foreach(range(17, 32) as $num)
                            @php $status = $patient->tooth_chart[$num] ?? 'healthy'; @endphp
                            <div class="tooth-editor" data-tooth="{{ $num }}">
                                <div class="tooth-body tooth-{{ $status }}" onclick="cycleToothStatus(this, {{ $num }})" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')cycleToothStatus(this,{{ $num }})">
                                    <span class="tooth-num">{{ $num }}</span>
                                </div>
                                <input type="hidden" name="tooth_chart[{{ $num }}]" value="{{ $status }}" id="tooth-input-{{ $num }}">
                            </div>
                        @endforeach
                    </div>
                </div>
                @php
                    $legendStatuses = [
                        'healthy', 'decayed', 'filled', 'crown', 'root_canal',
                        'extracted', 'missing', 'implant', 'fractured', 'abscess',
                        'braces', 'veneer',
                    ];
                @endphp
                <div class="tooth-legend mt-3 d-flex flex-wrap gap-2 justify-content-center small">
                    @foreach($legendStatuses as $ls)
                        <span class="legend-item">
                            <span class="legend-dot tooth-{{ $ls }}"></span>
                            {{ trans('messages.tooth_status_'.$ls, [], 'en') }}
                        </span>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan

    @can('create', \App\Models\Payment::class)
        <x-modal id="addPaymentModal" :title="__('messages.record_payment')">
            <form data-ajax-form method="POST" action="{{ route('payments.store', $patient) }}">
                @csrf
                <x-form-select name="treatment_id" :label="__('messages.treatment')" required :placeholder="__('messages.select_treatment')"
                    :options="\App\Models\Treatment::active()->orderBy('name')->pluck('name', 'id')" />
                <x-form-select name="payment_type" :label="__('messages.payment_type')" required
                    :options="['paid_now' => __('messages.paid_now'), 'pay_later' => __('messages.pay_later'), 'installment' => __('messages.installment')]" />
                <x-form-input type="number" step="0.01" name="total_amount" :label="__('messages.total_amount')" required />
                <x-form-input type="number" step="0.01" name="first_installment_amount" :label="__('messages.first_installment_amount')" />
                <x-form-input type="date" name="due_date" :label="__('messages.due_date')" />
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan
@endsection

@push('styles')
<style>
.tooth-chart-wrapper, .tooth-chart-editor {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
    padding: 8px;
}
.tooth-arch {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 5px;
    max-width: 100%;
    padding: 8px;
    border-radius: 8px;
}
.tooth-arch.upper {
    border-bottom: 2px solid var(--bs-border-color);
    padding-bottom: 12px;
}
.tooth-arch.lower {
    padding-top: 12px;
}
.tooth, .tooth-editor {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 38px;
}
.tooth-body {
    width: 34px;
    height: 34px;
    border-radius: 50% 50% 45% 45%;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: default;
    transition: all .2s ease;
    background: #fff;
    position: relative;
}
.tooth-editor .tooth-body {
    cursor: pointer;
}
.tooth-editor .tooth-body:hover,
.tooth-editor .tooth-body:focus {
    transform: scale(1.2);
    box-shadow: 0 3px 8px rgba(0,0,0,.2);
    outline: none;
    z-index: 1;
}
.tooth-num {
    font-size: 10px;
    font-weight: 700;
    color: #495057;
    pointer-events: none;
    line-height: 1;
}
/* Status Colors */
.tooth-healthy   { background: #fff; border-color: #adb5bd; }
.tooth-decayed   { background: #dc3545; border-color: #b02a37; }
.tooth-decayed .tooth-num { color: #fff; }
.tooth-treated   { background: #0d6efd; border-color: #0a58ca; }
.tooth-treated .tooth-num { color: #fff; }
.tooth-missing   { background: #6c757d; border-color: #495057; }
.tooth-missing .tooth-num { color: #fff; }
.tooth-root_canal{ background: #198754; border-color: #146c43; }
.tooth-root_canal .tooth-num { color: #fff; }
.tooth-crown     { background: #ffc107; border-color: #cc9a06; }
.tooth-crown .tooth-num { color: #212529; }

.tooth-legend { gap: 10px; }
.legend-item { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; }
.legend-dot {
    width: 16px; height: 16px;
    border-radius: 50% 50% 45% 45%;
    border: 1px solid #adb5bd;
    display: inline-block;
    flex-shrink: 0;
}
.xray-image {
    border: 1px solid var(--bs-border-color);
    background: #000;
    transition: transform .2s;
}
.xray-image:hover {
    transform: scale(1.02);
}
/* RTL support for tooth chart */
[dir="rtl"] .tooth-arch {
    flex-direction: row-reverse;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';
    const TOOTH_STATUSES = ['healthy', 'decayed', 'treated', 'missing', 'root_canal', 'crown'];
    const STATUS_LABELS = {
        healthy: '{{ trans("messages.tooth_status_healthy", [], "en") }}',
        decayed: '{{ trans("messages.tooth_status_decayed", [], "en") }}',
        treated: '{{ trans("messages.tooth_status_treated", [], "en") }}',
        missing: '{{ trans("messages.tooth_status_missing", [], "en") }}',
        root_canal: '{{ trans("messages.tooth_status_root_canal", [], "en") }}',
        crown: '{{ trans("messages.tooth_status_crown", [], "en") }}'
    };

    window.cycleToothStatus = function(el, num) {
        const input = document.getElementById('tooth-input-' + num);
        if (!input) return;

        let current = input.value || 'healthy';
        let idx = TOOTH_STATUSES.indexOf(current);
        if (idx === -1) idx = 0;
        let next = TOOTH_STATUSES[(idx + 1) % TOOTH_STATUSES.length];

        input.value = next;
        TOOTH_STATUSES.forEach(s => el.classList.remove('tooth-' + s));
        el.classList.add('tooth-' + next);

        // Update tooltip/title if needed
        const toothName = el.closest('.tooth-editor')?.getAttribute('data-tooth') || num;
        el.setAttribute('title', 'Tooth #' + toothName + ' — ' + (STATUS_LABELS[next] || next));
    };
})();
</script>
@endpush
