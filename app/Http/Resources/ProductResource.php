<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'asin' => $this['product']->asin,
            'name' => $this['product']->name,
            'price' => $this['product']->last_price,
            'images' => json_decode($this['product']->images),
            'details' => json_decode($this['product']->details),
            'Product Details' =>  json_decode($this['product']->product_details),
            'Overview Details' => json_decode($this['product']->overview_details),
            'Product Information' => $this['product']->product_info,
            'Product Description' => $this['product']->product_description,
            'prices' => $this['product']->prices ? new PriceCollection($this['product']->prices) : [],
            'category' => new CategoryResource($this['product']->category),
            'categories' => $this['categories']
        ];
    }
}
