<?php

use App\Support\ManagedPages;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

/**
 * Seeds one "view {page}" and one "manage {page}" permission for every
 * page in App\Support\ManagedPages — 18 pages x 2 = 36 permissions.
 *
 * DEPENDS ON: the earlier Admin Doctor / is_locked migrations having
 * already run (needs the `roles`/`permissions` tables from
 * spatie/laravel-permission to exist).
 *
 * Safe to re-run: firstOrCreate means running this twice, or adding a new
 * page to ManagedPages later and re-running, only ever adds what's
 * missing — it never duplicates or touches existing rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (ManagedPages::allPermissionNames() as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Admin Doctor gets every page permission automatically — it's
        // meant to be the full-access role, and per the earlier migration
        // it already inherits Doctor's permissions plus "manage roles".
        // Without this, a fresh Admin Doctor would need every page
        // manually checked on first use, which defeats the point of it
        // being the top role.
        $adminDoctor = \Spatie\Permission\Models\Role::where('name', 'Admin Doctor')->first();
        if ($adminDoctor) {
            $adminDoctor->givePermissionTo(ManagedPages::allPermissionNames());
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', ManagedPages::allPermissionNames())->delete();
    }
};
