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
                'is_active' => true,
            ]
        );

        Doctor::firstOrCreate(
            ['name' => 'Dr. Maryem Awaga'],
            [
                'user_id' => null,
                'specialty' => 'General & Cosmetic Dentistry',
                'is_active' => true,
            ]
        );
    }
}
