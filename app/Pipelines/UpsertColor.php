<?php

namespace App\Pipelines;

use App\Models\Color;
use Closure;

class UpsertColor
{
    public function handle(array $row, Closure $next): array
    {
        $product = $row['__product'];

        if (!$product) {
            throw new \Exception('Product instance not found in row');
        }

        // Try to find color by name + pms_color
        $color = Color::firstWhere([
            'name' => $row['color_name'],
            'pms_color' => $row['pms_color'],
        ]);

        if (!$color) {
            $color = new Color([
                'name' => $row['color_name'],
                'pms_color' => $row['pms_color'],
            ]);
            $color->save();
        } else {
            $color->fill([
                'name' => $row['color_name'],
                'pms_color' => $row['pms_color'],
            ]);

            if ($color->isDirty()) {
                $color->save();
            }
        }

        // Assign color to product if changed
        if ($product->color_id !== $color->id) {
            $product->color_id = $color->id;

            if ($product->isDirty('color_id')) {
                $product->save();
            }
        }

        // Attach color_id to row for next pipeline if needed
        $row['color_id'] = $color->id;
        $row['__product'] = $product;

        return $next($row);
    }
}
