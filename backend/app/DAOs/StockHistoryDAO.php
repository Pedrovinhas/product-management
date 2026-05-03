<?php

namespace App\DAOs;

use App\DTOs\StockAdjustmentDTO;
use App\Models\StockHistory;
use Illuminate\Pagination\LengthAwarePaginator;

class StockHistoryDAO
{
    public function create(int $productId, StockAdjustmentDTO $dto): StockHistory
    {
        return StockHistory::create([
            'product_id' => $productId,
            'user_id' => $dto->userId,
            'type' => $dto->type,
            'quantity' => $dto->quantity,
            'previous_stock' => $dto->previousStock,
            'current_stock' => $dto->currentStock,
        ]);
    }

    /**
     * @return LengthAwarePaginator<StockHistory>
     */
    public function listByProduct(int $productId, int $perPage = 15): LengthAwarePaginator
    {
        return StockHistory::with('user')
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
