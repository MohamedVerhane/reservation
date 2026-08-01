<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = Carbon::now()->addDays(fake()->numberBetween(1, 30));
        $checkOut = $checkIn->copy()->addDays(fake()->numberBetween(1, 7));

        return [
            'user_id' => User::factory(),
            'hotel_id' => Hotel::factory(),
            'room_id' => Room::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => fake()->numberBetween(1, 4),
            'children_count' => fake()->numberBetween(0, 2),
            'total_price' => fake()->randomFloat(2, 100, 2000),
            'status' => Reservation::STATUS_CONFIRMED,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Reservation::STATUS_PENDING,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Reservation::STATUS_CONFIRMED,
        ]);
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Reservation::STATUS_CHECKED_IN,
        ]);
    }

    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Reservation::STATUS_CHECKED_OUT,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Reservation::STATUS_CANCELLED,
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => Carbon::now()->addDays(5),
            'check_out' => Carbon::now()->addDays(8),
            'status' => Reservation::STATUS_CONFIRMED,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => Carbon::now()->subDays(10),
            'check_out' => Carbon::now()->subDays(7),
            'status' => Reservation::STATUS_CHECKED_OUT,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => Carbon::now()->subDay(),
            'check_out' => Carbon::now()->addDays(3),
            'status' => Reservation::STATUS_CHECKED_IN,
        ]);
    }

    public function forDates(Carbon $checkIn, Carbon $checkOut): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);
    }
}
