<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => User::ROLE_DOCTOR, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => User::ROLE_RECEPTIONIST, 'guard_name' => 'web']);

        // Primary admin Doctor account. Change this password after
        // first login - see the README "Default accounts" section.
        $admin = User::firstOrCreate(
            ['username' => 'doctor'],
            [
                'name' => 'Dr. Amina Zedan',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->assignRole(User::ROLE_DOCTOR);

        // A second, receptionist-facing demo login.
        $receptionist = User::firstOrCreate(
            ['username' => 'reception'],
            [
                'name' => 'Layla Hassan',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $receptionist->assignRole(User::ROLE_RECEPTIONIST);
    }
}
