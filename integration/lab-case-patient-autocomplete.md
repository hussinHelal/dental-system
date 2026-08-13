# Lab Case Patient Autocomplete

Lab Cases now use an async patient autocomplete instead of loading a patient `<select>` containing every patient.

The module route is:

```php
Route::get('/lab-cases/patient-lookup', [LabCaseController::class, 'patientLookup'])
    ->name('lab-cases.patient-lookup');
```

The endpoint accepts `q` and returns up to 8 patients with `id`, `name`, and `phone`.
