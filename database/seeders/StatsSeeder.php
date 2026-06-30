<?php

namespace Database\Seeders;

use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatsSeeder extends Seeder
{
    public function run(): void
    {
        Stat::updateOrCreate(['id' => 1], [
            'projects_count' => 3,
            'semesters_count' => 4,
            'experiences_count' => 3,
        ]);
    }
}
