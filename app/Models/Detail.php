<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $fillable = [
        'description',
        'style',
        'spec_sheet',
        'piece_weight',
        'companion_styles'
    ];
}
