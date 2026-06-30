<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'issuer' => $this->faker->company(),
            'issued_date' => $this->faker->date(),
            'credential_url' => $this->faker->optional()->url(),
            'file_path' => null,
            'image_path' => null,
            'display_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
