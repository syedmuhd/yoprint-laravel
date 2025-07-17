<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Pipeline\Pipeline;
use App\Models\Upload;

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

class ImportChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public array $chunk,
        public Upload $upload
    ) {}

    public function handle(): void
    {
        foreach ($this->chunk as $r) {
            if (!isset($r['unique_key']) || empty($r['unique_key'])) {
                continue;
            }

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

                Log::info("Done : " . $r['unique_key']);
        }
    }
}
