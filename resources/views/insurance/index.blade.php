@extends('layouts.app')

@section('title', __('insurance.title'))

@section('content')
    <script src="{{ asset('js/dental-ui.js') }}" defer></script>

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
                    <i class="bi bi-shield-check text-primary"></i>
                </div>
                <h3 class="mb-0">{{ __('insurance.title') }}</h3>
            </div>

            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end">
                <div class="input-group" style="max-width: 290px;">
                    <span class="input-group-text bg-body"><i class="bi bi-search"></i></span>
                    <input id="insuranceSearch" type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('insurance.search') }}" maxlength="100" autocomplete="off">
                </div>

                <select id="insuranceStatus" name="status" class="form-select" style="max-width: 180px;">
                    <option value="">{{ __('insurance.all_statuses') }}</option>
                    <option value="active" @selected(request('status') === 'active')>{{ __('insurance.status_active') }}</option>
                    <option value="expired" @selected(request('status') === 'expired')>{{ __('insurance.status_expired') }}</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>{{ __('insurance.status_cancelled') }}</option>
                </select>

                <button class="btn btn-outline-primary text-nowrap" type="submit">{{ __('insurance.filter') }}</button>

                @if (request()->filled('search') || request()->filled('status'))
                    <a class="btn btn-outline-secondary" href="{{ route('insurance.index') }}" title="{{ __('insurance.clear_filter') }}">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif

                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createInsuranceModal">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('insurance.add_contract') }}
                </button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('insurance.company_name') }}</th>
                                <th>{{ __('insurance.contract_number') }}</th>
                                <th>{{ __('insurance.end_date') }}</th>
                                <th>{{ __('insurance.discount') }}</th>
                                <th>{{ __('insurance.status') }}</th>
                                <th>{{ __('insurance.recorded_by') }}</th>
                                <th class="text-end">{{ __('insurance.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contracts as $contract)
                                <tr>
                                    <td data-label="{{ __('insurance.company_name') }}">
                                        <span class="fw-semibold">{{ $contract->company_name }}</span>
                                    </td>
                                    <td data-label="{{ __('insurance.contract_number') }}">{{ $contract->contract_number }}</td>
                                    <td data-label="{{ __('insurance.end_date') }}">{{ $contract->end_date->format('Y-m-d') }}</td>
                                    <td data-label="{{ __('insurance.discount') }}">{{ $contract->discount_percentage }}%</td>
                                    <td data-label="{{ __('insurance.status') }}">
                                        <span class="badge bg-{{ $contract->status->badgeColor() }}">{{ $contract->status->label() }}</span>
                                    </td>
                                    <td data-label="{{ __('insurance.recorded_by') }}">{{ $contract->creator?->name ?: '—' }}</td>
                                    <td data-label="{{ __('insurance.actions') }}" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editInsuranceModal{{ $contract->id }}" title="{{ __('insurance.edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('insurance.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('insurance.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="{{ __('insurance.delete') }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-shield-check fs-2 d-block mb-2"></i>
                                            {{ __('insurance.no_records') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($contracts->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    {{ $contracts->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @foreach ($contracts as $contract)
        @include('insurance.partials.edit-modal', ['contract' => $contract])
    @endforeach

    @include('insurance.partials.create-modal')
@endsection
