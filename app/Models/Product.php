<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'asin';
    protected $keyType = 'string';

    protected $casts = [
        'asin' => 'string',
        'prices.product_asin' => 'string'
    ];

    protected $appends = ['last_price'];

    protected $fillable = [
        'asin',
        'name',
        'images',
        'category_id',
        'product_description',
        'details',
        'overview_details',
        'product_details',
        'product_info'
    ];

    public function prices()
    {
        return $this
            ->hasMany(Price::class, 'product_asin', 'asin')
            ->orderByDesc('updated_at');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getLastPriceAttribute()
    {
        return $this->prices()
            ->first()
            ->price ?? 0;
    }
}
