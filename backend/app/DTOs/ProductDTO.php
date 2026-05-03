<?php

namespace App\DTOs;

readonly class ProductDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public float $price,
        public int $stockQuantity,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            description: isset($data['description']) ? (string) $data['description'] : null,
            price: (float) $data['price'],
            stockQuantity: (int) $data['stock_quantity'],
        );
    }
}
