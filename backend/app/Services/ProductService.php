<?php

namespace App\Services;

use App\DAOs\ProductDAO;
use App\DAOs\StockHistoryDAO;
use App\DTOs\ProductDTO;
use App\DTOs\StockAdjustmentDTO;
use App\Enums\StockType;
use App\Models\Product;
use DomainException;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        private readonly ProductDAO $productDAO,
        private readonly StockHistoryDAO $stockHistoryDAO,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<Product>
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productDAO->list($filters, $perPage);
    }

    public function findById(int $id): Product
    {
        return $this->productDAO->findById($id);
    }

    public function create(ProductDTO $dto, int $userId): Product
    {
        if ($this->productDAO->existsByName($dto->name)) {
            throw new DomainException("A product with the name \"{$dto->name}\" already exists.");
        }

        $product = $this->productDAO->create($dto);

        if ($dto->stockQuantity > 0) {
            $this->stockHistoryDAO->create($product->id, new StockAdjustmentDTO(
                type: StockType::Entrada,
                quantity: $dto->stockQuantity,
                userId: $userId,
                previousStock: 0,
                currentStock: $dto->stockQuantity,
            ));
        }

        return $product;
    }

    public function update(int $id, ProductDTO $dto, int $userId): Product
    {
        $product = $this->productDAO->findById($id);

        if ($this->productDAO->existsByName($dto->name, $product->id)) {
            throw new DomainException("A product with the name \"{$dto->name}\" already exists.");
        }

        $previousStock = $product->stock_quantity;
        $updated = $this->productDAO->update($product, $dto);

        if ($dto->stockQuantity !== $previousStock) {
            $diff = $dto->stockQuantity - $previousStock;
            $type = $diff > 0 ? StockType::Entrada : StockType::Saida;

            $this->stockHistoryDAO->create($updated->id, new StockAdjustmentDTO(
                type: $type,
                quantity: abs($diff),
                userId: $userId,
                previousStock: $previousStock,
                currentStock: $dto->stockQuantity,
            ));
        }

        return $updated;
    }

    public function delete(int $id): void
    {
        $product = $this->productDAO->findById($id);
        $this->productDAO->delete($product);
    }
}
