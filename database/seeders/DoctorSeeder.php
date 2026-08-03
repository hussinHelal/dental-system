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
            ['name' => 'Dr. Amina Zedan'],
            [
                'user_id' => $adminUser?->id,
                'specialty' => 'General & Cosmetic Dentistry',
                'phone' => '+20 100 123 4567',
                'working_hours' => [
                    'sat' => ['09:00-17:00'], 'sun' => ['09:00-17:00'], 'mon' => ['09:00-17:00'],
                    'tue' => ['09:00-17:00'], 'wed' => ['09:00-17:00'], 'thu' => ['09:00-14:00'],
                ],
                'is_active' => true,
            ]
        );

        Doctor::firstOrCreate(
            ['name' => 'Dr. Karim Fathy'],
            [
                'user_id' => null,
                'specialty' => 'Orthodontics',
                'phone' => '+20 100 765 4321',
                'working_hours' => [
                    'sat' => ['12:00-20:00'], 'mon' => ['12:00-20:00'],
                    'wed' => ['12:00-20:00'], 'thu' => ['12:00-20:00'],
                ],
                'is_active' => true,
            ]
        );
    }
}
