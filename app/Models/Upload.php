<?php

namespace App\Models;

use App\Enums\UploadStatus;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'filename',
        'status',
    ];

    protected $casts = [
        'status' => UploadStatus::class,
    ];
}
