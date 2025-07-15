<?php

namespace App\Pipelines;

use App\Models\Product;
use Closure;

class UpsertProduct
{
    public function handle(array $row, Closure $next): array
    {
        // Insert a new record, or update if already exist
        $product = Product::firstOrNew([
            'unique_key' => $row['unique_key'],
        ]);

        // Fill all the model attributes
        $product->fill([
            'product_title' => $row['product_title'] ?? null,
            'qty' => $row['qty'] ?? 0,
            'inventory_key' => $row['inventory_key'] ?? null,
            'size_index' => $row['size_index'] ?? null,
            'product_measurements' => $row['product_measurements'] ?? null,
            'gtin' => $row['gtin'] ?? null,
        ]);

        // If there's any updates needed, execute it.
        if ($product->isDirty() || !$product->exists) {
            $product->save();
        }

        // Attach product instance for next pipeline (if any)
        $row['__product'] = $product;

        return $next($row);
    }
}
