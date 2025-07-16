<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    protected $fillable = [
        'price_text',
        'suggested_price',
        'piece_price',
        'dozens_price',
        'case_price',
        'msrp',
        'map_pricing',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pricinggroup()
    {
        return $this->belongsTo(Pricinggroup::class);
    }

}
