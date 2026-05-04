<?php

namespace App\Http\Controllers;

use App\DAOs\StockHistoryDAO;
use App\Http\Resources\StockHistoryResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class StockHistoryController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly StockHistoryDAO $stockHistoryDAO,
    ) {}

    public function index(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);
        $history = $this->stockHistoryDAO->listByProduct($product->id);

        return response()->json([
            'data' => StockHistoryResource::collection($history)->response()->getData(true),
            'message' => 'Histórico de estoque recuperado com sucesso.',
            'errors' => [],
        ]);
    }
}
