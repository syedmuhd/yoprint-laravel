<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Pipeline\Pipeline;

use App\Pipelines\SanitizeFields;
use App\Pipelines\UpsertProduct;
use App\Pipelines\UpsertDetail;

use Log;

class CsvImport implements OnEachRow, WithHeadingRow, WithChunkReading, WithLimit
{
    public function __construct(protected $upload)
    {
    }

    public function limit(): int
    {
        return 100;
    }

    /**
     * On each row of imported csv
     */
    public function onRow(Row $row)
    {
        // Get row as an array
        $r = $row->toArray();

        // Make sure unique_key is there alive
        if (!isset($r['unique_key']) || empty($r['unique_key']))
            return;

        // Nice to have log
        Log::info("📥 Importing product: {$r['unique_key']}");

        // Each row will get processed through pipeline
        // Pipeline rocks, in future if we need to enhance this further (maybe filter or whatever needs to be done),
        // just make a new pipeline class and include it here
        app(Pipeline::class)
            ->send($r)
            ->through([
                SanitizeFields::class,
                UpsertProduct::class,
                UpsertDetail::class,
            ])
            ->thenReturn();

        Log::info("✅ Imported product", []);

    }

    public function chunkSize(): int
    {
        return 100;
    }
}
