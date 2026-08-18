@php $contract = $contract ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('insurance.company_name') }}</label>
        <input type="text" name="company_name" class="form-control" required value="{{ old('company_name', $contract->company_name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('insurance.contract_number') }}</label>
        <input type="text" name="contract_number" class="form-control" value="{{ old('contract_number', $contract->contract_number ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('insurance.start_date') }}</label>
        <input type="date" name="start_date" class="form-control" required
            value="{{ old('start_date', optional($contract?->start_date)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('insurance.end_date') }}</label>
        <input type="date" name="end_date" class="form-control" required
            value="{{ old('end_date', optional($contract?->end_date)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('insurance.discount') }}</label>
        <input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control" required
            value="{{ old('discount_percentage', $contract->discount_percentage ?? 0) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('insurance.patient_name') }}</label>
        <div class="position-relative" data-patient-autocomplete data-endpoint="{{ url('/patients/search') }}" data-target-phone="#insurancePhone{{ $contract?->id ?? 'Create' }}">
            <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $contract->contact_person ?? '') }}" data-patient-search-input autocomplete="off">
            <div class="dropdown-menu w-100 p-0 shadow-sm d-none" data-patient-results></div>
        </div>
        <div class="form-text">{{ __('insurance.contact_person_hint') }}</div>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('insurance.patient_phone') }}</label>
        <input id="insurancePhone{{ $contract?->id ?? 'Create' }}" type="text" name="phone" class="form-control" value="{{ old('phone', $contract->phone ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('insurance.status') }}</label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', $contract->status->value ?? 'active') === 'active')>{{ __('insurance.status_active') }}</option>
            <option value="expired" @selected(old('status', $contract->status->value ?? '') === 'expired')>{{ __('insurance.status_expired') }}</option>
            <option value="cancelled" @selected(old('status', $contract->status->value ?? '') === 'cancelled')>{{ __('insurance.status_cancelled') }}</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('insurance.coverage_details') }}</label>
        <textarea name="coverage_details" class="form-control" rows="2">{{ old('coverage_details', $contract->coverage_details ?? '') }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">{{ __('insurance.notes') }}</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $contract->notes ?? '') }}</textarea>
    </div>
</div>
