<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    /**
     * Creates the full permission/role set (via RolePermissionSeeder's
     * shared seedPermissionsAndRoles(), with no demo user) plus one
     * admin login - no demo doctors, rooms, patients, treatments,
     * inventory, appointments, or payments. Intended for a real
     * clinic's first launch (e.g. a packaged NativePHP desktop build),
     * where DatabaseSeeder's demo data would be wrong to ship.
     *
     * The first-launch admin is granted Admin Doctor (not Doctor):
     * on a brand-new install there is no one else who could later
     * grant "manage roles" to them, so they need to start with it.
     *
     * The admin username/password come from .env so every deployment
     * doesn't share the same hardcoded default credentials. If unset,
     * falls back to username "admin" and a random password that's
     * printed once to the log - check storage/logs/laravel.log after
     * first launch if you didn't set ADMIN_USERNAME/ADMIN_PASSWORD.
     */
    public function run(): void
    {
        // Creates Admin Doctor / Doctor / Receptionist / Assistant with
        // their full permission grants - previously this seeder only
        // created two bare role rows with zero permissions attached,
        // and assigned a "Doctor" role that this seeder never created,
        // which threw on every packaged first-launch and left the
        // admin account with no role at all.
        (new RolePermissionSeeder())->seedPermissionsAndRoles();

        if (User::count() > 0) {
            return;
        }

        $username = config('clinic.admin_username');
        $password = config('clinic.admin_password');
        $generatedPassword = null;

        if (! $password) {
            $generatedPassword = Str::password(12);
            $password = $generatedPassword;
        }

        $admin = User::create([
            'name' => config('clinic.admin_name'),
            'username' => $username,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);
        $admin->assignRole(User::ROLE_ADMIN_DOCTOR);

        if ($generatedPassword) {
            logger()->warning(
                "ProductionSeeder: generated first-run admin credentials - username [{$username}] password [{$generatedPassword}]. Log in and change this immediately from the Profile page."
            );
        }
    }
}
