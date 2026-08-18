<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ToothChartController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DentalLabController;
use App\Http\Controllers\LabCaseController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\InsuranceContractController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\FinancialTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Guest-only login (Sanctum-backed session auth; no public registration).
// Locale switch is available to guests too, so the login page itself
// can be viewed in Arabic or English before signing in.
Route::post('/locale', [ProfileController::class, 'updateLocale'])->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile - full page for every user.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.theme');

    // Doctors - view for both roles, mutations Doctor-only (policy +
    // route middleware, belt and braces).
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::middleware('role:Doctor')->group(function () {
        Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
        Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
        Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
        Route::post('/doctors/{doctor}/reactivate', [DoctorController::class, 'reactivate'])->name('doctors.reactivate');
    });

    // Rooms - same pattern as Doctors.
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::middleware('role:Doctor')->group(function () {
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::post('/rooms/{room}/reactivate', [RoomController::class, 'reactivate'])->name('rooms.reactivate');
    });

    // Treatments - view for both (needed when booking appointments),
    // catalog edits Doctor-only.
    Route::get('/treatments', [TreatmentController::class, 'index'])->name('treatments.index');
    Route::get('/treatments/{treatment}', [TreatmentController::class, 'show'])->name('treatments.show');
    Route::middleware('role:Doctor')->group(function () {
        Route::post('/treatments', [TreatmentController::class, 'store'])->name('treatments.store');
        Route::put('/treatments/{treatment}', [TreatmentController::class, 'update'])->name('treatments.update');
        Route::delete('/treatments/{treatment}', [TreatmentController::class, 'destroy'])->name('treatments.destroy');
        Route::post('/treatments/{treatment}/reactivate', [TreatmentController::class, 'reactivate'])->name('treatments.reactivate');
    });

    // Patients - first-class module; Receptionist creates/edits but
    // never deletes.
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::middleware('role:Doctor')->group(function () {
        Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    });
    Route::resource('patients', PatientController::class);

    // Appointments - daily schedule + full cross-field search.
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/search', [AppointmentController::class, 'search'])->name('appointments.search');
    Route::get('/appointments/availability', [AppointmentController::class, 'availability'])->name('appointments.availability');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::middleware('role:Doctor')->group(function () {
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    });
    Route::post('/appointments/quick-patient', [AppointmentController::class, 'quickPatient'])
    ->name('appointments.quick-patient');

    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/data', [AgendaController::class, 'data'])->name('agenda.data');

    // Payments - nested under a patient; Receptionist records
    // payments/installments but never deletes history.
    Route::post('/patients/{patient}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/{payment}/installments', [PaymentController::class, 'addInstallment'])->name('payments.installments.store');
    Route::middleware('role:Doctor')->group(function () {
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // Inventory - Receptionist may only adjust quantity.
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{item}/quantity', [InventoryController::class, 'updateQuantity'])->name('inventory.quantity');
    Route::middleware('role:Doctor')->group(function () {
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::put('/inventory/{item}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });

    // User Management - Doctor only, full page (not a modal).
    Route::middleware('role:Doctor')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    });

    // Backups - Receptionist may view/download history only.
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::middleware('role:Doctor')->group(function () {
        Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
        Route::post('/backups/import', [BackupController::class, 'import'])->name('backups.import');
        Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });

    // Activity log - Doctor only, sensitive audit data.
    Route::middleware('role:Doctor')->group(function () {
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });

    // Tooth Chart
    Route::get('/patients/{patient}/tooth-chart', [ToothChartController::class, 'show'])->name('patients.tooth-chart');
    Route::post('/patients/{patient}/tooth-chart', [ToothChartController::class, 'update'])->name('patients.tooth-chart.update');
    Route::delete('/patients/{patient}/tooth-chart/{tooth_number}', [ToothChartController::class, 'destroy'])->name('patients.tooth-chart.destroy');
    Route::get('/patients/lookup', [PatientController::class, 'search'])
    ->name('patients.lookup');
    Route::get('/lab-cases/patient-lookup', [LabCaseController::class, 'patientLookup'])
    ->name('lab-cases.patient-lookup');
    
    // Procurement: Doctors and Receptionists can work with records; only Doctors delete.
    Route::middleware('role:Doctor|Receptionist')->group(function () {
        Route::resource('suppliers', SupplierController::class)->except(['show', 'create', 'edit', 'destroy']);
        Route::resource('purchases', PurchaseController::class)->except(['show', 'create', 'edit', 'destroy']);
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->middleware('role:Doctor')->name('suppliers.destroy');
        Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->middleware('role:Doctor')->name('purchases.destroy');

        Route::resource('dental-labs', DentalLabController::class)
            ->except(['show', 'create', 'edit', 'destroy'])
            ->parameters(['dental-labs' => 'dentalLab']);
            
        Route::resource('lab-cases', LabCaseController::class)
            ->except(['show', 'create', 'edit', 'destroy'])
            ->parameters(['lab-cases' => 'labCase']);

        Route::delete('/dental-labs/{dentalLab}', [DentalLabController::class, 'destroy'])->middleware('role:Doctor')->name('dental-labs.destroy');
        Route::delete('/lab-cases/{labCase}', [LabCaseController::class, 'destroy'])->middleware('role:Doctor')->name('lab-cases.destroy');
        Route::get('/lab-cases/patient-lookup', [LabCaseController::class, 'patientLookup'])->name('lab-cases.patient-lookup');
    });

    Route::middleware('role:Doctor')->group(function () {
        Route::resource('assets', AssetController::class)->except(['show', 'create', 'edit']);
        Route::resource('insurance', InsuranceContractController::class)
            ->except(['show', 'create', 'edit'])
            ->parameters(['insurance' => 'insurance']);
        Route::resource('employees', EmployeeController::class)->except(['show', 'create', 'edit']);

           Route::resource('finance', FinancialTransactionController::class)
            ->except(['show', 'create', 'edit'])
            ->parameters(['finance' => 'transaction']);
            
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    });
    
    
    
});
