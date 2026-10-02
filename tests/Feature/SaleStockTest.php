<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_reduces_product_stock(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create(['name' => 'Test Shop', 'slug' => 'test-shop']);
        $shop->users()->attach($user, ['role' => 'owner']);
        $product = Product::create(['shop_id' => $shop->id, 'name' => 'Produit test', 'reference' => 'TEST-1', 'price' => 1000, 'stock_quantity' => 5, 'active' => true]);

        $response = $this->actingAs($user)->post(route('sales.store', $shop), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 3]);
        $this->assertDatabaseCount('sales', 1);
    }

    public function test_sale_is_rejected_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $shop = Shop::create(['name' => 'Test Shop', 'slug' => 'test-shop']);
        $shop->users()->attach($user, ['role' => 'owner']);
        $product = Product::create(['shop_id' => $shop->id, 'name' => 'Produit test', 'reference' => 'TEST-2', 'price' => 1000, 'stock_quantity' => 1, 'active' => true]);

        $response = $this->actingAs($user)->post(route('sales.store', $shop), [
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 1]);
        $this->assertDatabaseCount('sales', 0);
    }
}
