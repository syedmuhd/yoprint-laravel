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

    /**
     * Relationships
     */
    public function detail()
    {
        return $this->belongsTo(Detail::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function pricing()
    {
        return $this->belongsTo(Pricing::class);
    }

    public function mill()
    {
        return $this->belongsTo(Mill::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
