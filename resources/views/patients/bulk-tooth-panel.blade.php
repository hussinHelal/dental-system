<div class="card shadow-sm mb-3" id="bulkToothCard">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ __('messages.bulk_treatment') ?? 'Bulk Treatment' }}</h6>
        <div class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="selectAllTeeth">
            <label class="form-check-label small" for="selectAllTeeth">
                {{ __('messages.select_all') ?? 'Select All' }}
            </label>
        </div>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-2" id="bulkSelectedCount">
            {{ __('messages.no_teeth_selected') ?? 'No teeth selected — click the checkboxes on the chart.' }}
        </p>

        <div class="mb-2">
            <label class="form-label small mb-1">{{ __('messages.status') }}</label>
            <select class="form-select form-select-sm" id="bulkStatusSelect">
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

        <div class="mb-2">
            <label class="form-label small mb-1">{{ __('messages.linked_treatment') }}</label>
            <select class="form-select form-select-sm" id="bulkTreatmentSelect">
                <option value="">{{ __('messages.none') }}</option>
                @foreach($treatments as $treatment)
                    <option value="{{ $treatment->id }}">{{ $treatment->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-primary flex-fill" id="bulkApplyBtn" disabled>
                <i class="bi bi-check2-circle"></i> {{ __('messages.apply') ?? 'Apply' }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger flex-fill" id="bulkResetBtn" disabled>
                <i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.reset') ?? 'Reset' }}
            </button>
        </div>

        <div id="bulkToothFeedback" class="small mt-2" role="status" aria-live="polite"></div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    const selectAll = document.getElementById('selectAllTeeth');
    const countLabel = document.getElementById('bulkSelectedCount');
    const applyBtn = document.getElementById('bulkApplyBtn');
    const resetBtn = document.getElementById('bulkResetBtn');
    const statusSelect = document.getElementById('bulkStatusSelect');
    const treatmentSelect = document.getElementById('bulkTreatmentSelect');
    const feedback = document.getElementById('bulkToothFeedback');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function checkboxes() {
        return Array.from(document.querySelectorAll('.tooth-bulk-checkbox'));
    }

    function selectedNumbers() {
        return checkboxes().filter(cb => cb.checked).map(cb => parseInt(cb.dataset.tooth, 10));
    }

    function refreshSelectionState() {
        const selected = selectedNumbers();
        const total = checkboxes().length;

        if (selected.length === 0) {
            countLabel.textContent = '{{ __('messages.no_teeth_selected') ?? 'No teeth selected — click the checkboxes on the chart.' }}';
        } else {
            countLabel.textContent = selected.length + ' / ' + total + ' {{ __('messages.teeth_selected') ?? 'teeth selected' }}';
        }

        applyBtn.disabled = selected.length === 0;
        resetBtn.disabled = selected.length === 0;

        // Keep "Select All" in sync if the user unchecks one manually.
        selectAll.checked = total > 0 && selected.length === total;
    }

    selectAll.addEventListener('change', function() {
        checkboxes().forEach(cb => { cb.checked = selectAll.checked; });
        refreshSelectionState();
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('tooth-bulk-checkbox')) {
            refreshSelectionState();
        }
    });

    function setBusy(busy) {
        applyBtn.disabled = busy || selectedNumbers().length === 0;
        resetBtn.disabled = busy || selectedNumbers().length === 0;
    }

    function showFeedback(message, isError) {
        feedback.textContent = message;
        feedback.className = 'small mt-2 ' + (isError ? 'text-danger' : 'text-success');
    }

    // Applies the SVG/color class + tooth-number badge update for a given
    // tooth number, mirroring exactly what the existing single-tooth
    // ajax:success handler does (see the bottom of this page's script) —
    // so a bulk apply visually updates the chart the same way a
    // single-tooth save does, without a full page reload.
    // NOTE — matches an existing limitation in your single-tooth save
    // handler (the `form.addEventListener('ajax:success', ...)` block at
    // the bottom of tooth-chart.blade.php): only the CSS class (color) is
    // swapped client-side. The status-specific SVG shapes — the crown
    // ellipse, root-canal dashed line, extraction X, fracture line,
    // abscess dot, braces bracket — are rendered server-side by the `@if`
    // blocks inside patients/partials/tooth.blade.php at page-load time,
    // so they will NOT appear/disappear until the page is reloaded. This
    // was already true before this bulk feature; it's not a regression,
    // but it's worth knowing: a bulk-applied "crown" status will show the
    // right color immediately but not the crown icon until refresh.
    // If you want the icon to appear immediately, the fix applies equally
    // to both the single-tooth and bulk paths — it isn't specific to bulk.
    function repaintTooth(toothNumber, status, treatmentId, notes) {
        const wrapper = document.querySelector('.tooth-wrapper[data-tooth="' + toothNumber + '"]');
        if (!wrapper) return;

        const wasSelected = wrapper.classList.contains('selected');
        wrapper.className = 'tooth-wrapper ' + status + (wasSelected ? ' selected' : '');
        wrapper.dataset.status = status;
        wrapper.dataset.treatment = treatmentId || '';
        wrapper.dataset.notes = notes || '';
    }

    async function sendBulkRequest(kind) {
        const toothNumbers = selectedNumbers();
        if (toothNumbers.length === 0) return;

        const isApply = kind === 'apply';
        const url = isApply
            ? "{{ route('patients.tooth-chart.bulk-apply', $patient) }}"
            : "{{ route('patients.tooth-chart.bulk-reset', $patient) }}";

        const payload = isApply
            ? {
                  tooth_numbers: toothNumbers,
                  status: statusSelect.value,
                  treatment_id: treatmentSelect.value || null,
              }
            : { tooth_numbers: toothNumbers };

        setBusy(true);
        showFeedback('{{ __('messages.working') ?? 'Working…' }}', false);

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
                showFeedback('{{ __('messages.unexpected_error') ?? 'Unexpected server response.' }}', true);
                return;
            }

            if (!response.ok || !data.success) {
                showFeedback(data.message || '{{ __('messages.error_occurred') ?? 'Something went wrong.' }}', true);
                return;
            }

            showFeedback(data.message, false);

            if (isApply) {
                toothNumbers.forEach(num => repaintTooth(num, statusSelect.value, treatmentSelect.value, ''));
            } else {
                toothNumbers.forEach(num => repaintTooth(num, 'healthy', '', ''));
            }
        } catch (err) {
            showFeedback('{{ __('messages.network_error') ?? 'Network error — please try again.' }}', true);
        } finally {
            setBusy(false);
        }
    }

    applyBtn.addEventListener('click', () => sendBulkRequest('apply'));
    resetBtn.addEventListener('click', () => {
        if (!confirm('{{ __('messages.confirm_reset_tooth') }}')) return;
        sendBulkRequest('reset');
    });
})();
</script>
@endpush

@push('styles')
<style>
/* Position the bulk-select checkbox in the top-left corner of each tooth
   without disturbing the existing hover/selected visuals. */
.tooth-wrapper {
    position: relative;
}
.tooth-bulk-checkbox {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 2;
    margin: 1px;
    cursor: pointer;
}
[dir="rtl"] .tooth-bulk-checkbox {
    left: auto;
    right: 0;
}
</style>
@endpush
