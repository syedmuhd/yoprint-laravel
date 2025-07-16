<?php

namespace App\Pipelines;

use App\Models\Subcategory;
use Closure;

class UpsertSubcategory
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        $categoryId = $row['category_id'] ?? null;

        if (!$categoryId) {
            throw new \Exception('Category ID not set before subcategory pipe.');
        }

        // Find by name + category_id
        $subcategory = Subcategory::firstWhere([
            'name' => $row['subcategory_name'],
            'category_id' => $categoryId,
        ]);

        if (!$subcategory) {
            // Create new subcategory
            $subcategory = new Subcategory([
                'name' => $row['subcategory_name'],
            ]);

            // Use Eloquent relationship to associate category
            $subcategory->category()->associate($categoryId);
            $subcategory->save();
        } else {
            // Only update if dirty
            $subcategory->name = $row['subcategory_name'];

            // Just in case category changed (rare, but handled)
            if ($subcategory->category_id !== $categoryId) {
                $subcategory->category()->associate($categoryId);
            }

            if ($subcategory->isDirty()) {
                $subcategory->save();
            }
        }

        // Link subcategory to product if changed
        if ($product->subcategory_id !== $subcategory->id) {
            $product->subcategory_id = $subcategory->id;

            if ($product->isDirty('subcategory_id')) {
                $product->save();
            }
        }

        $row['__product'] = $product;

        return $next($row);
    }
}
