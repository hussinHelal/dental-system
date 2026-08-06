@extends('layouts.app')

@section('title', __('messages.patients'))

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm p-3 rounded-4 zedan-page-header">
        <h3 class="mb-0">{{ __('messages.patients') }}</h3>
        <div class="d-flex gap-2 flex-grow-1 justify-content-end">
            <x-search-bar :placeholder="__('messages.search_patients')" />
            @can('create', \App\Models\Patient::class)
                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createPatientModal">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.add_patient') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($patients->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.phone') }}</th>
                                <th>{{ __('messages.age') }}</th>
                                <th>{{ __('messages.gender') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td data-label="{{ __('messages.name') }}">
                                        <img src="{{ $patient->photoUrl() }}" width="32" height="32" class="rounded-circle me-2" alt="{{ $patient->full_name }}" data-image-preview style="cursor: pointer;">
                                        {{ $patient->full_name }}
                                    </td>
                                    <td data-label="{{ __('messages.phone') }}">{{ $patient->phone }}</td>
                                    <td data-label="{{ __('messages.age') }}">{{ $patient->display_age ?? '-' }}</td>
                                    <td data-label="{{ __('messages.gender') }}">{{ $patient->gender ? __('messages.gender_'.$patient->gender) : '-' }}</td>
                                    <td data-label="">
                                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> {{ __('messages.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">{{ $patients->links() }}</div>

    @can('create', \App\Models\Patient::class)
        <x-modal id="createPatientModal" :title="__('messages.add_patient')">
            <form data-ajax-form method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data">
                @csrf
                <x-form-input name="full_name" :label="__('messages.full_name')" required />
                <x-form-input name="phone" :label="__('messages.phone')" required />
                <x-form-input type="date" name="date_of_birth" :label="__('messages.date_of_birth')" />
                <x-form-input type="number" name="age" :label="__('messages.age_if_dob_unknown')" />
                <x-form-select name="gender" :label="__('messages.gender')" :options="['male' => __('messages.gender_male'), 'female' => __('messages.gender_female')]" :placeholder="__('messages.select_gender')" />
                <x-form-textarea name="address" :label="__('messages.address')" />
                <x-form-textarea name="notes" :label="__('messages.notes')" />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.photo') }}</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan

 

@endsection
