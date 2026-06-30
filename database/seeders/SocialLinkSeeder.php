<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            ['platform' => 'email', 'label' => 'Email', 'url' => 'mailto:febryanustambling54@gmail.com', 'display_order' => 1],
            ['platform' => 'github', 'label' => 'GitHub', 'url' => 'https://github.com/febryanustambling', 'display_order' => 2],
            ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://linkedin.com/in/febryanustambling', 'display_order' => 3],
            ['platform' => 'whatsapp', 'label' => 'WhatsApp', 'url' => 'https://wa.me/62', 'display_order' => 4],
        ];

        foreach ($links as $link) {
            SocialLink::updateOrCreate(
                ['platform' => $link['platform']],
                array_merge($link, ['is_active' => true])
            );
        }
    }
}
