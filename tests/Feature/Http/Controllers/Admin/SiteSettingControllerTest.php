<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_view_web_settings_page(): void
    {
        SiteSetting::getSingleton();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.web'));

        $response->assertOk();
        $response->assertInertia();
    }

    public function test_admin_can_update_pakasir_settings(): void
    {
        SiteSetting::getSingleton();

        $response = $this->actingAs($this->admin)->post(route('admin.settings.web.payment'), [
            'pakasir_project' => 'test-project',
            'pakasir_api_key' => 'sk_test_123456',
            'pakasir_webhook_secret' => 'whsec_test_secret',
            'pakasir_active' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('site_settings', [
            'pakasir_project' => 'test-project',
            'pakasir_api_key' => 'sk_test_123456',
            'pakasir_webhook_secret' => 'whsec_test_secret',
            'pakasir_active' => true,
        ]);
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)
            ->get(route('admin.settings.web'));

        $response->assertForbidden();
    }

    public function test_pakasir_api_key_can_be_cleared(): void
    {
        SiteSetting::getSingleton()->update([
            'pakasir_api_key' => 'old_key',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.web.payment'), [
            'pakasir_project' => 'test-project',
            'pakasir_api_key' => '',
            'pakasir_webhook_secret' => '',
            'pakasir_active' => false,
        ]);

        $response->assertRedirect();

        $settings = SiteSetting::getSingleton();
        $this->assertNull($settings->pakasir_api_key);
        $this->assertNull($settings->pakasir_webhook_secret);
    }

    public function test_webhook_secret_required_when_api_key_set(): void
    {
        SiteSetting::getSingleton();

        $response = $this->actingAs($this->admin)->post(route('admin.settings.web.payment'), [
            'pakasir_project' => 'test-project',
            'pakasir_api_key' => 'sk_test_123',
            'pakasir_active' => true,
        ]);

        $response->assertSessionHasErrors('pakasir_webhook_secret');
    }

    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get(route('admin.settings.web'));

        $response->assertRedirectToRoute('login');
    }
}
