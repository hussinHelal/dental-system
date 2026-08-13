# Patient autocomplete integration

The package provides `GET /patients/lookup?q=...` and the reusable `public/js/dental-ui.js` autocomplete implementation.

For the existing Appointment Patient field, replace the current patient selector/input with `integration/patient-autocomplete.blade.php`, or reproduce its markup in the appointment modal. Make sure the appointment form includes the hidden `patient_id` input.

Include the JS once through the existing layout stack:

```blade
@push('scripts')
    <script src="{{ asset('js/dental-ui.js') }}" defer></script>
@endpush
```

The autocomplete searches by patient name or phone, returns up to 8 matches, and supports mouse selection plus ArrowUp/ArrowDown/Enter/Escape.
