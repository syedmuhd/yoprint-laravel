<?php

namespace App\Pipelines;

use App\Models\Size;
use Closure;

class UpsertSize
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        // Find or create size by name
        $size = Size::firstOrNew(['name' => $row['size']]);

        // Update case_size if changed or newly created
        $size->case_size = $row['case_size'] ?? null;

        if ($size->isDirty() || !$size->exists) {
            $size->save();
        }

        // Link size to product
        if ($product->size_id !== $size->id) {
            $product->size_id = $size->id;

            if ($product->isDirty('size_id')) {
                $product->save();
            }
        }

        $row['size_id'] = $size->id;
        $row['__product'] = $product;

        return $next($row);
    }
}
