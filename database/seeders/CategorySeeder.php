<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Script',
                'slug' => 'script',
                'description' => 'Bot scripts, automation scripts, dan utility scripts',
                'display_order' => 1,
            ],
            [
                'name' => 'Template',
                'slug' => 'template',
                'description' => 'Website templates, email templates, dan document templates',
                'display_order' => 2,
            ],
            [
                'name' => 'Plugin',
                'slug' => 'plugin',
                'description' => 'Browser extensions, WordPress plugins, dan aplikasi plugins',
                'display_order' => 3,
            ],
            [
                'name' => 'E-Book',
                'slug' => 'ebook',
                'description' => 'Digital books dan guides',
                'display_order' => 4,
            ],
            [
                'name' => 'UI Kit',
                'slug' => 'ui-kit',
                'description' => 'Design assets, icons, dan UI components',
                'display_order' => 5,
            ],
            [
                'name' => 'Theme',
                'slug' => 'theme',
                'description' => 'Website themes dan WordPress themes',
                'display_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
