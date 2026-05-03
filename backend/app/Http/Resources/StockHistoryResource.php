<?php

namespace App\Http\Resources;

use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin StockHistory */
class StockHistoryResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'type' => $this->type->value,
            'quantity' => $this->quantity,
            'previous_stock' => $this->previous_stock,
            'current_stock' => $this->current_stock,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
