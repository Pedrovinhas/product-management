<?php

namespace App\Models;

use App\Enums\StockType;
use Database\Factories\StockHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property StockType $type
 * @property int $quantity
 * @property int $previous_stock
 * @property int $current_stock
 * @property \Illuminate\Support\Carbon $created_at
 */
#[UseFactory(StockHistoryFactory::class)]
#[Fillable(['product_id', 'user_id', 'type', 'quantity', 'previous_stock', 'current_stock'])]
class StockHistory extends Model
{
    /** @use HasFactory<StockHistoryFactory> */
    use HasFactory;

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type' => StockType::class,
            'quantity' => 'integer',
            'previous_stock' => 'integer',
            'current_stock' => 'integer',
        ];
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
