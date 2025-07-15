<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Upload;
use App\Enums\UploadStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadService
{
    public function handle(UploadedFile $file): Upload
    {
        // Get original extension
        $extension = $file->getClientOriginalExtension();

        // Generate unique filename with extension
        $filename = 'upload_' . Str::uuid() . '.' . $extension;

        // Store uploaded file in storage/app/uploads with generated name
        $path = $file->storeAs('uploads', $filename);

        // Create upload record
        $upload = Upload::create([
            'filename' => $path,
            'status' => UploadStatus::Pending,
        ]);

        // Dispatch job
        // ProcessUpload::dispatch($upload);

        return $upload;
    }
}