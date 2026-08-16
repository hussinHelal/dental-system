@extends('layouts.app')

@section('title', __('suppliers.title'))

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
                    <i class="bi bi-truck text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ __('suppliers.title') }}</h3>
                </div>
            </div>

            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end" role="search">
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="{{ __('suppliers.search') }}"
                           maxlength="100" autocomplete="off">
                </div>
                <button class="btn btn-outline-primary text-nowrap" type="submit">
                    {{ __('suppliers.filter') }}
                </button>
                @if (request()->filled('search'))
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary text-nowrap">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-primary text-nowrap">
                    <i class="bi bi-cart3 me-1"></i>{{ __('purchases.title') }}
                </a>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('suppliers.add_supplier') }}
                </button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('suppliers.name') }}</th>
                                <th>{{ __('suppliers.phone') }}</th>
                                <th>{{ __('suppliers.address') }}</th>
                                <th>{{ __('suppliers.purchases_count') }}</th>
                                <th class="text-end">{{ __('suppliers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td data-label="{{ __('suppliers.name') }}">
                                        <div class="fw-semibold">{{ $supplier->name }}</div>
                                    </td>
                                    <td data-label="{{ __('suppliers.phone') }}">{{ $supplier->phone ?: '—' }}</td>
                                    <td data-label="{{ __('suppliers.address') }}">{{ $supplier->address ?: '—' }}</td>
                                    <td data-label="{{ __('suppliers.purchases_count') }}">
                                        <span class="badge text-bg-light border">{{ $supplier->purchases_count }}</span>
                                    </td>
                                    <td data-label="{{ __('suppliers.actions') }}" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}"
                                                    title="{{ __('suppliers.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('{{ __('suppliers.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="{{ __('suppliers.delete') }}">
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
                                            <i class="bi bi-truck fs-2 d-block mb-2"></i>
                                            {{ __('suppliers.no_records') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($suppliers->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    {{ $suppliers->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @foreach ($suppliers as $supplier)
        @include('suppliers.partials.edit-modal', ['supplier' => $supplier])
    @endforeach

    @include('suppliers.partials.create-modal')
@endsection
