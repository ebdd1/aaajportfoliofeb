<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        return [
            'period' => $this->faker->year() . ' - ' . $this->faker->year(),
            'role' => $this->faker->jobTitle(),
            'organization' => $this->faker->company(),
            'description' => $this->faker->paragraph(),
            'display_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
