@extends('layouts.app')

@section('title', __('assets.title'))

@section('content')
    <script src="{{ asset('js/dental-ui.js') }}" defer></script>
    <div class="container-fluid px-0">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary-subtle p-2"><i class="bi bi-pc-display text-primary"></i></div><div><h3 class="mb-0">{{ __('assets.title') }}</h3></div></div>
            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end" role="search">
                <div class="input-group" style="max-width: 320px;"><span class="input-group-text bg-body"><i class="bi bi-search"></i></span><input id="assetSearch" type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('assets.search') }}" maxlength="100" autocomplete="off"></div>
                <button class="btn btn-outline-primary text-nowrap" type="submit">{{ __('assets.filter') }}</button>
                @if(request()->filled('search'))<a class="btn btn-outline-secondary" href="{{ route('assets.index') }}"><i class="bi bi-x-lg"></i></a>@endif
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createAssetModal"><i class="bi bi-plus-lg me-1"></i>{{ __('assets.add_asset') }}</button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm"><div class="card-body p-0"><div class="table-responsive">
            <table class="table zedan-responsive-table mb-0 align-middle"><thead><tr>
                <th>{{ __('assets.name') }}</th><th>{{ __('assets.category') }}</th><th>{{ __('assets.purchase_date') }}</th><th>{{ __('assets.purchase_cost') }}</th><th>{{ __('assets.book_value') }}</th><th>{{ __('assets.recorded_by') }}</th><th class="text-end">{{ __('assets.actions') }}</th>
            </tr></thead><tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td data-label="{{ __('assets.name') }}"><span class="fw-semibold">{{ $asset->name }}</span></td>
                    <td data-label="{{ __('assets.category') }}">{{ $asset->category }}</td>
                    <td data-label="{{ __('assets.purchase_date') }}">{{ $asset->purchase_date->format('Y-m-d') }}</td>
                    <td data-label="{{ __('assets.purchase_cost') }}"><span data-compact-money data-value="{{ $asset->purchase_cost }}">{{ number_format($asset->purchase_cost, 2) }}</span></td>
                    <td data-label="{{ __('assets.book_value') }}"><span class="fw-semibold"><span data-compact-money data-value="{{ $asset->bookValue() }}">{{ number_format($asset->bookValue(), 2) }}</span></span></td>
                    <td data-label="{{ __('assets.recorded_by') }}">{{ $asset->creator?->name ?: '—' }}</td>
                    <td data-label="{{ __('assets.actions') }}" class="text-end"><div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAssetModal{{ $asset->id }}" title="{{ __('assets.edit') }}"><i class="bi bi-pencil"></i></button>
                        <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('assets.confirm_delete') }}')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="{{ __('assets.delete') }}"><i class="bi bi-trash"></i></button></form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-5"><div class="text-muted"><i class="bi bi-pc-display fs-2 d-block mb-2"></i>{{ __('assets.no_records') }}</div></td></tr>
            @endforelse
            </tbody></table>
        </div></div>@if($assets->hasPages())<div class="card-footer bg-transparent border-0 pt-0 pb-3">{{ $assets->withQueryString()->links('pagination::bootstrap-5') }}</div>@endif</div>
    </div>

    @foreach ($assets as $asset)
        <div class="modal fade" id="editAssetModal{{ $asset->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
            <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title">{{ __('assets.edit_asset') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">@include('assets.partials.fields', ['asset' => $asset])</div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('assets.cancel') }}</button><button type="submit" class="btn btn-primary">{{ __('assets.save') }}</button></div>
            </form>
        </div></div></div>
    @endforeach

    <div class="modal fade" id="createAssetModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">@csrf
            <div class="modal-header"><h5 class="modal-title">{{ __('assets.add_asset') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">@include('assets.partials.fields', ['asset' => null])</div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('assets.cancel') }}</button><button type="submit" class="btn btn-primary">{{ __('assets.save') }}</button></div>
        </form>
    </div></div></div>
@endsection
