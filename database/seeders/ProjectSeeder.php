<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Password Manager CLI',
                'description' => 'Password manager berbasis command-line yang mengenkripsi '
                    . 'dan mengelola kredensial secara lokal menggunakan Python '
                    . 'dengan enkripsi AES.',
                'tags' => ['Python', 'AES', 'CLI'],
                'repo_status' => 'coming_soon',
                'is_featured' => true,
                'display_order' => 1,
            ],
            [
                'title' => 'Domain Scanner Tool',
                'description' => 'Tool reconnaissance untuk memetakan informasi domain target '
                    . 'melalui DNS enumeration dan pemindaian port dengan Nmap.',
                'tags' => ['Python', 'Nmap', 'DNS Enum'],
                'repo_status' => 'coming_soon',
                'is_featured' => false,
                'display_order' => 2,
            ],
            [
                'title' => 'ZeroFive Encryption Tool',
                'description' => 'Aplikasi desktop berbasis Windows Forms untuk enkripsi dan '
                    . 'dekripsi data menggunakan XOR cipher, dibangun di atas .NET.',
                'tags' => ['C#', '.NET', 'XOR Cipher'],
                'repo_status' => 'coming_soon',
                'is_featured' => false,
                'display_order' => 3,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['title' => $project['title']],
                array_merge($project, ['is_active' => true])
            );
        }
    }
}
