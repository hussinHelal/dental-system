@extends('layouts.app')

@section('title', __('dental_labs.labs_title'))

@section('content')
    <div class="container-fluid px-0">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle p-2">
                    <i class="bi bi-building-gear text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ __('dental_labs.labs_title') }}</h3>
                </div>
            </div>

            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end" role="search">
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                    <input id="labSearch" type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('dental_labs.search') }}" maxlength="100" autocomplete="off">
                </div>

                <button class="btn btn-outline-primary text-nowrap" type="submit">{{ __('dental_labs.filter') }}</button>

                @if (request()->filled('search'))
                    <a class="btn btn-outline-secondary" href="{{ route('dental-labs.index') }}">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif

                <a href="{{ route('lab-cases.index') }}" class="btn btn-outline-primary text-nowrap">
                    <i class="bi bi-box-seam me-1"></i>{{ __('dental_labs.view_cases') }}
                </a>

                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createLabModal">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('dental_labs.add_lab') }}
                </button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('dental_labs.name') }}</th>
                                <th>{{ __('dental_labs.phone') }}</th>
                                <th>{{ __('dental_labs.address') }}</th>
                                <th>{{ __('dental_labs.cases_count') }}</th>
                                <th class="text-end">{{ __('dental_labs.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($labs as $lab)
                                <tr>
                                    <td data-label="{{ __('dental_labs.name') }}">
                                        <span class="fw-semibold">{{ $lab->name }}</span>
                                    </td>
                                    <td data-label="{{ __('dental_labs.phone') }}">{{ $lab->phone ?: '—' }}</td>
                                    <td data-label="{{ __('dental_labs.address') }}">{{ $lab->address ?: '—' }}</td>
                                    <td data-label="{{ __('dental_labs.cases_count') }}">
                                        <span class="badge text-bg-light border">{{ $lab->cases_count }}</span>
                                    </td>
                                    <td data-label="{{ __('dental_labs.actions') }}" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editLabModal{{ $lab->id }}" title="{{ __('dental_labs.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('dental-labs.destroy', $lab) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('dental_labs.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="{{ __('dental_labs.delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-building fs-2 d-block mb-2"></i>
                                            {{ __('dental_labs.no_labs') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($labs->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    {{ $labs->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @foreach ($labs as $lab)
        @include('dental-labs.partials.edit-modal', ['lab' => $lab])
    @endforeach

    @include('dental-labs.partials.create-modal')
@endsection
