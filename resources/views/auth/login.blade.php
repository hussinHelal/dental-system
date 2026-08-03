<x-guest-layout>
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="username" class="form-label">{{ __('messages.username') }}</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('messages.password') }}</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label for="remember" class="form-check-label">{{ __('messages.remember_me') }}</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __('messages.login') }}</button>
    </form>
</x-guest-layout>
