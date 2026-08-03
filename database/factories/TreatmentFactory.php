<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Treatment>
 */
class TreatmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Filling', 'Cleaning', 'Extraction', 'Whitening']),
            'default_cost' => fake()->randomFloat(2, 200, 3000),
            'is_multi_session' => false,
            'is_active' => true,
        ];
    }
}
