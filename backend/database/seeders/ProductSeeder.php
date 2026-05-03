<?php

namespace Database\Seeders;

use App\Enums\StockType;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Product::factory()
            ->count(10)
            ->create()
            ->each(function (Product $product) use ($user): void {
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => StockType::Entrada,
                    'quantity' => $product->stock_quantity,
                    'previous_stock' => 0,
                    'current_stock' => $product->stock_quantity,
                ]);
            });
    }
}
