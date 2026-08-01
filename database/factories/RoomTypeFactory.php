<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    public function definition(): array
    {
        $types = ['Standard', 'Deluxe', 'Suite', 'Executive', 'Presidential', 'Family', 'Twin', 'Queen'];

        return [
            'hotel_id' => Hotel::factory(),
            'name' => fake()->randomElement($types),
            'description' => fake()->sentence(),
            'base_price' => fake()->randomFloat(2, 50, 500),
            'max_guests' => fake()->numberBetween(1, 6),
            'max_children' => fake()->numberBetween(0, 3),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'base_price' => $price,
        ]);
    }
}
