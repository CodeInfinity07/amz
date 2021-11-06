<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    /**
     * Scrape Product Page on Amazon and Store info
     *
     * @param String $asin
     *
     * @return JsonResponse
     */
    public function scrapeProduct(string $asin): JsonResponse
    {
        try {
            $this->productService->handleScrapeProduct($asin);

            return response()->json(
                $this->productService->getProduct($asin),
                200,
                array(),
                JSON_PRETTY_PRINT
            );
        } catch (\Exception $e) {
            logger($e->getMessage());

            return response()->json([
                'message' => 'Impossible Scrape Product ' . $asin
            ], 404);
        }
    }


    /**
     * Fetch All Products
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(
            $this->productService->getAllProducts($request->categoryId, $request->name),
            200
        );
    }

    /**
     * Fetch single Product
     *
     * @param String $asin
     * @return JsonResource
     */
    public function show(string $asin)
    {
        return $this->productService->getProduct($asin);
    }


    /**
     * Delete product
     *
     * @param  Product $product
     * @return JsonResource
     */
    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product->asin);

        return response()->noContent();
    }
}
