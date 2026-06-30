<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::updateOrCreate(['id' => 1], [
            'name' => 'Febryanus Tambing',
            'tagline' => 'Cybersecurity Enthusiast & API Integration Specialist',
            'bio' => 'Mahasiswa Informatika semester 4 yang mendalami reconnaissance, '
                . 'network security, dan post-exploitation — sambil membangun '
                . 'pengalaman nyata lewat integrasi API dan proyek keamanan siber dari nol.',
            'email' => 'febryanustambling54@gmail.com',
            'university' => 'Universitas Cokroaminoto Palopo',
            'major' => 'Informatika',
            'semester' => '4',
            'meta_title' => 'Febryanus Tambing — Cybersecurity Enthusiast & API Integration Specialist',
            'meta_description' => 'Portfolio Febryanus Tambing, mahasiswa Informatika UNCP, '
                . 'fokus reconnaissance, network security, dan post-exploitation.',
        ]);
    }
}
