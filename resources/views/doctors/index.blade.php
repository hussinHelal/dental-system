@extends('layouts.app')

@section('title', __('messages.doctors'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm">
        <h3 class="mb-0">{{ __('messages.doctors') }}</h3>
        <div class="d-flex gap-2 flex-grow-1 justify-content-end">
            <x-search-bar :placeholder="__('messages.search_doctors')" />
            @can('create', \App\Models\Doctor::class)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDoctorModal">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.add_doctor') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($doctors->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.specialty') }}</th>
                                <th>{{ __('messages.phone') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctors as $doctor)
                                <tr>
                                    <td data-label="{{ __('messages.name') }}">
                                        <img src="{{ $doctor->photoUrl() }}" width="32" height="32" class="rounded-circle me-2" alt="">
                                        {{ $doctor->name }}
                                    </td>
                                    <td data-label="{{ __('messages.specialty') }}">{{ $doctor->specialty }}</td>
                                    <td data-label="{{ __('messages.phone') }}">{{ $doctor->phone }}</td>
                                    <td data-label="{{ __('messages.status') }}">
                                        <span class="badge text-bg-{{ $doctor->is_active ? 'success' : 'secondary' }}">
                                            {{ $doctor->is_active ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </td>
                                    <td data-label="">
                                        @can('update', $doctor)
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDoctorModal{{ $doctor->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan
                                        @can('delete', $doctor)
                                            @if($doctor->is_active)
                                                <form data-ajax-form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_deactivate') }}')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="{{ __('messages.deactivate') }}"><i class="bi bi-slash-circle"></i></button>
                                                </form>
                                            @else
                                                <form data-ajax-form method="POST" action="{{ route('doctors.reactivate', $doctor) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success" title="{{ __('messages.activate') }}"><i class="bi bi-check-circle"></i></button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>

                                @can('update', $doctor)
                                    <x-modal :id="'editDoctorModal'.$doctor->id" :title="__('messages.edit_doctor')">
                                        <form data-ajax-form method="POST" action="{{ route('doctors.update', $doctor) }}" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <x-form-input name="name" :label="__('messages.name')" :value="$doctor->name" required />
                                            <x-form-input name="specialty" :label="__('messages.specialty')" :value="$doctor->specialty" />
                                            <x-form-input name="phone" :label="__('messages.phone')" :value="$doctor->phone" />
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.photo') }}</label>
                                                <input type="file" name="photo" class="form-control" accept="image/*">
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
                                        </form>
                                    </x-modal>
                                @endcan
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @can('create', \App\Models\Doctor::class)
        <x-modal id="createDoctorModal" :title="__('messages.add_doctor')">
            <form data-ajax-form method="POST" action="{{ route('doctors.store') }}" enctype="multipart/form-data">
                @csrf
                <x-form-input name="name" :label="__('messages.name')" required />
                <x-form-input name="specialty" :label="__('messages.specialty')" />
                <x-form-input name="phone" :label="__('messages.phone')" />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.photo') }}</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan
@endsection
