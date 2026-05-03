<?php

namespace App\DAOs;

use App\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductDAO
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<Product>
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query();

        if (isset($filters['search']) && $filters['search'] !== '') {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        if (isset($filters['min_stock']) && $filters['min_stock'] !== '') {
            $query->where('stock_quantity', '>=', (int) $filters['min_stock']);
        }

        if (isset($filters['max_stock']) && $filters['max_stock'] !== '') {
            $query->where('stock_quantity', '<=', (int) $filters['max_stock']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function findById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function create(ProductDTO $dto): Product
    {
        return Product::create([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'stock_quantity' => $dto->stockQuantity,
        ]);
    }

    public function update(Product $product, ProductDTO $dto): Product
    {
        $product->update([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'stock_quantity' => $dto->stockQuantity,
        ]);

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $query = Product::where('name', $name);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
