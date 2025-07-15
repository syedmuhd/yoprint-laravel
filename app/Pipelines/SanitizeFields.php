<?php

declare(strict_types=1);

namespace App\Pipelines;
use Log;

class SanitizeFields
{
    public function handle(array $row, \Closure $next): array
    {
        // Copy original row as base
        $sanitized = $row;

        foreach (['product_title', 'product_description'] as $key) {
            if (isset($row[$key])) {
                $value = is_string($row[$key]) ? $row[$key] : '';

                $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $sanitized[$key] = trim($value);
            }
        }

        Log::info("✅ Row sanitized", ['row' => $sanitized]);

        return $next($sanitized);
    }
}
