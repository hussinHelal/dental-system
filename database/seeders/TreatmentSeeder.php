<?php

namespace Database\Seeders;

use App\Models\Treatment;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            ['name' => 'Root Canal', 'category' => 'Endodontics', 'description' => 'Multi-visit root canal therapy.', 'typical_duration_minutes' => 60, 'default_cost' => 3500, 'is_multi_session' => true],
            ['name' => 'Filling', 'category' => 'Restorative', 'description' => 'Composite filling for a single cavity.', 'typical_duration_minutes' => 30, 'default_cost' => 600, 'is_multi_session' => false],
            ['name' => 'Extraction', 'category' => 'Oral Surgery', 'description' => 'Simple tooth extraction.', 'typical_duration_minutes' => 30, 'default_cost' => 500, 'is_multi_session' => false],
            ['name' => 'Cleaning', 'category' => 'Preventive', 'description' => 'Routine scale and polish.', 'typical_duration_minutes' => 45, 'default_cost' => 400, 'is_multi_session' => false],
            ['name' => 'Whitening', 'category' => 'Cosmetic', 'description' => 'In-clinic whitening session.', 'typical_duration_minutes' => 60, 'default_cost' => 1800, 'is_multi_session' => false],
        ];

        foreach ($treatments as $treatment) {
            Treatment::firstOrCreate(['name' => $treatment['name']], $treatment + ['is_active' => true]);
        }
    }
}
