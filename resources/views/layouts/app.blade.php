<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-bs-theme="{{ auth()->user()->theme ?? 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} &middot; @yield('title', __('messages.dashboard'))</title>

    @if(app()->getLocale() === 'ar')
        @vite('resources/css/app-rtl.css')
    @else
        @vite('resources/css/app.css')
    @endif

    <script>
        // Translated strings app.js needs for alerts/confirms it shows
        // without a server round-trip (network errors, generic
        // failures) - kept here so nothing in the JS is hardcoded to
        // English.
        window.i18n = @json([
            'somethingWentWrong' => __('messages.js_something_went_wrong'),
            'networkError' => __('messages.js_network_error'),
        ]);
    </script>

    @vite(['resources/js/app.js'])
</head>

@stack('styles')    
@stack('scripts')
<body>
    <nav class="navbar navbar-expand-lg navbar-dark zedan-navbar sticky-top">
        <div class="container-fluid">
            <button class="btn btn-link text-white d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#zedanSidebar">
                <i class="bi bi-list fs-3"></i>
            </button>

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-clipboard2-pulse"></i> {{ config('app.name') }}
            </a>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <button class="btn btn-sm btn-outline-light" data-theme-toggle type="button" title="{{ __('messages.toggle_theme') }}">
                    <i class="bi bi-moon-stars"></i>
                </button>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                        {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-locale-switch="en">English</a></li>
                        <li><a class="dropdown-item" href="#" data-locale-switch="ar">العربية</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" width="24" height="24" class="rounded-circle" data-image-preview style="cursor: pointer;">
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> {{ __('messages.profile') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> {{ __('messages.logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        @php
            $navItems = [
                ['route' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => __('messages.dashboard')],
                ['route' => 'appointments.index', 'icon' => 'bi-calendar-check', 'label' => __('messages.appointments')],
                ['route' => 'patients.index', 'icon' => 'bi-people', 'label' => __('messages.patients')],
                ['route' => 'doctors.index', 'icon' => 'bi-person-badge', 'label' => __('messages.doctors')],
                ['route' => 'rooms.index', 'icon' => 'bi-door-open', 'label' => __('messages.rooms')],
                ['route' => 'treatments.index', 'icon' => 'bi-clipboard2-pulse', 'label' => __('messages.treatments')],
                ['route' => 'inventory.index', 'icon' => 'bi-box-seam', 'label' => __('messages.inventory')],
                ['route' => 'backups.index', 'icon' => 'bi-cloud-arrow-down', 'label' => __('messages.backups')],
            ];
            if (auth()->user()->isDoctor()) {
                $navItems[] = ['route' => 'users.index', 'icon' => 'bi-person-gear', 'label' => __('messages.staff')];
                $navItems[] = ['route' => 'activity-log.index', 'icon' => 'bi-clock-history', 'label' => __('messages.activity_log')];
            }
        @endphp

        <div class="offcanvas-lg offcanvas-start zedan-sidebar p-3" tabindex="-1" id="zedanSidebar">
            <div class="offcanvas-header d-lg-none">
                <h5 class="offcanvas-title">{{ __('messages.menu') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#zedanSidebar"></button>
            </div>
            <div class="offcanvas-body d-block">
                <ul class="nav nav-pills flex-column">
                    @foreach($navItems as $item)
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center {{ request()->routeIs($item['route']) || request()->routeIs(str($item['route'])->before('.').'.*') ? 'active' : '' }}"
                               href="{{ route($item['route']) }}">
                                <i class="bi {{ $item['icon'] }} me-2 flex-shrink-0"></i> <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <main class="flex-grow-1 p-3 p-md-4 d-flex flex-column">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="flex-grow-1">
                @yield('content')
            </div>

            <footer class="text-center text-muted small fw-normal mt-3 pt-2 border-top opacity-90">
                Created by Hussin Helal © 2026
            </footer>
        </main>
    </div>

    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="" id="imagePreviewModalImg" class="img-fluid rounded shadow-sm">
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
