<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_accessible_when_maintenance_disabled(): void
    {
        SiteSetting::getSingleton()->update(['maintenance_mode' => false]);

        $response = $this->get('/');
        $response->assertOk();
    }

    public function test_public_pages_redirect_to_maintenance_when_enabled(): void
    {
        SiteSetting::getSingleton()->update(['maintenance_mode' => true]);

        $response = $this->get('/');
        $response->assertViewIs('maintenance');
    }

    public function test_admin_can_access_when_maintenance_enabled(): void
    {
        $admin = User::factory()->admin()->create();
        SiteSetting::getSingleton()->update(['maintenance_mode' => true]);

        $response = $this->actingAs($admin)->get('/');
        $response->assertOk();
    }

    public function test_admin_can_access_settings_when_maintenance_enabled(): void
    {
        $admin = User::factory()->admin()->create();
        SiteSetting::getSingleton()->update(['maintenance_mode' => true]);

        $response = $this->actingAs($admin)->get('/admin/settings/web');
        $response->assertOk();
    }

    public function test_products_page_blocked_when_maintenance_enabled(): void
    {
        SiteSetting::getSingleton()->update(['maintenance_mode' => true]);

        $response = $this->get('/products');
        $response->assertViewIs('maintenance');
    }

    public function test_login_page_accessible_when_maintenance_enabled(): void
    {
        SiteSetting::getSingleton()->update(['maintenance_mode' => true]);

        $response = $this->get('/login');
        $response->assertOk();
    }

    public function test_maintenance_page_shows_proper_message(): void
    {
        SiteSetting::getSingleton()->update(['maintenance_mode' => true]);

        $response = $this->get('/');
        $response->assertViewHas('message');
    }
}
