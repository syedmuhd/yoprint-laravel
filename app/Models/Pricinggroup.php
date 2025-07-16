<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pricinggroup extends Model
{
    protected $fillable = [
        'name',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

}
