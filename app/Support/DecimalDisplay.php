<?php

namespace App\Support;

class DecimalDisplay
{
    public static function asset(mixed $value, string $asset): string
    {
        if (strtoupper($asset) === 'USD') {
            return self::money($value) ?? '0.00';
        }

        return self::trim($value, 8) ?? '0';
    }

    public static function money(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return number_format((float) $value, 2, '.', '');
    }

    public static function trim(mixed $value, int $maxScale = 8): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = (string) $value;
        if (! str_contains($raw, '.')) {
            return $raw;
        }

        [$whole, $fraction] = explode('.', $raw, 2);
        $fraction = rtrim(substr($fraction, 0, $maxScale), '0');

        return $fraction === '' ? $whole : $whole.'.'.$fraction;
    }
}
