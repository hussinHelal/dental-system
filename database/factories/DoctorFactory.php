<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Dr. '.fake()->lastName(),
            'specialty' => fake()->randomElement(['General Dentistry', 'Orthodontics', 'Endodontics']),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
