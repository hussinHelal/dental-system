@extends('layouts.app')

@section('title', $patient->full_name)

@section('content')
    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-primary mb-2 ps-0  shadow-sm">
        <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }} icon-mirror-rtl"></i> {{ __('messages.back') }}
    </a>

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

    <div class="row g-4 shadow-sm">
        <div class="col-lg-7">
            <div class="card zedan-card mb-4">
                <div class="card-header bg-transparent">{{ __('messages.appointments') }}</div>
                <div class="card-body p-0">
                    @if($patient->appointments->isEmpty())
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
                                    @foreach($patient->appointments as $appointment)
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
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5 shadow-sm">
            <div class="card zedan-card mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <span>{{ __('messages.payments') }}</span>
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

                    @if($patient->payments->isEmpty())
                        <x-empty-state />
                    @else
                        @foreach($patient->payments as $payment)
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
                    @endif
                </div>
            </div>
        </div>
    </div>

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
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
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
