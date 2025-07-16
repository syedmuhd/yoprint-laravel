<?php

namespace App\Pipelines;

use App\Models\Detail;
use Closure;

class UpsertDetail
{
    public function handle(array $row, Closure $next): array
    {
        $detail = $row['__product']?->detail ?? new Detail();

        $detail->fill([
            'description' => $row['product_description'] ?? null,
            'style' => $row['style'] ?? null,
            'spec_sheet' => $row['spec_sheet'] ?? null,
            'piece_weight' => $row['piece_weight'] ?? null,
            'companion_styles' => $row['companion_styles'] ?? null,
        ]);

        if ($detail->isDirty() || !$detail->exists) {
            $detail->save();
        }

        $product = $row['__product'];

        // Link product to detail if changed
        if ($product->detail_id !== $detail->id) {
            $product->detail_id = $detail->id;

            if ($product->isDirty()) {
                $product->save();
            }
        }

        // Attach product instance for next pipeline (if any)
        $row['__product'] = $product;

        return $next($row);
    }
}
