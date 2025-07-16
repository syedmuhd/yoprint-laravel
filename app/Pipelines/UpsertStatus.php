<?php

namespace App\Pipelines;

use App\Models\Status;
use Closure;

class UpsertStatus
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        $status = Status::firstOrCreate(
            ['name' => $row['product_status']],
            ['name' => $row['product_status']]
        );

        // Assign status to product if changed
        if ($product->status_id !== $status->id) {
            $product->status_id = $status->id;

            if ($product->isDirty('status_id')) {
                $product->save();
            }
        }

        $row['__product'] = $product;

        return $next($row);
    }
}
