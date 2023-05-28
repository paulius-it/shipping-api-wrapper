<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shipping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_his_created_shippings()
    {
        $shippingUser           = User::factory()->create();
        $shippingRecord = Shipping::factory()->create([
            'user_id' => $shippingUser->id,
        ]);

        $response = $this->actingAs($shippingUser)->getJson('/api/shippings');

        $response->assertStatus(200);
    }

    public function test_user_can_initiate_shipping()
    {
        $user = User::factory()->create();

        $shippingRecord = Shipping::factory()->make([
    'item' => 'Shipping item from LP Express',
]);

        $response = $this->actingAs($user)->postJson('/api/shippings', $shippingRecord->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('shipping', [
            'item' => 'Shipping item from LP Express',
        ]);
    }

    public function test_user_can_update_shipping()
    {
        $user    = User::factory()->create();
        $Shipping = Shipping::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson('/api/shippings/' . $Shipping->id, [
            'item' => 'Updated shipping item',
        ]);

        $response->assertStatus(202);

        $this->assertDatabaseHas('shipping', [
            'item' => 'Updated shipping item',
        ]);
    }

    public function test_user_can_delete_shipping()
    {
        $user    = User::factory()->create();
        $shipping = Shipping::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson('/api/shippings/' . $shipping->id);

        $response->assertOk();

        $this->assertDatabaseMissing('shipping', [
            'id' => $shipping->id,
            'deleted_at' => NULL
        ]);
    }
}
