@extends('layouts.app')

@section('title', __('messages.staff'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm p-3 rounded-4 zedan-page-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary-subtle p-2">
                <i class="bi bi-people-fill text-primary"></i>
            </div>
            <div>
                <h3 class="mb-0">{{ __('messages.staff') }}</h3>
                <p class="text-muted small mb-0">{{ __('messages.manage_staff_hint') }}</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-grow-1 justify-content-end">
            <x-search-bar :placeholder="__('messages.search_staff')" />
            <a href="{{ route('users.create') }}" class="btn btn-primary text-nowrap">
                <i class="bi bi-person-plus-fill me-2"></i> {{ __('messages.add_staff') }}
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-primary text-nowrap">
                <i class="bi bi-people me-2"></i> {{ __('messages.roles') }}
            </a>
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($staff->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.username') }}</th>
                                <th>{{ __('messages.role') }}</th>
                                <th>{{ __('messages.working_hours') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $user)
                                <tr>
                                    <td data-label="{{ __('messages.name') }}">
                                        <img src="{{ $user->avatarUrl() }}" width="32" height="32" class="rounded-circle me-2" alt="{{ $user->name }}" data-image-preview style="cursor: pointer;">
                                        {{ $user->name }}
                                    </td>
                                    <td data-label="{{ __('messages.username') }}">{{ $user->username }}</td>
                                    <td data-label="{{ __('messages.role') }}">
                                        <span class="badge text-bg-{{ $user->isDoctor() ? 'primary' : 'info' }}">
                                            {{ $user->isDoctor() ? __('messages.doctor') : __('messages.receptionist') }}
                                        </span>
                                    </td>
                                    <td data-label="{{ __('messages.working_hours') }}">{{ $user->working_hours_summary }}</td>
                                    <td data-label="{{ __('messages.status') }}">
                                        <span class="badge text-bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </td>
                                    <td data-label="">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form data-ajax-form method="POST" action="{{ route('users.toggle', $user) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary" title="{{ $user->is_active ? __('messages.deactivate') : __('messages.activate') }}">
                                                    <i class="bi {{ $user->is_active ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
