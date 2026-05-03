<?php

namespace Tests\Unit;

use App\DAOs\ProductDAO;
use App\DAOs\StockHistoryDAO;
use App\DTOs\ProductDTO;
use App\DTOs\StockAdjustmentDTO;
use App\Enums\StockType;
use App\Models\Product;
use App\Services\ProductService;
use DomainException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_create_throws_domain_exception_when_product_name_already_exists(): void
    {
        $productDAO = Mockery::mock(ProductDAO::class);
        $stockHistoryDAO = Mockery::mock(StockHistoryDAO::class);

        $productDAO->shouldReceive('existsByName')
            ->once()
            ->with('Existing Product')
            ->andReturn(true);

        $service = new ProductService($productDAO, $stockHistoryDAO);

        $dto = new ProductDTO(
            name: 'Existing Product',
            description: null,
            price: 10.0,
            stockQuantity: 5,
        );

        $this->expectException(DomainException::class);
        $service->create($dto, 1);
    }

    public function test_create_persists_product_and_records_stock_history_when_stock_greater_than_zero(): void
    {
        $productDAO = Mockery::mock(ProductDAO::class);
        $stockHistoryDAO = Mockery::mock(StockHistoryDAO::class);

        $product = new Product;
        $product->id = 1;

        $productDAO->shouldReceive('existsByName')->once()->andReturn(false);
        $productDAO->shouldReceive('create')->once()->andReturn($product);

        $stockHistoryDAO->shouldReceive('create')
            ->once()
            ->withArgs(function (int $productId, StockAdjustmentDTO $dto): bool {
                return $productId === 1
                    && $dto->type === StockType::Entrada
                    && $dto->quantity === 5
                    && $dto->previousStock === 0
                    && $dto->currentStock === 5;
            });

        $service = new ProductService($productDAO, $stockHistoryDAO);

        $dto = new ProductDTO(
            name: 'New Product',
            description: null,
            price: 10.0,
            stockQuantity: 5,
        );

        $result = $service->create($dto, 1);

        $this->assertSame($product, $result);
    }

    public function test_create_does_not_record_stock_history_when_stock_is_zero(): void
    {
        $productDAO = Mockery::mock(ProductDAO::class);
        $stockHistoryDAO = Mockery::mock(StockHistoryDAO::class);

        $product = new Product;
        $product->id = 1;

        $productDAO->shouldReceive('existsByName')->once()->andReturn(false);
        $productDAO->shouldReceive('create')->once()->andReturn($product);
        $stockHistoryDAO->shouldNotReceive('create');

        $service = new ProductService($productDAO, $stockHistoryDAO);

        $dto = new ProductDTO(
            name: 'Zero Stock',
            description: null,
            price: 10.0,
            stockQuantity: 0,
        );

        $service->create($dto, 1);
    }

    public function test_update_throws_domain_exception_when_new_name_belongs_to_another_product(): void
    {
        $productDAO = Mockery::mock(ProductDAO::class);
        $stockHistoryDAO = Mockery::mock(StockHistoryDAO::class);

        $product = new Product;
        $product->id = 1;
        $product->stock_quantity = 10;

        $productDAO->shouldReceive('findById')->once()->with(1)->andReturn($product);
        $productDAO->shouldReceive('existsByName')->once()->with('Taken Name', 1)->andReturn(true);

        $service = new ProductService($productDAO, $stockHistoryDAO);

        $dto = new ProductDTO(
            name: 'Taken Name',
            description: null,
            price: 10.0,
            stockQuantity: 10,
        );

        $this->expectException(DomainException::class);
        $service->update(1, $dto, 1);
    }

    public function test_delete_soft_deletes_the_product(): void
    {
        $productDAO = Mockery::mock(ProductDAO::class);
        $stockHistoryDAO = Mockery::mock(StockHistoryDAO::class);

        $product = new Product;
        $product->id = 1;

        $productDAO->shouldReceive('findById')->once()->with(1)->andReturn($product);
        $productDAO->shouldReceive('delete')->once()->with($product);

        $service = new ProductService($productDAO, $stockHistoryDAO);
        $service->delete(1);
    }
}
