@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm p-3 rounded-4 zedan-page-header">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary-subtle p-2">
                <i class="bi bi-shield-lock-fill text-primary"></i>
            </div>
            <h3 class="mb-0">{{ __('Edit Role') }}: {{ $role->name }}</h3>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                @include('roles._form')

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
