<?php

namespace Database\Factories;

use App\Models\Stat;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatFactory extends Factory
{
    protected $model = Stat::class;

    public function definition(): array
    {
        return [
            'projects_count' => $this->faker->numberBetween(1, 50),
            'semesters_count' => $this->faker->numberBetween(1, 14),
            'experiences_count' => $this->faker->numberBetween(1, 10),
        ];
    }
}
