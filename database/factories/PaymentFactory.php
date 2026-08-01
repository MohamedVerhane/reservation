<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'amount' => fake()->randomFloat(2, 50, 2000),
            'method' => fake()->randomElement([
                Payment::METHOD_CASH,
                Payment::METHOD_CREDIT_CARD,
                Payment::METHOD_DEBIT_CARD,
                Payment::METHOD_BANK_TRANSFER,
                Payment::METHOD_ONLINE,
            ]),
            'status' => Payment::STATUS_PENDING,
            'transaction_id' => 'TXN-' . time() . strtoupper(\Illuminate\Support\Str::random(8)),
            'paid_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_COMPLETED,
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_FAILED,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Payment::STATUS_REFUNDED,
        ]);
    }

    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    public function viaMethod(string $method): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => $method,
        ]);
    }
}
