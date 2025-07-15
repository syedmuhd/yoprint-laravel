<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use App\Services\UploadService;
use Log;

class UploadController extends Controller
{
    public function index()
    {
        return Upload::latest()->take(10)->get(); // return as JSON
    }

    public function store(Request $request)
    {

        ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '25M');

//         $request->validate([
//     'file' => 'required|file|mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel',
// ]);


        Log::info("after validate");

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('file');

        $upload = app(UploadService::class)->handle($file);

        return response()->json([
            'success' => true,
            'upload_id' => $upload->id,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

}
