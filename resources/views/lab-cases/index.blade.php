@extends('layouts.app')

@section('title', __('dental_labs.cases_title'))

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
                    <i class="bi bi-box-seam text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ __('dental_labs.cases_title') }}</h3>
                    <p class="text-muted small mb-0">{{ __('dental_labs.status') }}</p>
                </div>
            </div>

            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end">
                <select name="dental_lab_id" class="form-select" style="max-width: 190px;">
                    <option value="">{{ __('dental_labs.all') }}</option>
                    @foreach ($labs as $lab)
                        <option value="{{ $lab->id }}" @selected(request('dental_lab_id') == $lab->id)>{{ $lab->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-select" style="max-width: 170px;">
                    <option value="">{{ __('dental_labs.all') }}</option>
                    <option value="sent" @selected(request('status') === 'sent')>{{ __('dental_labs.status_sent') }}</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>{{ __('dental_labs.status_in_progress') }}</option>
                    <option value="received" @selected(request('status') === 'received')>{{ __('dental_labs.status_received') }}</option>
                    <option value="delivered" @selected(request('status') === 'delivered')>{{ __('dental_labs.status_delivered') }}</option>
                </select>

                <button class="btn btn-outline-primary text-nowrap" type="submit">{{ __('dental_labs.filter') }}</button>

                @if (request()->filled('dental_lab_id') || request()->filled('status'))
                    <a href="{{ route('lab-cases.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif

                <a href="{{ route('dental-labs.index') }}" class="btn btn-outline-primary text-nowrap">
                    {{ __('dental_labs.manage_labs') }}
                </a>

                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('dental_labs.add_case') }}
                </button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('dental_labs.patient') }}</th>
                                <th>{{ __('dental_labs.lab') }}</th>
                                <th>{{ __('dental_labs.case_type') }}</th>
                                <th>{{ __('dental_labs.sent_date') }}</th>
                                <th>{{ __('dental_labs.expected_return') }}</th>
                                <th>{{ __('dental_labs.actual_return') }}</th>
                                <th>{{ __('dental_labs.status') }}</th>
                                <th>{{ __('dental_labs.cost') }}</th>
                                <th>{{ __('dental_labs.recorded_by') }}</th>
                                <th class="text-end">{{ __('dental_labs.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cases as $case)
                                <tr>
                                    <td data-label="{{ __('dental_labs.patient') }}">
                                        <span class="fw-semibold">{{ $case->patient->full_name ?? $case->patient->name ?? '-' }}</span>
                                    </td>
                                    <td data-label="{{ __('dental_labs.lab') }}">{{ $case->lab->name }}</td>
                                    <td data-label="{{ __('dental_labs.case_type') }}">{{ $case->case_type }}</td>
                                    <td data-label="{{ __('dental_labs.sent_date') }}">{{ $case->sent_date->format('Y-m-d') }}</td>
                                    <td data-label="{{ __('dental_labs.expected_return') }}">
                                        {{ optional($case->expected_return_date)->format('Y-m-d') ?: '—' }}
                                    </td>
                                    <td data-label="{{ __('dental_labs.actual_return') }}">
                                        {{ optional($case->actual_return_date)->format('Y-m-d') ?: '—' }}
                                    </td>
                                    <td data-label="{{ __('dental_labs.status') }}">
                                        <span class="badge bg-{{ $case->status->badgeColor() }}">{{ $case->status->label() }}</span>
                                    </td>
                                    <td data-label="{{ __('dental_labs.cost') }}">{{ number_format($case->cost, 2) }}</td>
                                    <td data-label="{{ __('dental_labs.recorded_by') }}">{{ $case->creator?->name ?: '—' }}</td>
                                    <td data-label="{{ __('dental_labs.actions') }}" class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCaseModal{{ $case->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('lab-cases.destroy', $case) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('dental_labs.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
                                            {{ __('dental_labs.no_cases') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($cases->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                    {{ $cases->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @foreach ($cases as $case)
        @include('lab-cases.partials.edit-modal', ['case' => $case])
    @endforeach

    @include('lab-cases.partials.create-modal')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let debounceTimer = null;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, s => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[s]));
    }

    // 1. Live Input Search
    document.addEventListener('input', function (e) {
        const input = e.target.closest('[data-patient-search-input]');
        if (!input) return;

        const container = input.closest('[data-patient-autocomplete]');
        const hiddenInput = container.querySelector('[data-patient-id]');
        const resultsBox = container.querySelector('[data-patient-results]');
        const endpoint = container.dataset.endpoint;

        // Reset ID when typing
        hiddenInput.value = '';
        input.classList.remove('is-invalid');
        
        const query = input.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 1) {
            resultsBox.classList.remove('show');
            resultsBox.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${endpoint}?q=${encodeURIComponent(query)}`, {
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
                }
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.json();
            })
            .then(data => {
                // Normalize response format (supports [], {data: []}, or custom JSON objects)
                const patients = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);

                if (!patients.length) {
                    resultsBox.innerHTML = `<div class="dropdown-item text-muted disabled py-2">No patients found</div>`;
                } else {
                    resultsBox.innerHTML = patients.map(p => {
                        const name = p.full_name || p.name || 'Unknown';
                        const phone = p.phone || p.mobile || '';
                        return `
                            <button type="button" class="dropdown-item py-2" data-patient-option data-id="${p.id}" data-name="${escapeHtml(name)}">
                                <div class="fw-semibold text-dark">${escapeHtml(name)}</div>
                                ${phone ? `<div class="small text-muted">${escapeHtml(phone)}</div>` : ''}
                            </button>
                        `;
                    }).join('');
                }
                resultsBox.classList.add('show');
            })
            .catch(err => {
                console.error('Patient Autocomplete Fetch Error:', err);
                resultsBox.classList.remove('show');
            });
        }, 300);
    });

    // 2. Click Option Selection
    document.addEventListener('click', function (e) {
        const item = e.target.closest('[data-patient-option]');
        if (item) {
            const container = item.closest('[data-patient-autocomplete]');
            const input = container.querySelector('[data-patient-search-input]');
            const hiddenInput = container.querySelector('[data-patient-id]');
            const resultsBox = container.querySelector('[data-patient-results]');

            input.value = item.dataset.name;
            hiddenInput.value = item.dataset.id;
            input.classList.remove('is-invalid');

            resultsBox.classList.remove('show');
            resultsBox.innerHTML = '';
            return;
        }

        // Hide results when clicking outside
        if (!e.target.closest('[data-patient-autocomplete]')) {
            document.querySelectorAll('[data-patient-results]').forEach(box => {
                box.classList.remove('show');
            });
        }
    });

    // 3. Form Validation Check on Submit
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const container = form.querySelector('[data-patient-autocomplete]');
        if (!container) return;

        const hiddenInput = container.querySelector('[data-patient-id]');
        const searchInput = container.querySelector('[data-patient-search-input]');

        if (searchInput && hiddenInput && !hiddenInput.value) {
            e.preventDefault();
            e.stopPropagation();
            searchInput.classList.add('is-invalid');
            searchInput.focus();
        }
    });
});
</script>
@endpush

@endsection
