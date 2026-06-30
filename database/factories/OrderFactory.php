<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => 'ORD-' . strtoupper(Str::random(10)),
            'total_amount' => fake()->numberBetween(10000, 500000),
            'status' => Order::STATUS_PENDING,
            'payment_method' => fake()->randomElement(['qris', 'bni_va', 'bri_va', 'mandiri_va']),
            'payment_token' => Str::random(20),
            'payment_number' => fake()->numerify('########'),
            'expired_at' => now()->addHours(24),
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_PENDING,
            'paid_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_EXPIRED,
        ]);
    }
}
