<?php

namespace App\Pipelines;

use App\Models\Pricing;
use Closure;

class UpsertPricing
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        $pricinggroupId = $row['pricinggroup_id'] ?? null;

        if (!$pricinggroupId) {
            throw new \Exception('pricinggroup_id not set before UpsertPricing.');
        }

        // Use existing pricing or create new
        $pricing = $product->pricing ?? new Pricing();

        $pricing->fill([
            'price_text' => $row['price_text'] ?? null,
            'suggested_price' => $row['suggested_price'] ?? null,
            'piece_price' => $row['piece_price'] ?? null,
            'dozens_price' => $row['dozens_price'] ?? null,
            'case_price' => $row['case_price'] ?? null,
            'msrp' => $row['msrp'] ?? null,
            'map_pricing' => $row['map_pricing'] ?? null,
        ]);

        // Use associate() to set pricinggroup_id (relationship-safe)
        $pricing->pricinggroup()->associate($pricinggroupId);

        if ($pricing->isDirty() || !$pricing->exists) {
            $pricing->save();
        }

        // Assign pricing_id to product if changed
        if ($product->pricing_id !== $pricing->id) {
            $product->pricing_id = $pricing->id;

            if ($product->isDirty('pricing_id')) {
                $product->save();
            }
        }

        $row['__product'] = $product;

        return $next($row);
    }
}
