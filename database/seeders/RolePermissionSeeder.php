<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\ManagedPages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeds the fixed set of Spatie permissions (one "view" and one "manage"
 * permission per entry in ManagedPages, plus the standalone "manage
 * roles" permission that is NOT part of the page grid — see
 * RoleController's docblock) and the default roles that use them.
 *
 * "manage roles" is intentionally granted ONLY to Admin Doctor. It's
 * the single permission that gates RoleController, and it is not, and
 * must never be, exposed as a page checkbox — see ManagedPages /
 * roles._form for why.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedDefaultUsers();
    }

    /**
     * Creates "view {page}" and "manage {page}" for every ManagedPages
     * entry, plus the standalone "manage roles" permission.
     */
    private function seedPermissions(): void
    {
        foreach (ManagedPages::slugs() as $slug) {
            Permission::firstOrCreate([
                'name' => ManagedPages::viewPermission($slug),
                'guard_name' => 'web',
            ]);

            Permission::firstOrCreate([
                'name' => ManagedPages::managePermission($slug),
                'guard_name' => 'web',
            ]);
        }

        Permission::firstOrCreate([
            'name' => 'manage roles',
            'guard_name' => 'web',
        ]);
    }

    private function seedRoles(): void
    {
        $allPageSlugs = ManagedPages::slugs();

        // --- Admin Doctor: everything, including role management. ---
        // is_locked = true: this is the role RoleController protects
        // from being edited/deleted (see guardAgainstLockedRole()), so
        // there is always at least one role that can grant "manage
        // roles" to someone else.
        $adminDoctor = Role::firstOrCreate(
            ['name' => 'Admin Doctor', 'guard_name' => 'web'],
            ['is_locked' => true]
        );
        $adminDoctor->syncPermissions(
            $this->allViewAndManagePermissions($allPageSlugs)->push('manage roles')
        );

        // --- Doctor: full clinical + operational access, no role mgmt. ---
        $doctor = Role::firstOrCreate(['name' => User::ROLE_DOCTOR, 'guard_name' => 'web']);
        $doctor->syncPermissions(
            $this->allViewAndManagePermissions($allPageSlugs)
        );

        // --- Receptionist: front-desk pages, manage where the routes
        // file allows it (patients, appointments, procurement per
        // 'role:Doctor|Receptionist' groups), view-only elsewhere. ---
        $receptionist = Role::firstOrCreate(['name' => User::ROLE_RECEPTIONIST, 'guard_name' => 'web']);
        $receptionist->syncPermissions(
            $this->receptionistPermissions($allPageSlugs)
        );

        // --- Assistant: view-only across the board. Adjust the
        // $assistantManageSlugs list below if Assistants should be able
        // to manage specific pages (e.g. tooth chart entries). ---
        $assistant = Role::firstOrCreate(['name' => 'Assistant', 'guard_name' => 'web']);
        $assistant->syncPermissions(
            $this->assistantPermissions($allPageSlugs)
        );
    }

    /**
     * Every "view {page}" and "manage {page}" permission that exists,
     * for roles (Admin Doctor / Doctor) that should have unrestricted
     * page access.
     */
    private function allViewAndManagePermissions(array $slugs): \Illuminate\Support\Collection
    {
        return collect($slugs)->flatMap(fn ($slug) => [
            ManagedPages::viewPermission($slug),
            ManagedPages::managePermission($slug),
        ]);
    }

    /**
     * TODO: adjust $manageSlugs to match the routes.php 'role:Doctor|Receptionist'
     * groups (patients, appointments, procurement, inventory-quantity-only, etc).
     * Everything not listed here is granted "view" only.
     */
    private function receptionistPermissions(array $allSlugs): \Illuminate\Support\Collection
    {
        $manageSlugs = [
            'patients',
            'appointments',
            'suppliers',
            'purchases',
            'dental-labs',
            'lab-cases',
            // add/remove to match routes.php exactly
        ];

        $permissions = collect();

        foreach ($allSlugs as $slug) {
            $permissions->push(ManagedPages::viewPermission($slug));

            if (in_array($slug, $manageSlugs, true)) {
                $permissions->push(ManagedPages::managePermission($slug));
            }
        }

        return $permissions;
    }

    /**
     * TODO: adjust $manageSlugs if Assistants should manage anything
     * (e.g. tooth-chart). Defaults to view-only everywhere.
     */
    private function assistantPermissions(array $allSlugs): \Illuminate\Support\Collection
    {
        $manageSlugs = [
            // e.g. 'tooth-chart',
        ];

        $permissions = collect();

        foreach ($allSlugs as $slug) {
            $permissions->push(ManagedPages::viewPermission($slug));

            if (in_array($slug, $manageSlugs, true)) {
                $permissions->push(ManagedPages::managePermission($slug));
            }
        }

        return $permissions;
    }

    private function seedDefaultUsers(): void
    {
        // Primary admin Doctor account. Change this password after
        // first login - see the README "Default accounts" section.
        $admin = User::firstOrCreate(
            ['username' => 'doctor'],
            [
                'name' => 'Dr. Ahmed Zedan',
                'password' => Hash::make('zedan98741'),
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['Admin Doctor']);

        // A second, receptionist-facing demo login.
        $receptionist = User::firstOrCreate(
            ['username' => 'reception'],
            [
                'name' => 'Mostafa Kandel',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $receptionist->syncRoles([User::ROLE_RECEPTIONIST]);
    }
}