@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Roles & Permissions') }}</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> {{ __('New Role') }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Role') }}</th>
                            <th>{{ __('Page Access') }}</th>
                            <th class="text-center">{{ __('Users') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            @php
                                $permissionNames = $role->permissions->pluck('name');
                                $viewCount = $permissionNames->filter(fn ($n) => str_starts_with($n, 'view '))->count();
                                $manageCount = $permissionNames->filter(fn ($n) => str_starts_with($n, 'manage ') && $n !== 'manage roles')->count();
                                $totalPages = count(\App\Support\ManagedPages::slugs());
                                $canManageRoles = $permissionNames->contains('manage roles');
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $role->name }}</span>
                                    @if ($role->is_locked)
                                        <span class="badge bg-secondary ms-2" title="{{ __('This role is protected and cannot be edited or deleted.') }}">
                                            <i class="bi bi-lock-fill"></i> {{ __('Protected') }}
                                        </span>
                                    @endif
                                    @if ($canManageRoles)
                                        <span class="badge bg-primary-subtle text-primary-emphasis border ms-1">
                                            {{ __('Can manage roles') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($manageCount === 0 && $viewCount === 0)
                                        <span class="text-muted small">{{ __('No page access') }}</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success-emphasis border">
                                            {{ __('Manage') }}: {{ $manageCount }} / {{ $totalPages }}
                                        </span>
                                        <span class="badge bg-info-subtle text-info-emphasis border">
                                            {{ __('View only') }}: {{ max(0, $viewCount - $manageCount) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $role->users_count }}</td>
                                <td class="text-end">
                                    @unless ($role->is_locked)
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i> {{ __('Edit') }}
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteRoleModal{{ $role->id }}">
                                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                                        </button>

                                        <x-modal id="deleteRoleModal{{ $role->id }}" :centered="true">
                                            <x-slot name="title">{{ __('Delete Role') }}</x-slot>
                                            <p class="mb-0">
                                                {{ __('Are you sure you want to delete the ":role" role? This cannot be undone.', ['role' => $role->name]) }}
                                            </p>
                                            <x-slot name="footer">
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                                                </form>
                                            </x-slot>
                                        </x-modal>
                                    @else
                                        <span class="text-muted small">{{ __('Locked') }}</span>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
