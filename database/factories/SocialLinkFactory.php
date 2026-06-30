<?php

namespace Database\Factories;

use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialLinkFactory extends Factory
{
    protected $model = SocialLink::class;

    public function definition(): array
    {
        return [
            'platform' => $this->faker->randomElement(['github', 'linkedin', 'twitter', 'instagram']),
            'label' => $this->faker->words(2, true),
            'url' => $this->faker->url(),
            'display_order' => $this->faker->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
