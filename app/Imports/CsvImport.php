<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Pipeline\Pipeline;

use App\Pipelines\SanitizeFields;
use Log;

class CsvImport implements OnEachRow, WithHeadingRow, WithChunkReading, WithLimit
{
    public function __construct(protected $upload)
    {
    }

    public function limit(): int
    {
        return 5;
    }

    // Process each for of the CSV according to the requirement
    public function onRow(Row $row)
    {
        $r = $row->toArray();

        if (!isset($r['unique_key']) || empty($r['unique_key'])) return;

        Log::info($r['unique_key']);

        // Use Pipeline for predictable flow (and easy to read ofcourse!)
        $finalData = app(Pipeline::class)
            ->send($r)
            ->through([
                SanitizeFields::class,
            ])
            ->thenReturn();

        // Insert new record, or update existing record (will implement record dirty checks)
        Product::updateOrCreate(
            ['unique_key' => $finalData['unique_key']],
            $finalData
        );
    }

    public function chunkSize(): int
    {
        return 5;
    }
}
