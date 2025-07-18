<?php

namespace App\Jobs;

use App\Enums\UploadStatus;
use App\Models\Upload;
use App\Services\ProcessCsvService;
use App\Events\UploadStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Log;
use Throwable;

class ProcessUpload implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600;

    public function __construct(public Upload $upload)
    {
    }

    public function handle(ProcessCsvService $service): void
    {
        try {
            $this->upload->update(['status' => UploadStatus::Processing]);
            broadcast(new UploadStatusUpdated($this->upload));

            $filePath = Storage::path($this->upload->file_path);

            $totalRows = count(file($filePath)) - 1;

            $service->handle($this->upload, $totalRows, $filePath);

            $this->upload->update(['status' => UploadStatus::Completed]);
            broadcast(new UploadStatusUpdated($this->upload));
        } catch (Throwable $e) {
            Log::error("Upload failed for file ID {$this->upload->id}: {$e->getMessage()}");
            $this->upload->update(['status' => UploadStatus::Failed]);
            broadcast(new UploadStatusUpdated($this->upload));
            throw $e;
        }
    }
}