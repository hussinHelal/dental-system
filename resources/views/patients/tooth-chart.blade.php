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

        <!-- Legend & Quick Actions -->
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

            <!-- Selected Tooth Info -->
            <div class="card shadow-sm" id="toothInfoCard" style="display: none;">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">{{ __('messages.tooth') }} #<span id="selectedToothNum">--</span></h6>
                </div>
                <div class="card-body">
                    <form id="toothForm" data-ajax-form method="POST" action="{{ route('patients.tooth-chart.update', $patient) }}">
                        @csrf
                        <input type="hidden" name="tooth_number" id="toothNumberInput">
                        
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.status') }}</label>
                            <select name="status" class="form-select" id="toothStatusSelect">
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
                            <select name="treatment_id" class="form-select">
                                <option value="">{{ __('messages.none') }}</option>
                                @foreach($treatments as $treatment)
                                    <option value="{{ $treatment->id }}">{{ $treatment->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.notes') }}</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-check-lg"></i> {{ __('messages.save') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="resetToothBtn">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </form>
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
    const toothWrappers = document.querySelectorAll('.tooth-wrapper');
    const infoCard = document.getElementById('toothInfoCard');
    const selectedNumEl = document.getElementById('selectedToothNum');
    const toothNumInput = document.getElementById('toothNumberInput');
    const statusSelect = document.getElementById('toothStatusSelect');
    const form = document.getElementById('toothForm');
    const resetBtn = document.getElementById('resetToothBtn');

    let currentTooth = null;

    toothWrappers.forEach(wrapper => {
        wrapper.addEventListener('click', () => {
            toothWrappers.forEach(t => t.classList.remove('selected'));
            
            wrapper.classList.add('selected');
            currentTooth = wrapper;
            
            const num = wrapper.dataset.tooth;
            const label = wrapper.dataset.label || num;
            const status = wrapper.dataset.status || 'healthy';
            const treatmentId = wrapper.dataset.treatment || '';
            const notes = wrapper.dataset.notes || '';

            selectedNumEl.textContent = label;
            toothNumInput.value = num;
            statusSelect.value = status;
            form.querySelector('select[name="treatment_id"]').value = treatmentId;
            form.querySelector('textarea[name="notes"]').value = notes;

            infoCard.style.display = 'block';
            infoCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    resetBtn.addEventListener('click', async () => {
        if (!currentTooth) return;
        if (!confirm('{{ __('messages.confirm_reset_tooth') }}')) return;

        const num = currentTooth.dataset.tooth;
        const url = `{{ route('patients.tooth-chart.destroy', [$patient, '__NUM__']) }}`.replace('__NUM__', num);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({ _method: 'DELETE' })
            });

            if (response.ok) {
                currentTooth.className = 'tooth-wrapper healthy';
                currentTooth.dataset.status = 'healthy';
                delete currentTooth.dataset.treatment;
                delete currentTooth.dataset.notes;
                infoCard.style.display = 'none';
            }
        } catch (err) {
            console.error('Reset failed', err);
        }
    });

    form.addEventListener('ajax:success', (e) => {
        if (!currentTooth) return;
        const status = statusSelect.value;
        currentTooth.className = `tooth-wrapper selected ${status}`;
        currentTooth.dataset.status = status;
        currentTooth.dataset.treatment = form.querySelector('select[name="treatment_id"]').value;
        currentTooth.dataset.notes = form.querySelector('textarea[name="notes"]').value;
    });
});
</script>
@endpush
@endsection