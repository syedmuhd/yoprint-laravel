<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Upload;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CsvImport;

class ProcessCsvService
{
    public function handle(Upload $upload, int $totalRows, string $filePath): void
    {
        $import = new CsvImport($upload);

        $import->totalRows = $totalRows;

        Excel::import($import, $filePath);
    }
}