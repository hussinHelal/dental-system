<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            DoctorSeeder::class,
            RoomSeeder::class,
            TreatmentSeeder::class,
            PatientSeeder::class,
            InventorySeeder::class,
            AppointmentSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
