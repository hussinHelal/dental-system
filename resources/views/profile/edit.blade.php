@extends('layouts.app')

@section('title', __('messages.profile'))

@section('content')
    <h3 class="mb-3">{{ __('messages.profile') }}</h3>

    <div class="card zedan-card shadow-sm" style="max-width: 500px;">
        <div class="card-body shadow-sm">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="text-center mb-3">
                <img src="{{ $user->avatarUrl() }}" width="80" height="80" class="rounded-circle" alt="">
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <x-form-input name="name" :label="__('messages.name')" :value="$user->name" required />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.photo') }}</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <hr>
                <p class="text-secondary small">{{ __('messages.change_password_hint') }}</p>
                <x-form-input type="password" name="current_password" :label="__('messages.current_password')" />
                <x-form-input type="password" name="password" :label="__('messages.new_password_optional')" />
                <x-form-input type="password" name="password_confirmation" :label="__('messages.confirm_password')" />
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </div>
    </div>
@endsection
