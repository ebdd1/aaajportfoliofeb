<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->get('/admin/profile');

        $response->assertOk();
    }

    public function test_profile_page_requires_authentication(): void
    {
        $response = $this->get('/admin/profile');

        $response->assertRedirect('/login');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->admin()->create();
        Profile::getSingleton();

        $response = $this
            ->actingAs($user)
            ->patch('/admin/profile', [
                'name' => 'New Name',
                'tagline' => 'New Tagline',
                'bio' => 'New Bio',
                'email' => $user->email,
                'university' => 'New University',
                'major' => 'New Major',
                'semester' => '6',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/profile');

        $this->assertDatabaseHas('profiles', ['name' => 'New Name']);
    }

    public function test_profile_update_validates_required_fields(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/admin/profile', []);

        $response->assertSessionHasErrors(['name', 'tagline', 'bio', 'email', 'university', 'major', 'semester']);
    }
}
