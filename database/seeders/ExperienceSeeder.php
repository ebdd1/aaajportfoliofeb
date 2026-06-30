<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = [
            [
                'period' => 'Jan 2024 – Mar 2024',
                'role' => 'Cybersecurity Intern',
                'organization' => 'CyberDefense Academy',
                'description' => 'Mempelajari dan praktik reconnaissance, vulnerability assessment, dan penetration testing pada environment lab.',
                'display_order' => 1,
            ],
            [
                'period' => 'Jul 2023 – Des 2023',
                'role' => 'API Integration Developer',
                'organization' => 'Freelance',
                'description' => 'Mengembangkan integrasi API untuk berbagai platform dan automasi workflow menggunakan Python dan Node.js.',
                'display_order' => 2,
            ],
            [
                'period' => 'Jan 2023 – Jun 2023',
                'role' => 'Web Developer',
                'organization' => 'Student Organization',
                'description' => 'Membuat dan maintenance website organisasi menggunakan Laravel dan Vue.js.',
                'display_order' => 3,
            ],
        ];

        foreach ($experiences as $experience) {
            Experience::updateOrCreate(
                ['role' => $experience['role'], 'period' => $experience['period']],
                array_merge($experience, ['is_active' => true])
            );
        }
    }
}
