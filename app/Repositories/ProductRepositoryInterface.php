<?php

namespace App\Repositories;

use App\Models\Price;
use App\Models\Product;

/**
 * Interface for Products Repository
 */
interface ProductRepositoryInterface
{
    /**
     * Fetch all Products
     */
    public function getAllProducts();

    /**
     * Update or Create Product
     * 
     * @param array $data
     * 
     * @return Product|null
     */
    public function storeProduct(array $data): ?Product;


    /**
     * Add a new Price on Product
     * 
     * @param Product $product
     * @param float $productPrice
     * 
     * @return Price|null
     */
    public function updatePrice(Product $product, float $productPrice): ?Price;

    /**
     * Fetch a single Product
     * 
     * @param string $asin
     * 
     * @return Product
     */
    public function getProduct(string $asin): Product;
}
