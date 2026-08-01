<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Amenity>
 */
class AmenityFactory extends Factory
{
    public function definition(): array
    {
        $amenities = [
            ['name' => 'WiFi', 'icon' => 'bi-wifi'],
            ['name' => 'Pool', 'icon' => 'bi-water'],
            ['name' => 'Gym', 'icon' => 'bi-bicycle'],
            ['name' => 'Spa', 'icon' => 'bi-flower1'],
            ['name' => 'Restaurant', 'icon' => 'bi-cup-hot'],
            ['name' => 'Parking', 'icon' => 'bi-car-front'],
            ['name' => 'Air Conditioning', 'icon' => 'bi-snow'],
            ['name' => 'Room Service', 'icon' => 'bi-bell'],
            ['name' => 'TV', 'icon' => 'bi-tv'],
            ['name' => 'Mini Bar', 'icon' => 'bi-cup-straw'],
        ];

        $amenity = fake()->randomElement($amenities);

        return [
            'name' => $amenity['name'],
            'icon' => $amenity['icon'],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
