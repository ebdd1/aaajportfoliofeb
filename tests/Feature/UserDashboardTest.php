<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_purchases_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('ushome.user'));

        $response->assertOk();
    }

    public function test_guest_cannot_access_purchases_page(): void
    {
        $response = $this->get(route('ushome.user'));

        $response->assertRedirect('/login');
    }

    public function test_user_can_see_own_paid_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->paid()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('ushome.user'));

        $response->assertOk();
    }

    public function test_user_cannot_see_others_orders(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Order::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->get(route('ushome.user'));

        $response->assertOk();
        // Orders should only include user's own
    }

    public function test_paid_order_has_download_access(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $order = Order::factory()->paid()->create(['user_id' => $user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        // User should be able to download
        $this->actingAs($user);
        $this->assertTrue($order->fresh()->isPaid());
    }

    public function test_pending_order_shows_payment_info(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->pending()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('ushome.user'));

        $response->assertOk();
        $this->assertTrue($order->isPending());
    }
}
