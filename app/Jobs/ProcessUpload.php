<?php

namespace App\Jobs;

use App\Enums\UploadStatus;
use App\Models\Upload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Log;
use Throwable;
use App\Events\UploadStatusUpdated;

use App\Services\ProcessCsvService;

class ProcessUpload implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(public Upload $upload)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ProcessCsvService $service): void
    {
        try {
            $this->upload->update(['status' => UploadStatus::Processing]);
            broadcast(new UploadStatusUpdated($this->upload));

            $service->handle($this->upload);

            $this->upload->update(['status' => UploadStatus::Completed]);
            broadcast(new UploadStatusUpdated($this->upload));
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            $this->upload->update(['status' => UploadStatus::Failed]);
            broadcast(new UploadStatusUpdated($this->upload));
        }
    }
}
