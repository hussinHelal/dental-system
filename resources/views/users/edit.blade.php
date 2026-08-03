@extends('layouts.app')

@section('title', __('messages.edit_staff'))

@section('content')
   <center>
    <a href="{{ route('users.index') }}" class="btn btn-sm btn-link mb-2 ps-0 shadow-sm">
        <i class="bi bi-arrow-{{ app()->getLocale() === 'ar' ? 'end' : 'start' }} icon-mirror-rtl"></i> {{ __('messages.back') }}
    </a>

    <div class="card zedan-card shadow-sm" style="max-width: 500px;">
        <div class="card-body shadow-sm">
            <h5 class="mb-3">{{ __('messages.edit_staff') }}</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-form-input name="name" :label="__('messages.name')" :value="$user->name" required />
                <x-form-input name="username" :label="__('messages.username')" :value="$user->username" required />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.working_hours') }}</label>
                    <div class="row g-2">
                        @foreach(['sat','sun','mon','tue','wed','thu','fri'] as $day)
                            <div class="col-6">
                                <label class="form-label small">{{ __('messages.day_'.$day) }}</label>
                                <input type="text" name="working_hours[{{ $day }}]" class="form-control"
                                    value="{{ old('working_hours.'.$day, $user->working_hours_for_form[$day] ?? '') }}"
                                    placeholder="{{ __('messages.working_hours_placeholder') }}"
                                    dir="ltr">
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">{{ __('messages.working_hours_hint') }}</div>
                </div>
                <x-form-input type="password" name="password" :label="__('messages.new_password_optional')" />
                <x-form-input type="password" name="password_confirmation" :label="__('messages.confirm_password')" />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.photo') }}</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </div>
    </div>
</center>
@endsection
