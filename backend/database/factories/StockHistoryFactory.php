<?php

namespace Database\Factories;

use App\Enums\StockType;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockHistory>
 */
class StockHistoryFactory extends Factory
{
    protected $model = StockHistory::class;

    public function definition(): array
    {
        $previous = $this->faker->numberBetween(0, 50);
        $quantity = $this->faker->numberBetween(1, 20);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(StockType::cases()),
            'quantity' => $quantity,
            'previous_stock' => $previous,
            'current_stock' => $previous + $quantity,
        ];
    }
}
