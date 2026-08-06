<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Backup;
use App\Models\Doctor;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Treatment;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use App\Policies\ActivityLogPolicy;
use App\Policies\BackupPolicy;
use App\Policies\DoctorPolicy;
use App\Policies\InventoryItemPolicy;
use App\Policies\PatientPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\RoomPolicy;
use App\Policies\TreatmentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        //  if (config('database.default') === 'sqlite') {
        //     DB::statement('PRAGMA foreign_keys = ON;');
        // }

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        
        // Kept portable to MySQL (utf8mb4 + older versions cap indexed
        // string columns at 191 chars); harmless no-op on SQLite.
        Schema::defaultStringLength(191);

        $this->ensureDatabaseIsProvisioned();

        Gate::policy(Doctor::class, DoctorPolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Treatment::class, TreatmentPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(InventoryItem::class, InventoryItemPolicy::class);
        Gate::policy(Backup::class, BackupPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Activity::class, ActivityLogPolicy::class);
    }

    /**
     * Packaged desktop builds (NativePHP) ship with an empty database -
     * the maintainers wipe it before packaging, and an end user has no
     * terminal to run `artisan migrate --seed` themselves. This runs
     * migrations and a minimal (non-demo) seed on first launch so the
     * app is usable immediately after install.
     *
     * A marker file (not a DB query) gates this so steady-state requests
     * pay almost nothing forever after the first successful run - just
     * one file_exists() stat call, not two database round trips on
     * every single page load.
     */
    // private function ensureDatabaseIsProvisioned(): void
    // {
    //     $marker = storage_path('framework/.db_provisioned');

    //     if (file_exists($marker)) {
    //         return;
    //     }

    //     try {
    //         $usersTableExists = Schema::hasTable('users');
    //     } catch (\Throwable $e) {
    //         // Database file/connection not ready yet - nothing safe to
    //         // do here; a normal request later will retry.
    //         return;
    //     }

    //     if ($usersTableExists && User::count() > 0) {
    //         touch($marker);
    //         return;
    //     }

    //     Artisan::call('migrate', ['--force' => true]);

    //     if (User::count() === 0) {
    //         Artisan::call('db:seed', ['--force' => true, '--class' => \Database\Seeders\ProductionSeeder::class]);
    //     }

    //     touch($marker);
    // }
     private function ensureDatabaseIsProvisioned(): void
    {
        $marker = storage_path('framework/.db_provisioned');

        if (file_exists($marker)) {
            return;
        }

        try {
            $usersTableExists = Schema::hasTable('users');
        } catch (\Throwable $e) {
            return;
        }

        if ($usersTableExists && User::count() > 0) {
            touch($marker);
            return;
        }

        // Use --force and suppress output to avoid headers-already-sent
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            // Migration failed (likely locked) — don't crash the request
            return;
        }

        if (User::count() === 0) {
            try {
                Artisan::call('db:seed', [
                    '--force' => true,
                    '--class' => \Database\Seeders\ProductionSeeder::class,
                ]);
            } catch (\Throwable $e) {
                // Seeding failed
                return;
            }
        }

        touch($marker);
    }
}
