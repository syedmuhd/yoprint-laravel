<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'name',
        'pms_color'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
