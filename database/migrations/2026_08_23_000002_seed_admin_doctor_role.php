<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeds the new top-level "Admin Doctor" role and the permission that gates
 * access to Role Management. Existing Doctor / Receptionist rows are left
 * completely untouched — this is additive only.
 *
 * DEPENDS ON: the `roles` table having an `is_locked` column (created in
 * the base 2024_01_01_000000_create_permission_tables.php migration —
 * this file writes to roles.is_locked).
 *
 * IMPORTANT: this migration does not assign Admin Doctor to any user. After
 * running it, promote your own account manually, e.g.:
 *   php artisan tinker
 *   >>> User::find(1)->assignRole('Admin Doctor');
 */
return new class extends Migration
{
    public function up(): void
    {
        // Reset Spatie's cached permissions so the newly created rows are
        // picked up immediately instead of on the next cache expiry.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $manageRoles = Permission::firstOrCreate([
            'name' => 'manage roles',
            'guard_name' => 'web',
        ]);

        $adminDoctor = Role::firstOrCreate([
            'name' => 'Admin Doctor',
            'guard_name' => 'web',
        ]);

        // Do not silently take ownership of a pre-existing role. If an
        // installation already had an "Admin Doctor" role, preserve its
        // existing protection state rather than forcing is_locked=true.
        // A freshly-created role is ours to protect.
        if ($adminDoctor->wasRecentlyCreated) {
            $adminDoctor->forceFill(['is_locked' => true])->save();
        }

        $adminDoctor->givePermissionTo($manageRoles);

        // Admin Doctor inherits everything the existing Doctor role can do,
        // so promoting an account doesn't take capabilities away.
        $doctorRole = Role::where('name', 'Doctor')->first();
        if ($doctorRole) {
            $adminDoctor->syncPermissions(
                $doctorRole->permissions->merge([$manageRoles])
            );
        }
    }

    public function down(): void
    {
        $manageRoles = Permission::where('name', 'manage roles')
            ->where('guard_name', 'web')
            ->first();

        // Never remove a permission that another role may rely on. This
        // migration is additive; rollback must not silently change unrelated
        // roles' capabilities.
        if ($manageRoles) {
            $role = Role::where('name', 'Admin Doctor')
                ->where('guard_name', 'web')
                ->first();

            if ($role) {
                $role->revokePermissionTo($manageRoles);
            }

            $usedElsewhere = Role::where('guard_name', 'web')
                ->whereHas('permissions', fn ($query) => $query->whereKey($manageRoles->id))
                ->exists();

            if (! $usedElsewhere) {
                $manageRoles->delete();
            }
        }

        // Never delete an existing role merely because this migration is
        // being rolled back. We cannot reliably distinguish a pre-existing
        // "Admin Doctor" row from one created by this migration without a
        // separate bookkeeping table. Leaving the row in place is safer
        // than deleting application-owned data.

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
