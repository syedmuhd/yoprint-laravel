<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'brand_logo_image',
        'thumbnail_image',
        'color_swatch_image',
        'product_image',
        'name',
        'color_square_image',
        'color_product_image',
        'color_product_image_thumbnail',
        'front_model_image_url',
        'back_model_image',
        'front_flat_image',
        'back_flat_image',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
