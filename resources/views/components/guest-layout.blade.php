<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} &middot; {{ __('messages.login') }}</title>

    @if(app()->getLocale() === 'ar')
        @vite('resources/css/app-rtl.css')
    @else
        @vite('resources/css/app.css')
    @endif
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, var(--zedan-primary), var(--zedan-accent));">
    <div class="position-absolute top-0 end-0 p-3">
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); fetch('/locale',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({locale:'en'})}).finally(()=>location.reload());">English</a></li>
                <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); fetch('/locale',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({locale:'ar'})}).finally(()=>location.reload());">العربية</a></li>
            </ul>
        </div>
    </div>

    <div class="card zedan-card shadow-lg" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-clipboard2-pulse text-primary" style="font-size: 2.5rem;"></i>
                <h4 class="mt-2 mb-0">{{ config('app.name') }}</h4>
                <p class="text-secondary small">{{ __('messages.login_subtitle') }}</p>
            </div>

            {{ $slot }}
        </div>
    </div>
</body>
</html>
