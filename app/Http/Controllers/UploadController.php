<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UploadResource;
use App\Models\Upload;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Log;

class UploadController extends Controller
{
    public function index()
    {
        return UploadResource::collection(Upload::latest()->take(10)->get());
    }

    public function store(Request $request)
    {

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('file');

        $upload = app(UploadService::class)->handle($file);

        // 2. Return the resource. Laravel handles the JSON conversion.
        return new UploadResource($upload);
    }
}