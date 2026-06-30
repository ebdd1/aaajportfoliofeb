<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_admin_dashboard_loads_for_authenticated_user(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
    }

    public function test_admin_dashboard_displays_total_messages(): void
    {
        $user = User::factory()->admin()->create();
        Message::factory()->count(5)->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $this->assertEquals(5, Message::count());
    }

    public function test_admin_dashboard_displays_unread_messages_count(): void
    {
        $user = User::factory()->admin()->create();
        Message::factory()->count(3)->create(['is_read' => true]);
        Message::factory()->count(7)->create(['is_read' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $this->assertEquals(7, Message::unread()->count());
    }

    public function test_admin_dashboard_displays_active_projects_count(): void
    {
        $user = User::factory()->admin()->create();
        Project::factory()->count(3)->create(['is_active' => true]);
        Project::factory()->count(5)->create(['is_active' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $this->assertEquals(3, Project::where('is_active', true)->count());
    }

    public function test_admin_dashboard_displays_active_certificates_count(): void
    {
        $user = User::factory()->admin()->create();
        Certificate::factory()->count(4)->create(['is_active' => true]);
        Certificate::factory()->count(2)->create(['is_active' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $this->assertEquals(4, Certificate::where('is_active', true)->count());
    }
}
