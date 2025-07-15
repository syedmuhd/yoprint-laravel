<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

use App\Services\UploadService;

class ProcessUploadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yoprint:process {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process uploaded CSV file.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // File that we'll be working on
        $filePath = $this->option('file');

        // Make sure the file exist before we copied it
        if (!$filePath || !File::exists($filePath)) {
            $this->error("Local file not found: $filePath");
            return Command::FAILURE;
        }

        // Simulate exactly like browser upload
        $uploadedFile = new UploadedFile(
            $filePath,
            basename($filePath),
            mime_content_type($filePath),
            null,
            true
        );

        // Use UploadService
        $upload = app(UploadService::class)->handle($uploadedFile);

        $this->info("File successfully uploaded: {$upload->id}");

        return Command::SUCCESS;
    }
}
