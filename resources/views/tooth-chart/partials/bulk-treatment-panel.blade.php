{{--
    Bulk treatment panel for the odontogram.

    Expects $teeth: a collection of Tooth models, each with
    palmer_quadrant, palmer_position, and id — ordered by quadrant per
    App\Support\PalmerNotation::quadrantOrder().

    Sends exactly one POST request per action, regardless of how many
    teeth are checked (from 1 up to all 32).
--}}
<div class="card shadow-sm mb-4" id="bulk-treatment-panel"
     data-patient-id="{{ $patient->id }}"
     data-apply-url="{{ route('tooth-chart.bulk-apply', $patient->id) }}"
     data-remove-url="{{ route('tooth-chart.bulk-remove', $patient->id) }}">
    <div class="card-header bg-white">
        <h2 class="h6 mb-0">{{ __('Bulk Treatment') }}</h2>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-4">
                <label for="bulk-treatment-type" class="form-label">{{ __('Treatment') }}</label>
                <select id="bulk-treatment-type" class="form-select">
                    <option value="veneer">{{ __('Veneer') }}</option>
                    <option value="orthodontics">{{ __('Orthodontics') }}</option>
                    <option value="whitening">{{ __('Whitening') }}</option>
                    <option value="sealant">{{ __('Sealant') }}</option>
                    <option value="fluoride_treatment">{{ __('Fluoride Treatment') }}</option>
                    <option value="cleaning">{{ __('Cleaning') }}</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="bulk-treatment-note" class="form-label">{{ __('Note (optional)') }}</label>
                <input type="text" id="bulk-treatment-note" class="form-control" maxlength="255" placeholder="{{ __('e.g. pre-op consult') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="button" id="bulk-apply-btn" class="btn btn-success flex-fill">
                    <i class="bi bi-check2-circle me-1"></i> {{ __('Apply') }}
                </button>
                <button type="button" id="bulk-remove-btn" class="btn btn-outline-danger flex-fill">
                    <i class="bi bi-x-circle me-1"></i> {{ __('Remove') }}
                </button>
            </div>
        </div>

        <div class="form-check mb-2 border-bottom pb-2">
            <input class="form-check-input" type="checkbox" id="select-all-teeth">
            <label class="form-check-label fw-semibold" for="select-all-teeth">
                {{ __('Select All Teeth') }}
            </label>
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-2" id="tooth-checkbox-grid">
            @foreach (\App\Support\PalmerNotation::quadrantOrder() as $quadrant)
                <div class="col">
                    <div class="small text-muted fw-semibold mb-1">
                        {{ \App\Support\PalmerNotation::quadrantName($quadrant) }}
                    </div>
                    @foreach ($teeth->where('palmer_quadrant', $quadrant)->sortByDesc('palmer_position') as $tooth)
                        <div class="form-check">
                            <input class="form-check-input tooth-checkbox"
                                   type="checkbox"
                                   value="{{ $tooth->id }}"
                                   id="tooth-cb-{{ $tooth->id }}">
                            <label class="form-check-label d-flex align-items-center gap-2" for="tooth-cb-{{ $tooth->id }}">
                                <span class="fw-semibold">
                                    {{ \App\Support\PalmerNotation::label($tooth->palmer_quadrant, $tooth->palmer_position) }}
                                </span>
                                <span class="text-muted small">
                                    #{{ $tooth->tooth_number }}
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div id="bulk-treatment-feedback" class="mt-3" role="status" aria-live="polite"></div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const panel = document.getElementById('bulk-treatment-panel');
        const selectAll = document.getElementById('select-all-teeth');
    const checkboxes = () => Array.from(document.querySelectorAll('.tooth-checkbox'));
    const applyBtn = document.getElementById('bulk-apply-btn');
    const removeBtn = document.getElementById('bulk-remove-btn');
    const feedback = document.getElementById('bulk-treatment-feedback');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const toothGrid = document.getElementById('tooth-checkbox-grid');
    const treatmentType = document.getElementById('bulk-treatment-type');
    const treatmentNote = document.getElementById('bulk-treatment-note');

    if (!panel || !selectAll || !applyBtn || !removeBtn || !feedback || !toothGrid || !treatmentType || !treatmentNote) return;

    selectAll.addEventListener('change', function () {
        checkboxes().forEach(cb => { cb.checked = selectAll.checked; });
    });

    // If the user manually unchecks one tooth, "select all" should reflect
    // that it's no longer accurate — avoids a misleading checked state.
    toothGrid.addEventListener('change', function (e) {
        if (!e.target.classList.contains('tooth-checkbox')) return;
        const all = checkboxes();
        selectAll.checked = all.length > 0 && all.every(cb => cb.checked);
    });

    function selectedToothIds() {
        return checkboxes().filter(cb => cb.checked).map(cb => parseInt(cb.value, 10));
    }

    function setBusy(busy) {
        applyBtn.disabled = busy;
        removeBtn.disabled = busy;
    }

    function showFeedback(message, isError) {
        feedback.textContent = message;
        feedback.className = 'mt-3 ' + (isError ? 'text-danger' : 'text-success');
    }

    async function sendBulkRequest(action) {
        const toothIds = selectedToothIds();

        if (toothIds.length === 0) {
            showFeedback(@json(__('Select at least one tooth first.')), true);
            return;
        }
        if (!csrfToken) {
            showFeedback(@json(__('Security token missing — please refresh the page.')), true);
            return;
        }

        if (action === 'remove' && !window.confirm(@json(__('Remove this treatment from all selected teeth? This cannot be undone.')))) {
            return;
        }

        const url = action === 'apply' ? panel.dataset.applyUrl : panel.dataset.removeUrl;
        const payload = {
            tooth_ids: toothIds,
            treatment_type: treatmentType.value,
            note: treatmentNote.value || null,
        };

        setBusy(true);
        showFeedback(@json(__('Working…')), false);

        try {
            const response = await fetch(url, {
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
                // Server returned a non-JSON body (e.g. an HTML error page
                // from a 500 that bypassed our handler) — don't let JSON
                // parsing throw an unhandled error the user never sees.
                showFeedback(@json(__('Unexpected server response. Please try again.')), true);
                return;
            }

            if (!response.ok || !data.success) {
                showFeedback(data.message || @json(__('Something went wrong.')), true);
                return;
            }

            showFeedback(data.message, false);

            // Refresh the odontogram SVG/status display if that function
            // exists on the page (defined by the main tooth-chart script).
            if (typeof window.refreshToothChart === 'function') {
                window.refreshToothChart();
            }
        } catch (err) {
            showFeedback(@json(__('Network error — please check your connection and try again.')), true);
        } finally {
            setBusy(false);
        }
    }

    applyBtn.addEventListener('click', () => sendBulkRequest('apply'));
    removeBtn.addEventListener('click', () => sendBulkRequest('remove'));
})();
</script>
@endpush
