<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['product:read', 'product:write']);
    }

    public function test_can_list_products_paginated(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson('/api/products')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_can_filter_products_by_search(): void
    {
        Product::factory()->create(['name' => 'Widget Alpha']);
        Product::factory()->create(['name' => 'Gadget Beta']);

        $this->getJson('/api/products?search=Widget')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_can_filter_products_by_price_range(): void
    {
        Product::factory()->create(['price' => 10.00]);
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 100.00]);

        $this->getJson('/api/products?min_price=20&max_price=60')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_can_create_a_product(): void
    {
        $this->postJson('/api/products', [
            'name' => 'New Product',
            'description' => 'A description',
            'price' => 29.99,
            'stock_quantity' => 10,
        ])->assertStatus(201)
            ->assertJsonPath('data.name', 'New Product');

        $this->assertSame(1, Product::count());
    }

    public function test_cannot_create_product_with_duplicate_name(): void
    {
        Product::factory()->create(['name' => 'Existing Product']);

        $this->postJson('/api/products', [
            'name' => 'Existing Product',
            'price' => 10.00,
            'stock_quantity' => 5,
        ])->assertStatus(422);
    }

    public function test_create_product_validation_rejects_missing_required_fields(): void
    {
        $this->postJson('/api/products', [])->assertStatus(422);
    }

    public function test_create_product_validation_rejects_negative_price(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Bad Product',
            'price' => -1,
            'stock_quantity' => 5,
        ])->assertStatus(422);
    }

    public function test_create_product_validation_rejects_negative_stock(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Bad Product',
            'price' => 10,
            'stock_quantity' => -1,
        ])->assertStatus(422);
    }

    public function test_can_show_a_product(): void
    {
        $product = Product::factory()->create();

        $this->getJson("/api/products/{$product->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_show_returns_404_for_non_existent_product(): void
    {
        $this->getJson('/api/products/9999')->assertStatus(404);
    }

    public function test_can_update_a_product(): void
    {
        $product = Product::factory()->create();

        $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Name',
            'price' => 99.99,
            'stock_quantity' => 20,
        ])->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');
    }

    public function test_cannot_update_product_to_an_already_used_name(): void
    {
        Product::factory()->create(['name' => 'Product One']);
        $product2 = Product::factory()->create(['name' => 'Product Two']);

        $this->putJson("/api/products/{$product2->id}", [
            'name' => 'Product One',
            'price' => 10.00,
            'stock_quantity' => 5,
        ])->assertStatus(422);
    }

    public function test_can_update_product_keeping_its_own_name(): void
    {
        $product = Product::factory()->create(['name' => 'My Product', 'price' => 10.00, 'stock_quantity' => 5]);

        $this->putJson("/api/products/{$product->id}", [
            'name' => 'My Product',
            'price' => 20.00,
            'stock_quantity' => 5,
        ])->assertStatus(200)
            ->assertJsonPath('data.price', 20.00);
    }

    public function test_can_soft_delete_a_product(): void
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}")->assertStatus(200);

        $this->assertSame(0, Product::count());
        $this->assertSame(1, Product::withTrashed()->count());
    }

    public function test_deleted_product_does_not_appear_in_index(): void
    {
        Product::factory()->count(2)->create();
        $deleted = Product::factory()->create();
        $deleted->delete();

        $this->getJson('/api/products')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data.data');
    }

    public function test_show_returns_404_for_soft_deleted_product(): void
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->getJson("/api/products/{$product->id}")->assertStatus(404);
    }
}
