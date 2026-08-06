@extends('layouts.app')

@section('title', __('messages.add_staff'))

@section('content')
  <div class="d-flex flex-column align-items-center">
    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary mb-2 shadow-sm">
        <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }} icon-mirror-rtl"></i> {{ __('messages.back') }}
    </a>

    <div class="card zedan-card shadow-sm w-100" style="max-width: 560px;">
        <div class="card-body shadow-sm">
            <div class="d-flex flex-column align-items-center text-center gap-2 mb-3">
                <div class="rounded-circle bg-primary-subtle p-2">
                    <i class="bi bi-person-plus-fill text-primary"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ __('messages.add_staff') }}</h5>
                    <p class="text-muted small mb-0">{{ __('messages.create_staff_hint') }}</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                @csrf
                <x-form-input name="name" :label="__('messages.name')" required />
                <x-form-input name="username" :label="__('messages.username')" required />
                <x-form-select name="role" :label="__('messages.role')" :value="old('role', \App\Models\User::ROLE_RECEPTIONIST)" :options="[\App\Models\User::ROLE_RECEPTIONIST => __('messages.receptionist'), \App\Models\User::ROLE_DOCTOR => __('messages.doctor')]" required />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.working_hours') }}</label>
                    <textarea name="working_hours" class="form-control" rows="3" placeholder="{{ __('messages.working_hours_placeholder') }}" dir="ltr">{{ old('working_hours') }}</textarea>
                    <div class="form-text">{{ __('messages.working_hours_hint') }}</div>
                </div>
                <x-form-input type="password" name="password" :label="__('messages.password')" required />
                <x-form-input type="password" name="password_confirmation" :label="__('messages.confirm_password')" required />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.photo') }}</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="create_another" value="1" id="createAnother" @checked(old('create_another'))>
                    <label class="form-check-label" for="createAnother">{{ __('messages.create_another') }}</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check2-circle me-2"></i> {{ __('messages.save') }}
                </button>
            </form>
        </div>
    </div>
</center>
@endsection
