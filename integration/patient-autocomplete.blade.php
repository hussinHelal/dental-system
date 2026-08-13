{{-- Reusable patient autocomplete for appointments. Uses the application's existing patients.search endpoint. --}}
<div class="position-relative" data-patient-autocomplete data-endpoint="{{ url('/patients/search') }}">
    <input type="text"
           class="form-control"
           data-patient-search-input
           autocomplete="off"
           placeholder="{{ __('messages.search_patients') }}">
    <input type="hidden" name="patient_id" data-patient-id>
    <div class="dropdown-menu w-100 p-0 shadow-sm d-none" data-patient-results></div>
</div>
