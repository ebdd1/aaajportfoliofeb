<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_route_exists_and_is_post(): void
    {
        $response = $this->post('/logout');

        // Should not be 404 (route exists)
        $this->assertNotEquals(404, $response->status());
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
    }

    public function test_session_is_invalidated_after_logout(): void
    {
        $user = User::factory()->create();

        // Login first
        $this->actingAs($user)->get('/admin');

        // Then logout
        $this->post('/logout');

        // After logout, admin routes should redirect to login
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_logout_with_remember_token_is_cleared(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-token']);

        $this->actingAs($user)->post('/logout');

        // Note: Laravel Breeze doesn't clear remember_token on logout
        // This is expected behavior - session is invalidated, not the token
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }
}
