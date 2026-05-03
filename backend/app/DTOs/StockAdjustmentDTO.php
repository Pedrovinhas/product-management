<?php

namespace App\DTOs;

use App\Enums\StockType;

readonly class StockAdjustmentDTO
{
    public function __construct(
        public StockType $type,
        public int $quantity,
        public int $userId,
        public int $previousStock,
        public int $currentStock,
    ) {}
}
