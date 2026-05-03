<?php

namespace Tests\Feature;

use App\Enums\StockType;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StockHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['product:read', 'product:write']);
    }

    public function test_stock_history_entry_is_created_when_product_is_created_with_stock(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Stocked Product',
            'price' => 10.00,
            'stock_quantity' => 50,
        ])->assertStatus(201);

        $this->assertSame(1, StockHistory::count());

        /** @var StockHistory $history */
        $history = StockHistory::first();
        $this->assertSame(StockType::Entrada, $history->type);
        $this->assertSame(50, $history->quantity);
        $this->assertSame(0, $history->previous_stock);
        $this->assertSame(50, $history->current_stock);
        $this->assertSame($this->user->id, $history->user_id);
    }

    public function test_no_stock_history_created_when_product_is_created_with_zero_stock(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Empty Product',
            'price' => 10.00,
            'stock_quantity' => 0,
        ])->assertStatus(201);

        $this->assertSame(0, StockHistory::count());
    }

    public function test_stock_history_records_increase_when_stock_goes_up(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->putJson("/api/products/{$product->id}", [
            'name' => $product->name,
            'price' => (float) $product->price,
            'stock_quantity' => 15,
        ])->assertStatus(200);

        /** @var StockHistory $history */
        $history = StockHistory::first();
        $this->assertSame(StockType::Entrada, $history->type);
        $this->assertSame(5, $history->quantity);
        $this->assertSame(10, $history->previous_stock);
        $this->assertSame(15, $history->current_stock);
    }

    public function test_stock_history_records_decrease_when_stock_goes_down(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 20]);

        $this->putJson("/api/products/{$product->id}", [
            'name' => $product->name,
            'price' => (float) $product->price,
            'stock_quantity' => 8,
        ])->assertStatus(200);

        /** @var StockHistory $history */
        $history = StockHistory::first();
        $this->assertSame(StockType::Saida, $history->type);
        $this->assertSame(12, $history->quantity);
        $this->assertSame(20, $history->previous_stock);
        $this->assertSame(8, $history->current_stock);
    }

    public function test_no_stock_history_created_when_stock_does_not_change_on_update(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->putJson("/api/products/{$product->id}", [
            'name' => $product->name,
            'price' => (float) $product->price,
            'stock_quantity' => 10,
        ])->assertStatus(200);

        $this->assertSame(0, StockHistory::count());
    }

    public function test_can_list_stock_history_for_a_product_paginated(): void
    {
        $product = Product::factory()->create();
        StockHistory::factory()->count(3)->create([
            'product_id' => $product->id,
            'user_id' => $this->user->id,
        ]);

        $this->getJson("/api/products/{$product->id}/stock-history")
            ->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_stock_history_endpoint_returns_404_for_non_existent_product(): void
    {
        $this->getJson('/api/products/9999/stock-history')->assertStatus(404);
    }

    public function test_stock_history_resource_includes_user_and_type_value(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Product X',
            'price' => 5.00,
            'stock_quantity' => 5,
        ]);

        /** @var Product $product */
        $product = Product::where('name', 'Product X')->first();

        $this->getJson("/api/products/{$product->id}/stock-history")
            ->assertStatus(200)
            ->assertJsonPath('data.data.0.type', 'entrada')
            ->assertJsonPath('data.data.0.user.id', $this->user->id);
    }
}
