<?php

declare(strict_types= 1);

namespace App\Services;

use App\Models\Upload;

class ProcessCsvService {
    public function handle (Upload $upload): void {
        $path = storage_path("app/". $upload->filename);

        
    }
}