<?php

namespace App\Http\Controllers;

use App\DTOs\ProductDTO;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'min_price', 'max_price', 'min_stock', 'max_stock']);
        $products = $this->productService->list($filters);

        return response()->json([
            'data' => ProductResource::collection($products)->response()->getData(true),
            'message' => 'Products retrieved successfully.',
            'errors' => [],
        ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $product = $this->productService->create(
            ProductDTO::fromArray($request->validated()),
            $user->id,
        );

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product created successfully.',
            'errors' => [],
        ], 201, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product retrieved successfully.',
            'errors' => [],
        ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $product = $this->productService->update(
            $id,
            ProductDTO::fromArray($request->validated()),
            $user->id,
        );

        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Product updated successfully.',
            'errors' => [],
        ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json([
            'data' => null,
            'message' => 'Product deleted successfully.',
            'errors' => [],
        ]);
    }
}
