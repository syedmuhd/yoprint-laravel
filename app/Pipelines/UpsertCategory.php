<?php

namespace App\Pipelines;

use App\Models\Category;
use Closure;

class UpsertCategory
{
    public function handle(array $row, Closure $next): array
    {

        /**
         * Logic for Category
         * Many products belong to same category
         * 1. Check if category name is already exist
         * 2. If not yet exist, create the category and assign category id to the product
         * 3. If already exist, assign category id to the product
         * 4. If already exist, but category id is changed, assign the updated category id
         */

        // Fetch or create the category by name
        $category = Category::firstOrCreate(
            ['name' => $row['category_name']],
            ['name' => $row['category_name']]
        );

        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        // Assign category_id if changed
        if ($product->category_id !== $category->id) {
            $product->category_id = $category->id;

            if ($product->isDirty('category_id')) {
                $product->save();
            }
        }

        $row['__product'] = $product;
        
        // 💡 Make category_id available directly for later pipelines
        $row['category_id'] = $category->id;

        return $next($row);
    }
}
