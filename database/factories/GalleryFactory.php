<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gallery>
 */
class GalleryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->optional(0.5)->sentence(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
