@extends('layouts.app')

@section('title', $patient->full_name . ' - ' . __('messages.tooth_chart'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('messages.tooth_chart') }}</h2>
            <p class="text-muted mb-0">{{ $patient->full_name }} — {{ $patient->phone }}</p>
        </div>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_patient') }}
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllToggleBtn">
                        <i class="bi bi-check-all"></i> {{ __('messages.select_all') }}
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="tooth-chart-container">
                        <!-- Upper Arch -->
                        <div class="arch upper-arch mb-4">
                            <h6 class="text-center text-muted mb-3">{{ __('messages.upper_arch') }}</h6>
                            <div class="arch-wrapper">
                                <span class="arch-side-label me-2">{{ __('messages.left') }}</span>
                                <div class="single-teeth-row">
                                    {{-- Upper Left: 8 to 1 towards midline --}}
                                    @foreach([16,15,14,13,12,11,10,9] as $index => $num)
                                        @include('patients.partials.tooth', [
                                            'number' => $num,
                                            'record' => $toothMap[$num] ?? null,
                                            'label'  => 8 - $index,
                                        ])
                                    @endforeach

                                    {{-- Upper Right: 1 to 8 away from midline --}}
                                    @foreach([1,2,3,4,5,6,7,8] as $index => $num)
                                        @include('patients.partials.tooth', [
                                            'number' => $num,
                                            'record' => $toothMap[$num] ?? null,
                                            'label'  => $index + 1,
                                        ])
                                    @endforeach
                                </div>
                                <span class="arch-side-label ms-2">{{ __('messages.right') }}</span>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="arch-divider"></div>

                        <!-- Lower Arch -->
                        <div class="arch lower-arch mt-4">
                            <h6 class="text-center text-muted mb-3">{{ __('messages.lower_arch') }}</h6>
                            <div class="arch-wrapper">
                                <span class="arch-side-label me-2">{{ __('messages.left') }}</span>
                                <div class="single-teeth-row">
                                    {{-- Lower Left: 8 to 1 towards midline --}}
                                    @foreach([24,23,22,21,20,19,18,17] as $index => $num)
                                        @include('patients.partials.tooth', [
                                            'number' => $num,
                                            'record' => $toothMap[$num] ?? null,
                                            'label'  => 8 - $index,
                                        ])
                                    @endforeach

                                    {{-- Lower Right: 1 to 8 away from midline --}}
                                    @foreach([25,26,27,28,29,30,31,32] as $index => $num)
                                        @include('patients.partials.tooth', [
                                            'number' => $num,
                                            'record' => $toothMap[$num] ?? null,
                                            'label'  => $index + 1,
                                        ])
                                    @endforeach
                                </div>
                                <span class="arch-side-label ms-2">{{ __('messages.right') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend & Editor -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">{{ __('messages.legend') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach([
                            'healthy' => ['success', __('messages.status_healthy')],
                            'decayed' => ['danger', __('messages.status_decayed')],
                            'filled' => ['primary', __('messages.status_filled')],
                            'crown' => ['warning', __('messages.status_crown')],
                            'root_canal' => ['info', __('messages.status_root_canal')],
                            'extracted' => ['secondary', __('messages.status_extracted')],
                            'missing' => ['dark', __('messages.status_missing')],
                            'implant' => ['success', __('messages.status_implant')],
                            'fractured' => ['danger', __('messages.status_fractured')],
                            'abscess' => ['danger', __('messages.status_abscess')],
                            'braces' => ['primary', __('messages.status_braces')],
                            'veneer' => ['warning', __('messages.status_veneer')],
                        ] as $status => [$color, $label])
                            <span class="badge bg-{{ $color }}">{{ $label }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- MERGED editor: tap any tooth to select/deselect it. Works
                 identically whether 1 tooth or many are selected — Apply
                 always sends a single request covering everything
                 currently selected, then clears the selection. --}}
            <div class="card shadow-sm" id="toothInfoCard" style="display: none;">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" id="toothInfoTitle">{{ __('messages.tooth') }}</h6>
                    <button type="button" class="btn-close small" id="clearSelectionBtn" aria-label="{{ __('messages.clear_selection') }}"></button>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3" id="selectedTeethSummary"></p>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select class="form-select" id="toothStatusSelect">
                            <option value="healthy">{{ __('messages.status_healthy') }}</option>
                            <option value="decayed">{{ __('messages.status_decayed') }}</option>
                            <option value="filled">{{ __('messages.status_filled') }}</option>
                            <option value="crown">{{ __('messages.status_crown') }}</option>
                            <option value="root_canal">{{ __('messages.status_root_canal') }}</option>
                            <option value="extracted">{{ __('messages.status_extracted') }}</option>
                            <option value="missing">{{ __('messages.status_missing') }}</option>
                            <option value="implant">{{ __('messages.status_implant') }}</option>
                            <option value="fractured">{{ __('messages.status_fractured') }}</option>
                            <option value="abscess">{{ __('messages.status_abscess') }}</option>
                            <option value="braces">{{ __('messages.status_braces') }}</option>
                            <option value="veneer">{{ __('messages.status_veneer') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.linked_treatment') }}</label>
                        <select class="form-select" id="toothTreatmentSelect">
                            <option value="">{{ __('messages.none') }}</option>
                            @foreach($treatments as $treatment)
                                <option value="{{ $treatment->id }}">{{ $treatment->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="toothNotesWrapper">
                        <label class="form-label">{{ __('messages.notes') }}</label>
                        <textarea class="form-control" id="toothNotesInput" rows="2"></textarea>
                        <div class="form-text" id="toothNotesMultiHint" style="display: none;">
                            {{ __('messages.notes_multi_hint') }}
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" id="applyBtn">
                            <i class="bi bi-check-lg"></i> {{ __('messages.apply') }}
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="resetSelectionBtn" title="{{ __('messages.reset') }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>

                    <div id="toothActionFeedback" class="small mt-2" role="status" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.tooth-chart-container {
    user-select: none;
}

.arch-divider {
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--bs-border-color), transparent);
    margin: 1rem 0;
    border-radius: 2px;
}

.arch-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.arch-side-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--bs-secondary);
    white-space: nowrap;
}

.single-teeth-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 3px;
    flex-wrap: nowrap;
}

.tooth-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: transform 0.15s ease;
    padding: 2px;
    border-radius: 6px;
    position: relative;
}

.tooth-wrapper:hover {
    transform: translateY(-2px);
    background: rgba(var(--bs-primary-rgb), 0.05);
}

.tooth-wrapper.selected {
    background: rgba(var(--bs-primary-rgb), 0.15);
    box-shadow: 0 0 0 2px var(--bs-primary);
}

.tooth-wrapper.extracted .tooth-svg,
.tooth-wrapper.missing .tooth-svg {
    opacity: 0.2;
    filter: grayscale(1);
}

.tooth-number {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--bs-body-color);
    margin-top: 3px;
}

.tooth-svg {
    width: 34px;
    height: 46px;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
}

.tooth-svg path {
    transition: fill 0.2s ease, stroke 0.2s ease;
}

/* Status colors */
.tooth-wrapper.healthy .tooth-svg path { fill: #e8f5e9; stroke: #4caf50; }
.tooth-wrapper.decayed .tooth-svg path { fill: #ffebee; stroke: #f44336; }
.tooth-wrapper.filled .tooth-svg path { fill: #e3f2fd; stroke: #2196f3; }
.tooth-wrapper.crown .tooth-svg path { fill: #fff8e1; stroke: #ff9800; }
.tooth-wrapper.root_canal .tooth-svg path { fill: #e1f5fe; stroke: #03a9f4; }
.tooth-wrapper.extracted .tooth-svg path { fill: #f5f5f5; stroke: #9e9e9e; }
.tooth-wrapper.missing .tooth-svg path { fill: #fafafa; stroke: #bdbdbd; }
.tooth-wrapper.implant .tooth-svg path { fill: #e8f5e9; stroke: #2e7d32; }
.tooth-wrapper.fractured .tooth-svg path { fill: #ffebee; stroke: #c62828; }
.tooth-wrapper.abscess .tooth-svg path { fill: #fce4ec; stroke: #ad1457; }
.tooth-wrapper.braces .tooth-svg path { fill: #e8eaf6; stroke: #3f51b5; }
.tooth-wrapper.veneer .tooth-svg path { fill: #fff3e0; stroke: #e65100; }

@media (max-width: 768px) {
    .tooth-svg { width: 24px; height: 34px; }
    .single-teeth-row { gap: 1px; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const toothWrappers = Array.from(document.querySelectorAll('.tooth-wrapper'));
    const infoCard = document.getElementById('toothInfoCard');
    const titleEl = document.getElementById('toothInfoTitle');
    const summaryEl = document.getElementById('selectedTeethSummary');
    const statusSelect = document.getElementById('toothStatusSelect');
    const treatmentSelect = document.getElementById('toothTreatmentSelect');
    const notesInput = document.getElementById('toothNotesInput');
    const notesMultiHint = document.getElementById('toothNotesMultiHint');
    const applyBtn = document.getElementById('applyBtn');
    const resetBtn = document.getElementById('resetSelectionBtn');
    const clearBtn = document.getElementById('clearSelectionBtn');
    const selectAllToggleBtn = document.getElementById('selectAllToggleBtn');
    const feedback = document.getElementById('toothActionFeedback');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // The single source of truth for what's selected. Tapping a tooth
    // toggles its membership here; every other bit of UI (the panel,
    // each tooth's visual "selected" ring) is derived from this set.
    let selected = new Set();

    function selectedNumbers() {
        return Array.from(selected).sort((a, b) => a - b);
    }

    function findWrapper(num) {
        return document.querySelector('.tooth-wrapper[data-tooth="' + num + '"]');
    }

    function syncToothVisuals() {
        toothWrappers.forEach(wrapper => {
            const num = parseInt(wrapper.dataset.tooth, 10);
            wrapper.classList.toggle('selected', selected.has(num));
        });

        // Toggle button reflects and drives whole-chart selection: label
        // reads "Select All" when anything is still unselected, and
        // switches to "Deselect All" only once every tooth on the chart
        // is selected — so clicking it always does the "more useful next
        // action" rather than a fixed action regardless of state.
        const allSelected = toothWrappers.length > 0 && selected.size === toothWrappers.length;
        selectAllToggleBtn.innerHTML = allSelected
            ? '<i class="bi bi-x-square"></i> {{ __('messages.deselect_all') }}'
            : '<i class="bi bi-check-all"></i> {{ __('messages.select_all') }}';
    }

    // Prefill the form to match the selection:
    // - exactly one tooth selected -> prefill with that tooth's own
    //   current status/treatment/notes, same as the old single-tooth panel
    // - more than one selected -> leave fields at a neutral default,
    //   since teeth may currently differ; whatever the user picks here
    //   gets applied to ALL of them
    function refreshPanel() {
        const nums = selectedNumbers();

        if (nums.length === 0) {
            infoCard.style.display = 'none';
            return;
        }

        infoCard.style.display = 'block';

        if (nums.length === 1) {
            const wrapper = findWrapper(nums[0]);
            const label = wrapper?.dataset.label || nums[0];

            titleEl.textContent = '{{ __('messages.tooth') }} #' + label;
            summaryEl.textContent = '';
            summaryEl.style.display = 'none';

            statusSelect.value = wrapper?.dataset.status || 'healthy';
            treatmentSelect.value = wrapper?.dataset.treatment || '';
            notesInput.value = wrapper?.dataset.notes || '';
            notesMultiHint.style.display = 'none';
        } else {
            titleEl.textContent = '{{ __('messages.bulk_treatment') }}';
            summaryEl.style.display = 'block';
            summaryEl.textContent = nums.length + ' {{ __('messages.teeth_selected') }}: ' +
                nums.map(n => findWrapper(n)?.dataset.label || n).join(', ');

            // Multiple teeth likely have different current statuses, so we
            // don't guess by prefilling from any one of them — that would
            // look like "here's their shared status" when it isn't.
            // Notes work the same way: applying blanks them out for every
            // selected tooth unless the user actively types something, so
            // starting empty (rather than borrowing one tooth's note) is
            // what "notes_multi_hint" below is warning about.
            statusSelect.value = 'healthy';
            treatmentSelect.value = '';
            notesInput.value = '';
            notesMultiHint.style.display = 'block';
        }

        feedback.textContent = '';
    }

    function toggleTooth(num) {
        if (selected.has(num)) {
            selected.delete(num);
        } else {
            selected.add(num);
        }
        syncToothVisuals();
        refreshPanel();
    }

    function clearSelection() {
        selected.clear();
        syncToothVisuals();
        refreshPanel();
    }

    function selectAll() {
        toothWrappers.forEach(wrapper => {
            selected.add(parseInt(wrapper.dataset.tooth, 10));
        });
        syncToothVisuals();
        refreshPanel();
    }

    toothWrappers.forEach(wrapper => {
        wrapper.addEventListener('click', () => {
            toggleTooth(parseInt(wrapper.dataset.tooth, 10));
        });
    });

    clearBtn.addEventListener('click', clearSelection);

    selectAllToggleBtn.addEventListener('click', () => {
        const allSelected = toothWrappers.length > 0 && selected.size === toothWrappers.length;
        if (allSelected) {
            clearSelection();
        } else {
            selectAll();
        }
    });

    function setBusy(busy) {
        applyBtn.disabled = busy;
        resetBtn.disabled = busy;
    }

    function showFeedback(message, isError) {
        feedback.textContent = message;
        feedback.className = 'small mt-2 ' + (isError ? 'text-danger' : 'text-success');
    }


    function repaintTooth(num, status, treatmentId, notes) {
        const wrapper = findWrapper(num);
        if (!wrapper) return;

        wrapper.className = 'tooth-wrapper ' + status;
        wrapper.dataset.status = status;
        wrapper.dataset.treatment = treatmentId || '';
        wrapper.dataset.notes = notes || '';
    }

    async function applySelection() {
        const nums = selectedNumbers();
        if (nums.length === 0) return;

        setBusy(true);
        showFeedback('{{ __('messages.working') }}', false);

        const payload = {
            tooth_numbers: nums,
            status: statusSelect.value,
            treatment_id: treatmentSelect.value || null,
            notes: notesInput.value || null,
        };

        try {
            const response = await fetch("{{ route('patients.tooth-chart.bulk-apply', $patient) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            let data;
            try {
                data = await response.json();
            } catch {
                showFeedback('{{ __('messages.unexpected_error') }}', true);
                return;
            }

            if (!response.ok || !data.success) {
                showFeedback(data.message || '{{ __('messages.error_occurred') }}', true);
                return;
            }

            nums.forEach(num => repaintTooth(num, payload.status, payload.treatment_id, payload.notes));

            // Auto-deselect after a successful apply, as requested — the
            // panel closes and the chart is ready for the next selection.
            clearSelection();
        } catch (err) {
            showFeedback('{{ __('messages.network_error') }}', true);
        } finally {
            setBusy(false);
        }
    }

    async function resetSelection() {
        const nums = selectedNumbers();
        if (nums.length === 0) return;
        if (!confirm('{{ __('messages.confirm_reset_tooth') }}')) return;

        setBusy(true);
        showFeedback('{{ __('messages.working') }}', false);

        try {
            const response = await fetch("{{ route('patients.tooth-chart.bulk-reset', $patient) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ tooth_numbers: nums }),
            });

            let data;
            try {
                data = await response.json();
            } catch {
                showFeedback('{{ __('messages.unexpected_error') }}', true);
                return;
            }

            if (!response.ok || !data.success) {
                showFeedback(data.message || '{{ __('messages.error_occurred') }}', true);
                return;
            }

            nums.forEach(num => repaintTooth(num, 'healthy', '', ''));
            clearSelection();
        } catch (err) {
            showFeedback('{{ __('messages.network_error') }}', true);
        } finally {
            setBusy(false);
        }
    }

    applyBtn.addEventListener('click', applySelection);
    resetBtn.addEventListener('click', resetSelection);

    // Derive the toggle button's initial label from actual state rather
    // than relying on the server-rendered default happening to match —
    // harmless today (page always loads with an empty selection) but
    // keeps this correct even if that ever changes.
    syncToothVisuals();
});
</script>
@endpush
@endsection