<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'hotel_id' => Hotel::factory(),
            'reservation_id' => null,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
            'reply' => null,
            'replied_at' => null,
            'is_approved' => true,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    public function withRating(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $rating,
        ]);
    }

    public function withReply(): static
    {
        return $this->state(fn (array $attributes) => [
            'reply' => fake()->paragraph(),
            'replied_at' => now(),
        ]);
    }

    public function forReservation(Reservation $reservation): static
    {
        return $this->state(fn (array $attributes) => [
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'hotel_id' => $reservation->hotel_id,
        ]);
    }
}
