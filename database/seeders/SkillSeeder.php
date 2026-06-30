<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            [
                'category_number' => '01',
                'category_label' => 'Pengumpulan Informasi',
                'category_title' => 'Reconnaissance',
                'tags' => ['OSINT', 'DNS Enumeration', 'Nmap', 'Python'],
                'display_order' => 1,
            ],
            [
                'category_number' => '02',
                'category_label' => 'Pengamanan Sistem',
                'category_title' => 'Network Security',
                'tags' => ['Firewall', 'IDS/IPS', 'VPN', 'Wireshark'],
                'display_order' => 2,
            ],
            [
                'category_number' => '03',
                'category_label' => 'Simulasi Serangan',
                'category_title' => 'Post-Exploitation',
                'tags' => ['Havoc C2', 'XOR Cipher', 'C#/.NET', 'Payload Dev'],
                'display_order' => 3,
            ],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['category_number' => $skill['category_number']],
                array_merge($skill, ['is_active' => true])
            );
        }
    }
}
