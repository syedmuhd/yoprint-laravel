<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'unique_key',
        'product_title',
        'qty',
        'inventory_key',
        'size_index',
        'product_measurements',
        'gtin',
    ];
}
