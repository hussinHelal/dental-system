@extends('layouts.app')

@section('title', __('messages.staff'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm">
        <h3 class="mb-0">{{ __('messages.staff') }}</h3>
        <div class="d-flex gap-2 flex-grow-1 justify-content-end">
            <x-search-bar :placeholder="__('messages.search_staff')" />
            <a href="{{ route('users.create') }}" class="btn btn-primary text-nowrap">
                <i class="bi bi-plus-lg"></i> {{ __('messages.add_staff') }}
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
                                    <td data-label="{{ __('messages.working_hours') }}">{{ $user->working_hours_summary }}</td>
                                    <td data-label="{{ __('messages.status') }}">
                                        <span class="badge text-bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </td>
                                    <td data-label="">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <form data-ajax-form method="POST" action="{{ route('users.toggle', $user) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary">
                                                {{ $user->is_active ? __('messages.deactivate') : __('messages.activate') }}
                                            </button>
                                        </form>
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
