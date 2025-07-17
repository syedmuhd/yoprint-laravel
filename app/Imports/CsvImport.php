<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Pipeline\Pipeline;

use App\Pipelines\SanitizeFields;
use App\Pipelines\UpsertProduct;
use App\Pipelines\UpsertDetail;
use App\Pipelines\UpsertCategory;
use App\Pipelines\UpsertColor;
use App\Pipelines\UpsertImage;
use App\Pipelines\UpsertMill;
use App\Pipelines\UpsertSubcategory;
use App\Pipelines\UpsertPricinggroup;
use App\Pipelines\UpsertPricing;
use App\Pipelines\UpsertSize;
use App\Pipelines\UpsertStatus;

use Log;

class CsvImport implements OnEachRow, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public function __construct(protected $upload)
    {
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

        // Each row will get processed through pipeline
        // Pipeline rocks, in future if we need to enhance this further (maybe filter or whatever needs to be done),
        // just make a new pipeline class and include it here
        app(Pipeline::class)
            ->send($r)
            ->through([
                SanitizeFields::class,
                UpsertProduct::class,
                UpsertDetail::class,
                UpsertCategory::class,
                UpsertSubcategory::class,
                UpsertColor::class,
                UpsertImage::class,
                UpsertMill::class,
                UpsertPricinggroup::class,
                UpsertPricing::class,
                UpsertSize::class,
                UpsertStatus::class,
            ])
            ->thenReturn();

        // Nice to have log
        // Log::info("Imported product: {$r['unique_key']}");

    }

    public function chunkSize(): int
    {
        return 1000; // Processing 1000 rows each time.
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
