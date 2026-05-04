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

        $products = [
            [
                'name' => 'Notebook Dell Inspiron 15',
                'description' => 'Notebook Dell Inspiron 15, Intel Core i5-1235U, 8GB RAM, 512GB SSD, tela Full HD 15.6".',
                'price' => 3499.90,
                'stock_quantity' => 12,
            ],
            [
                'name' => 'Monitor LG UltraWide 29"',
                'description' => 'Monitor LG UltraWide 29WP500, resolução 2560x1080, IPS, 75Hz, entrada HDMI e USB-C.',
                'price' => 1899.00,
                'stock_quantity' => 8,
            ],
            [
                'name' => 'Teclado Mecânico Keychron K2',
                'description' => 'Teclado mecânico compacto 75%, switches Gateron Red, retroiluminação RGB, wireless Bluetooth ou USB.',
                'price' => 649.90,
                'stock_quantity' => 25,
            ],
            [
                'name' => 'Mouse Logitech MX Master 3S',
                'description' => 'Mouse ergonômico sem fio, sensor 8000 DPI, scroll MagSpeed, até 70 dias de bateria.',
                'price' => 499.90,
                'stock_quantity' => 30,
            ],
            [
                'name' => 'Headset Sony WH-1000XM5',
                'description' => 'Fone de ouvido over-ear com cancelamento de ruído líder da indústria, 30h de bateria, Bluetooth 5.2.',
                'price' => 2199.00,
                'stock_quantity' => 15,
            ],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['name' => $data['name']],
                $data,
            );

            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => StockType::Entrada,
                'quantity' => $product->stock_quantity,
                'previous_stock' => 0,
                'current_stock' => $product->stock_quantity,
            ]);
        }
    }
}
