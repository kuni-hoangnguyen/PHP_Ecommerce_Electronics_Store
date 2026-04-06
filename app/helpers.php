<?php

declare(strict_types=1);

if (!function_exists('normalized_asset_url')) {
    function normalized_asset_url(?string $path): string
    {
        $value = trim((string) $path);

        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value) === 1 || str_starts_with($value, '/')) {
            return $value;
        }

        return '/' . ltrim($value, '/');
    }
}

if (!function_exists('image_url')) {
    function image_url(?string $path): string
    {
        $normalized = normalized_asset_url($path);

        return $normalized !== '' ? $normalized : '';
    }
}
