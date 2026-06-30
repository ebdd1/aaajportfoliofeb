<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'febryanustambling54@gmail.com'],
            [
                'name' => 'Febryanus Tambing',
                'email' => 'febryanustambling54@gmail.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        // Create profile
        Profile::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Febryanus Tambing',
                'tagline' => 'Cybersecurity Enthusiast & API Integration Specialist',
                'bio' => 'Mahasiswa Informatika semester 4 yang mendalami reconnaissance, network security, dan post-exploitation.',
                'email' => 'febryanustambling54@gmail.com',
                'university' => 'Universitas Cokroaminoto Palopo',
                'major' => 'Informatika',
                'semester' => 4,
            ]
        );

        $this->command->info('Test data seeded successfully!');
    }
}
