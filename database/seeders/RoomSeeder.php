<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::firstOrCreate(['name' => 'Room 1'], [
            'equipment_notes' => 'Dental chair, X-ray unit, sterilizer',
            'is_active' => true,
        ]);

        Room::firstOrCreate(['name' => 'Room 2'], [
            'equipment_notes' => 'Dental chair, intraoral camera',
            'is_active' => true,
        ]);
    }
}
