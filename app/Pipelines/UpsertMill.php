<?php

namespace App\Pipelines;

use App\Models\Mill;
use Closure;

class UpsertMill
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        $mill = Mill::firstOrCreate(
            ['name' => $row['mill']],
            ['name' => $row['mill']]
        );

        // Attach mill to product if different
        if ($product->mill_id !== $mill->id) {
            $product->mill_id = $mill->id;

            if ($product->isDirty('mill_id')) {
                $product->save();
            }
        }

        $row['__product'] = $product;

        return $next($row);
    }
}
