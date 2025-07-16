<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Subcategory belongs to a Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Optional: Subcategory has many Products (if needed).
     * If each subcategory can have multiple products.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
