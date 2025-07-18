<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Upload;
use App\Enums\UploadStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Jobs\ProcessUpload;

class UploadService
{
    public function handle(UploadedFile $file): Upload
    {
        // Generate unique filename with extension
        $filename = 'upload_' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store uploaded file in storage/app/uploads with generated name
        $path = $file->storeAs('uploads', $filename);

        // Create upload record
        $upload = Upload::create([
            // FIX: Store the filename and the path correctly
            'filename' => $file->getClientOriginalName(), // Store original filename for display
            'file_path' => $path,                    // Store the actual path for processing
            'status' => UploadStatus::Pending,
        ]);

        ProcessUpload::dispatch($upload);

        return $upload;
    }
}