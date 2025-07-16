<?php

namespace App\Pipelines;

use App\Models\Image;
use Closure;

class UpsertImage
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        // If product already has an image, use it. Otherwise, create new.
        $image = $product->image ?? new Image();

        $image->fill([
            'brand_logo_image' => $row['brand_logo_image'] ?? null,
            'thumbnail_image' => $row['thumbnail_image'] ?? null,
            'color_swatch_image' => $row['color_swatch_image'] ?? null,
            'product_image' => $row['product_image'] ?? null,
            'color_square_image' => $row['color_square_image'] ?? null,
            'color_product_image' => $row['color_product_image'] ?? null,
            'color_product_image_thumbnail' => $row['color_product_image_thumbnail'] ?? null,
            'front_model_image_url' => $row['front_model_image_url'] ?? null,
            'back_model_image' => $row['back_model_image'] ?? null,
            'front_flat_image' => $row['front_flat_image'] ?? null,
            'back_flat_image' => $row['back_flat_image'] ?? null,
        ]);

        if ($image->isDirty() || !$image->exists) {
            $image->save();
        }

        // Link image to product if changed
        if ($product->image_id !== $image->id) {
            $product->image_id = $image->id;

            if ($product->isDirty('image_id')) {
                $product->save();
            }
        }

        $row['__product'] = $product;

        return $next($row);
    }
}
