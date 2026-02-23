<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart_adds_correct_quantity()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test Description',
            'base_price' => 100,
            'stock_quantity' => 10,
            'sku' => 'SKU-001',
        ]);

        // 1. Add to cart for the first time
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(1, CartItem::where('user_id', $user->id)->where('product_id', $product->id)->first()->quantity);

        // 2. Add the same item again
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart', [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $this->assertEquals(2, CartItem::where('user_id', $user->id)->where('product_id', $product->id)->first()->quantity);
    }

    public function test_sync_cart_synchronizes_guest_items()
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Sync Product',
            'slug' => 'sync-product',
            'description' => 'Test Description',
            'base_price' => 50,
            'stock_quantity' => 10,
            'sku' => 'SKU-SYNC',
        ]);

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 3,
                'options' => null,
            ]
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart/sync', [
                'items' => $items,
            ]);

        $response->assertStatus(200);
        $this->assertEquals(3, CartItem::where('user_id', $user->id)->where('product_id', $product->id)->first()->quantity);
    }
}
