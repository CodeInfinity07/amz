<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_asin',
        'price'
    ];

    protected $casts = [
        'product_asin' => 'string',
        'product.asin' => 'string'
    ];

    public function article()
    {
        $this->belongsTo(Article::class, 'product_asin', 'asin');
    }
}
