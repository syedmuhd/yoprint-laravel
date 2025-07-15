<?php

declare(strict_types= 1);

namespace App\Services;

use App\Models\Upload;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CsvImport;

class ProcessCsvService {
    public function handle (Upload $upload): void {
        $path = storage_path("app/private/". $upload->filename);

        Excel::import(new CsvImport($upload), $path);
    }
}