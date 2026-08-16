@php $case = $case ?? null; @endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="labCasePatientSearch{{ $case?->id ?? 'new' }}">
            {{ __('dental_labs.patient') }} <span class="text-danger">*</span>
        </label>
        <div class="position-relative dropdown" data-patient-autocomplete data-endpoint="{{ url('/lab-cases/patient-lookup') }}">
            <input type="text"
                   id="labCasePatientSearch{{ $case?->id ?? 'new' }}"
                   class="form-control"
                   data-patient-search-input
                   autocomplete="off"
                   value="{{ old('patient_name', $case?->patient?->full_name ?? '') }}"
                   placeholder="{{ __('messages.search_patients') }}"
                   required>
            <input type="hidden" name="patient_id" data-patient-id value="{{ old('patient_id', $case?->patient_id ?? '') }}">
            
            <div class="dropdown-menu w-100 shadow-sm" 
                 data-patient-results 
                 style="max-height: 250px; overflow-y: auto; z-index: 1065;"></div>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('dental_labs.lab') }}</label>
        <select name="dental_lab_id" class="form-select" required>
            @foreach ($labs as $lab)
                <option value="{{ $lab->id }}" @selected(old('dental_lab_id', $case->dental_lab_id ?? '') == $lab->id)>{{ $lab->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('dental_labs.case_type') }}</label>
        <input type="text" name="case_type" class="form-control" required value="{{ old('case_type', $case->case_type ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('dental_labs.status') }}</label>
        <select name="status" class="form-select" required>
            <option value="sent" @selected(old('status', $case->status->value ?? 'sent') === 'sent')>{{ __('dental_labs.status_sent') }}</option>
            <option value="in_progress" @selected(old('status', $case->status->value ?? '') === 'in_progress')>{{ __('dental_labs.status_in_progress') }}</option>
            <option value="received" @selected(old('status', $case->status->value ?? '') === 'received')>{{ __('dental_labs.status_received') }}</option>
            <option value="delivered" @selected(old('status', $case->status->value ?? '') === 'delivered')>{{ __('dental_labs.status_delivered') }}</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('dental_labs.sent_date') }}</label>
        <input type="date" name="sent_date" class="form-control" required
            value="{{ old('sent_date', optional($case?->sent_date)->format('Y-m-d')) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('dental_labs.expected_return') }}</label>
        <input type="date" name="expected_return_date" class="form-control"
            value="{{ old('expected_return_date', optional($case?->expected_return_date)->format('Y-m-d')) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('dental_labs.actual_return') }}</label>
        <input type="date" name="actual_return_date" class="form-control"
            value="{{ old('actual_return_date', optional($case?->actual_return_date)->format('Y-m-d')) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('dental_labs.cost') }}</label>
        <input type="number" step="0.01" min="0" name="cost" class="form-control" required value="{{ old('cost', $case->cost ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">{{ __('dental_labs.notes') }}</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $case->notes ?? '') }}</textarea>
    </div>
</div>