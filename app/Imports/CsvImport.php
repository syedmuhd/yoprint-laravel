<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Color;
use App\Models\Detail;
use App\Models\Mill;
use App\Models\Size;
use App\Models\Status;
use App\Models\Subcategory;
use App\Models\Pricinggroup;
use App\Models\Pricing;
use App\Models\Image;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithUpserts;

use App\Events\ImportChunkProcessed;

class CsvImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithUpserts
{
    private array $relationCache = [
        'categories'     => [],
        'colors'         => [],
        'details'        => [],
        'mills'          => [],
        'sizes'          => [],
        'statuses'      => [],
        'subcategories'  => [],
        'pricinggroups'  => [],
        'pricings'       => [],
        'images'         => [],
    ];

    public function __construct(protected $upload)
    {
    }

    // Upsert magic happens here
    public function uniqueBy(): string
    {
        return 'unique_key';
    }

    public int $totalRows;
    private int $rowsProcessed = 0;

    // Use toModel so we can utilize batch insert
    // with barch insert, lesser query, quicker to finish the job
    public function model(array $row)
    {

       $this->rowsProcessed++;

        // dispatch the event after every chunk
        // so we can have nice progress
        if ($this->rowsProcessed % $this->chunkSize() === 0) {
            // Calculate progress percentage
            $progress = (int) (($this->rowsProcessed / $this->totalRows) * 100);

            ImportChunkProcessed::dispatch($this->upload, $progress);

            // Reset the relation cache to free up memory
            $this->relationCache = [
                'categories'     => [], 'colors' => [], 'details' => [], 'mills' => [],
                'sizes'          => [], 'statuses' => [], 'subcategories' => [],
                'pricinggroups'  => [], 'pricings' => [], 'images' => [],
            ];
        }

        if (empty($row['unique_key'])) {
            return null;
        }

        $colorId = $this->getRelationId(Color::class, 'colors', ['name' => $this->sanitize($row['color_name'] ?? 'Default'), 'pms_color' => $this->sanitize($row['pms_color'] ?? null)]);
        $millId = $this->getRelationId(Mill::class, 'mills', ['name' => $this->sanitize($row['mill'] ?? 'Unknown Mill')]);
        $statusId = $this->getRelationId(Status::class, 'statuses', ['name' => $this->sanitize($row['product_status'] ?? 'Default Status')]);
        $sizeId = $this->getSpecialSizeId($row);
        
        $categoryId = $this->getRelationId(Category::class, 'categories', ['name' => $this->sanitize($row['category_name'] ?? 'Uncategorized')]);
        $subcategoryId = $this->getSubcategoryId($row, $categoryId); // FIX: Use new helper for Subcategory

        $pricingGroupId = $this->getRelationId(Pricinggroup::class, 'pricinggroups', ['name' => $this->sanitize($row['price_group'] ?? 'Default Group')]);
        $pricingId = $this->getPricingId($row, $pricingGroupId);
        
        $detailId = $this->getRelationId(Detail::class, 'details', ['description' => $this->sanitize($row['product_description'] ?? null), 'style' => $this->sanitize($row['style'] ?? null), 'spec_sheet' => $this->sanitize($row['spec_sheet'] ?? null), 'piece_weight' => $this->sanitize($row['piece_weight'] ?? null), 'companion_styles' => $this->sanitize($row['companion_styles'] ?? null)]);
        $imageId = $this->getRelationId(Image::class, 'images', ['brand_logo_image' => $this->sanitize($row['brand_logo_image'] ?? null), 'thumbnail_image' => $this->sanitize($row['thumbnail_image'] ?? null), 'color_swatch_image' => $this->sanitize($row['color_swatch_image'] ?? null), 'product_image' => $this->sanitize($row['product_image'] ?? null), 'color_square_image' => $this->sanitize($row['color_square_image'] ?? null), 'color_product_image' => $this->sanitize($row['color_product_image'] ?? null), 'color_product_image_thumbnail' => $this->sanitize($row['color_product_image_thumbnail'] ?? null), 'front_model_image_url' => $this->sanitize($row['front_model_image_url'] ?? null), 'back_model_image' => $this->sanitize($row['back_model_image'] ?? null), 'front_flat_image' => $this->sanitize($row['front_flat_image'] ?? null), 'back_flat_image' => $this->sanitize($row['back_flat_image'] ?? null)]);

        return new Product([
            'unique_key'           => $row['unique_key'],
            'product_title'        => $this->sanitize($row['product_title'] ?? null),
            'qty'                  => $row['qty'] ?? 0,
            'inventory_key'        => $row['inventory_key'] ?? null,
            'size_index'           => $row['size_index'] ?? null,
            'product_measurements' => $row['product_measurements'] ?? null,
            'gtin'                 => $row['gtin'] ?? null,
            'category_id'          => $categoryId,
            'color_id'             => $colorId,
            'detail_id'            => $detailId,
            'mill_id'              => $millId,
            'size_id'              => $sizeId,
            'status_id'            => $statusId,
            'subcategory_id'       => $subcategoryId,
            'pricing_id'           => $pricingId,
            'image_id'             => $imageId,
        ]);
    }

    private function getRelationId(string $modelClass, string $cacheKey, array $attributes): int
    {
        $uniqueAttributeKey = http_build_query($attributes);
        if (isset($this->relationCache[$cacheKey][$uniqueAttributeKey])) {
            return $this->relationCache[$cacheKey][$uniqueAttributeKey];
        }
        $relation = $modelClass::firstOrCreate($attributes);
        return $this->relationCache[$cacheKey][$uniqueAttributeKey] = $relation->id;
    }
    
    
    private function getSubcategoryId(array $row, int $categoryId): int
    {
        $attributes = ['name' => $this->sanitize($row['subcategory_name'] ?? 'Default Subcategory')];
        $uniqueAttributeKey = http_build_query($attributes) . '&category_id=' . $categoryId;
        $cacheKey = 'subcategories';

        if (isset($this->relationCache[$cacheKey][$uniqueAttributeKey])) {
            return $this->relationCache[$cacheKey][$uniqueAttributeKey];
        }
        
        $subcategory = Subcategory::where($attributes)
                                  ->where('category_id', $categoryId)
                                  ->first();
        if (!$subcategory) {
            $subcategory = new Subcategory($attributes);
            $subcategory->category_id = $categoryId;
            $subcategory->save();
        }
        return $this->relationCache[$cacheKey][$uniqueAttributeKey] = $subcategory->id;
    }

    private function getPricingId(array $row, int $pricingGroupId): int
    {
        $attributes = ['price_text' => $this->sanitize($row['price_text'] ?? null), 'suggested_price' => $this->sanitize($row['suggested_price'] ?? null), 'piece_price' => $this->sanitize($row['piece_price'] ?? null), 'dozens_price' => $this->sanitize($row['dozens_price'] ?? null), 'case_price' => $this->sanitize($row['case_price'] ?? null), 'msrp' => $this->sanitize($row['msrp'] ?? null), 'map_pricing' => $this->sanitize($row['map_pricing'] ?? null)];
        $uniqueAttributeKey = http_build_query($attributes) . '&pricinggroup_id=' . $pricingGroupId;
        $cacheKey = 'pricings';

        if (isset($this->relationCache[$cacheKey][$uniqueAttributeKey])) {
            return $this->relationCache[$cacheKey][$uniqueAttributeKey];
        }
        
        $pricing = Pricing::where($attributes)
                          ->where('pricinggroup_id', $pricingGroupId)
                          ->first();
        if (!$pricing) {
            $pricing = new Pricing($attributes);
            $pricing->pricinggroup_id = $pricingGroupId;
            $pricing->save();
        }
        return $this->relationCache[$cacheKey][$uniqueAttributeKey] = $pricing->id;
    }

    private function getSpecialSizeId(array $row): int
    {
        $sizeName = $this->sanitize($row['size'] ?? 'One Size');
        $cacheKey = 'sizes';
        if (isset($this->relationCache[$cacheKey][$sizeName])) {
            return $this->relationCache[$cacheKey][$sizeName];
        }
        $size = Size::updateOrCreate(['name' => $sizeName], ['case_size' => $row['case_size'] ?? null]);
        return $this->relationCache[$cacheKey][$sizeName] = $size->id;
    }

    private function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        return trim($value);
    }

    public function chunkSize(): int { return 500; }
    public function batchSize(): int { return 500; }
}