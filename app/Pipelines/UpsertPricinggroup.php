<?php

namespace App\Pipelines;

use App\Models\Pricinggroup;
use Closure;

class UpsertPricinggroup
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        // Find or create pricing group by name (from CSV column "price_group")
        $pricinggroup = Pricinggroup::firstOrCreate(
            ['name' => $row['price_group']],
            ['name' => $row['price_group']]
        );

        // ✅ Remove this (pricinggroup is not part of product)
        // if ($product->pricinggroup_id !== $pricinggroup->id) { ... }

        // Expose for UpsertPricing
        $row['pricinggroup_id'] = $pricinggroup->id;
        $row['__product'] = $product;

        return $next($row);
    }
}
