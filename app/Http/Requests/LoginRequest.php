<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('username', 'password');
        $remember = $this->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            activity('auth')
                ->withProperties(['ip' => $this->ip(), 'attempted_username' => $this->input('username')])
                ->log('failed_login');

            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        if (! Auth::user()->is_active) {
            activity('auth')
                ->causedBy(Auth::user())
                ->withProperties(['ip' => $this->ip()])
                ->log('blocked_login_deactivated');

            Auth::logout();

            throw ValidationException::withMessages([
                'username' => __('messages.account_deactivated'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        // return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
        return Str::transliterate(
        Str::lower($this->string('username')).'|'.$this->ip().'|'.md5($this->userAgent() ?? '')
    );
    }
}
