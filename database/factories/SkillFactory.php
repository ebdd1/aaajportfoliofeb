<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'category_number' => str_pad($this->faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
            'category_label' => $this->faker->randomElement(['Programming', 'Framework', 'Tool', 'Database']),
            'category_title' => $this->faker->sentence(2),
            'tags' => [$this->faker->word(), $this->faker->word(), $this->faker->word()],
            'display_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
