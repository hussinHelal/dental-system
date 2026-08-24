<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'light') : 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} &middot; @yield('title', __('messages.error'))</title>

    @if(app()->getLocale() === 'ar')
        @vite('resources/css/app-rtl.css')
    @else
        @vite('resources/css/app.css')
    @endif
</head>
<body class="zedan-error-body">

    <nav class="navbar navbar-dark zedan-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-clipboard2-pulse"></i> {{ config('app.name') }}
            </a>
        </div>
    </nav>

    <main class="zedan-error-main d-flex align-items-center justify-content-center">
        <div class="text-center zedan-error-card">
            <div class="zedan-error-code">@yield('code')</div>
            <h1 class="h4 fw-semibold mb-3">@yield('heading')</h1>
            <p class="text-muted mb-4">@yield('message')</p>

            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <button type="button" class="btn btn-outline-secondary" onclick="if (document.referrer) { history.back(); } else { window.location.href = '{{ url('/') }}'; }">
                    <i class="bi {{ app()->getLocale() === 'ar' ? 'bi-arrow-right' : 'bi-arrow-left' }}"></i>
                    {{ __('messages.go_back') }}
                </button>
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="bi bi-house-door"></i>
                    {{ __('messages.go_home') }}
                </a>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted small fw-normal py-3 opacity-75">
        {{ config('app.name') }} &middot; © {{ date('Y') }}
    </footer>

</body>
</html>