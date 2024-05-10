<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFlashSale extends Model
{
    use HasFactory;
    function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
