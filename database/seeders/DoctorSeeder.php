<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('username', 'doctor')->first();

        Doctor::firstOrCreate(
            ['name' => 'Dr. Ahmed Zedan'],
            [
                'user_id' => $adminUser?->id,
                'specialty' => 'General & Cosmetic Dentistry',
                'phone' => '+20 100 123 4567',
                'is_active' => true,
            ]
        );

        Doctor::firstOrCreate(
            ['name' => 'Dr. Karim Fathy'],
            [
                'user_id' => null,
                'specialty' => 'Orthodontics',
                'phone' => '+20 100 765 4321',
                'is_active' => true,
            ]
        );
    }
}
