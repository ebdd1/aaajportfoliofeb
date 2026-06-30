<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'slug' => fn (array $attrs) => Str::slug(fake()->words(2, true)),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->numberBetween(10000, 500000),
            'file_path' => 'products/' . Str::random(20) . '.zip',
            'thumbnail' => null,
            'version' => '1.0.0',
            'is_featured' => false,
            'is_active' => true,
            'downloads' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attrs) => ['is_active' => false]);
    }
}
