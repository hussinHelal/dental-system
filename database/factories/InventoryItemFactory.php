<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'quantity' => fake()->numberBetween(0, 100),
            'unit' => fake()->randomElement(['box', 'piece', 'ml']),
            'low_stock_threshold' => 10,
        ];
    }
}
