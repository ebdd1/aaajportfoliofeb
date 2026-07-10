<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ProfileSeeder::class,
            StatsSeeder::class,
            CategorySeeder::class,
            SkillSeeder::class,
            ExperienceSeeder::class,
            ProjectSeeder::class,
            SocialLinkSeeder::class,
            TransactionCategorySeeder::class,
            WalletSeeder::class,
        ]);
    }
}
