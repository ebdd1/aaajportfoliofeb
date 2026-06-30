<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'tags' => [$this->faker->word(), $this->faker->word(), $this->faker->word()],
            'repo_url' => $this->faker->url(),
            'demo_url' => $this->faker->url(),
            'repo_status' => $this->faker->randomElement(['available', 'coming_soon', 'private']),
            'image_path' => null,
            'is_featured' => $this->faker->boolean(),
            'display_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => ['is_featured' => true, 'is_active' => true]);
    }
}
