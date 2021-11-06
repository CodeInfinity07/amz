<?php

namespace App\Repositories;

use App\Models\Price;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getAllProducts(int $categoryId = null, string $name = null)
    {
        $latestPrice = DB::table('prices')
            ->select('product_asin', DB::raw('MAX(updated_at) as last_price_updated_at'))
            ->groupBy('product_asin');

        $queryProducts = Product::joinSub($latestPrice, 'latest_price', function ($join) {
            $join->on('products.asin', '=', 'latest_price.product_asin');
        })->join('prices', function ($join) {
            $join->on('latest_price.product_asin', '=', 'prices.product_asin');
            $join->on('latest_price.last_price_updated_at', '=', 'prices.updated_at');
        });

        if ($categoryId) {
            $queryProducts = $queryProducts->where('category_id', $categoryId);
        }

        if ($name) {
            $queryProducts = $queryProducts->where('products.name', 'LIKE', '%' . $name . '%');
        }

        return $queryProducts->orderBy('prices.price')->get();
    }

    /**
     * @inheritdoc
     */
    public function storeProduct(array $data): ?Product
    {
        $validator = Validator::make($data, [
            'asin' => 'required|string',
            'name' => 'required|string',
            'category_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            logger($validator->getMessageBag()->first());
            new Exception("Impossible Store Product: " . $validator->getMessageBag()->first());
        }

        $product = Product::updateOrCreate(
            ['asin' => $data['asin']],
            $data
        );


        return $product;
    }

    /**
     * @inheritdoc
     */
    public function updatePrice(Product $product, float $productPrice): ?Price
    {

        return $product->prices()->create([
            'price' => $productPrice
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getProduct(string $asin): Product
    {
        $product = Product::with('category')->findOrFail($asin);

        return $product;
    }
}
