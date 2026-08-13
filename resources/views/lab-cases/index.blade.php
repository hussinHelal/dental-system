@extends('layouts.app')

@section('title', __('dental_labs.cases_title'))

@section('content')
    <div class="container-fluid px-0">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary-subtle p-2"><i class="bi bi-box-seam text-primary"></i></div><div><h3 class="mb-0">{{ __('dental_labs.cases_title') }}</h3><p class="text-muted small mb-0">{{ __('dental_labs.status') }}</p></div></div>
            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end">
                <select name="dental_lab_id" class="form-select" style="max-width: 190px;"><option value="">{{ __('dental_labs.all') }}</option>@foreach ($labs as $lab)<option value="{{ $lab->id }}" @selected(request('dental_lab_id') == $lab->id)>{{ $lab->name }}</option>@endforeach</select>
                <select name="status" class="form-select" style="max-width: 170px;"><option value="">{{ __('dental_labs.all') }}</option><option value="sent" @selected(request('status') === 'sent')>{{ __('dental_labs.status_sent') }}</option><option value="in_progress" @selected(request('status') === 'in_progress')>{{ __('dental_labs.status_in_progress') }}</option><option value="received" @selected(request('status') === 'received')>{{ __('dental_labs.status_received') }}</option><option value="delivered" @selected(request('status') === 'delivered')>{{ __('dental_labs.status_delivered') }}</option></select>
                <button class="btn btn-outline-primary text-nowrap" type="submit">{{ __('dental_labs.filter') }}</button>
                @if(request()->filled('dental_lab_id') || request()->filled('status'))<a href="{{ route('lab-cases.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>@endif
                <a href="{{ route('dental-labs.index') }}" class="btn btn-outline-primary text-nowrap">{{ __('dental_labs.manage_labs') }}</a>
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createCaseModal"><i class="bi bi-plus-lg me-1"></i>{{ __('dental_labs.add_case') }}</button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm"><div class="card-body p-0"><div class="table-responsive">
            <table class="table zedan-responsive-table mb-0 align-middle"><thead><tr>
                <th>{{ __('dental_labs.patient') }}</th><th>{{ __('dental_labs.lab') }}</th><th>{{ __('dental_labs.case_type') }}</th><th>{{ __('dental_labs.sent_date') }}</th><th>{{ __('dental_labs.expected_return') }}</th><th>{{ __('dental_labs.actual_return') }}</th><th>{{ __('dental_labs.status') }}</th><th>{{ __('dental_labs.cost') }}</th><th>{{ __('dental_labs.recorded_by') }}</th><th class="text-end">{{ __('dental_labs.actions') }}</th>
            </tr></thead><tbody>
            @forelse ($cases as $case)
                <tr>
                    <td data-label="{{ __('dental_labs.patient') }}"><span class="fw-semibold">{{ $case->patient->full_name ?? $case->patient->name ?? '-' }}</span></td>
                    <td data-label="{{ __('dental_labs.lab') }}">{{ $case->lab->name }}</td>
                    <td data-label="{{ __('dental_labs.case_type') }}">{{ $case->case_type }}</td>
                    <td data-label="{{ __('dental_labs.sent_date') }}">{{ $case->sent_date->format('Y-m-d') }}</td>
                    <td data-label="{{ __('dental_labs.expected_return') }}">{{ optional($case->expected_return_date)->format('Y-m-d') ?: '—' }}</td>
                    <td data-label="{{ __('dental_labs.actual_return') }}">{{ optional($case->actual_return_date)->format('Y-m-d') ?: '—' }}</td>
                    <td data-label="{{ __('dental_labs.status') }}"><span class="badge bg-{{ $case->status->badgeColor() }}">{{ $case->status->label() }}</span></td>
                    <td data-label="{{ __('dental_labs.cost') }}">{{ number_format($case->cost, 2) }}</td>
                    <td data-label="{{ __('dental_labs.recorded_by') }}">{{ $case->creator?->name ?: '—' }}</td>
                    <td data-label="{{ __('dental_labs.actions') }}" class="text-end"><div class="d-inline-flex gap-1"><button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCaseModal{{ $case->id }}"><i class="bi bi-pencil"></i></button><form action="{{ route('lab-cases.destroy', $case) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('dental_labs.confirm_delete') }}')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center py-5"><div class="text-muted"><i class="bi bi-box-seam fs-2 d-block mb-2"></i>{{ __('dental_labs.no_cases') }}</div></td></tr>
            @endforelse
            </tbody></table>
        </div></div>@if($cases->hasPages())<div class="card-footer bg-transparent border-0 pt-0 pb-3">{{ $cases->withQueryString()->links('pagination::bootstrap-5') }}</div>@endif</div>
    </div>

    @foreach ($cases as $case)
        <div class="modal fade" id="editCaseModal{{ $case->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><form action="{{ route('lab-cases.update', $case) }}" method="POST">@csrf @method('PUT')<div class="modal-header"><h5 class="modal-title">{{ __('dental_labs.edit_case') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">@include('lab-cases.partials.fields', ['case' => $case, 'labs' => $labs])</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('dental_labs.cancel') }}</button><button type="submit" class="btn btn-primary">{{ __('dental_labs.save') }}</button></div></form></div></div></div>
    @endforeach

    <div class="modal fade" id="createCaseModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><form action="{{ route('lab-cases.store') }}" method="POST">@csrf<div class="modal-header"><h5 class="modal-title">{{ __('dental_labs.add_case') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">@include('lab-cases.partials.fields', ['case' => null, 'labs' => $labs])</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('dental_labs.cancel') }}</button><button type="submit" class="btn btn-primary">{{ __('dental_labs.save') }}</button></div></form></div></div></div>
@endsection
