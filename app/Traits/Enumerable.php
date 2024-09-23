<?php

namespace App\Traits;

use ReflectionClass;

trait   Enumerable
{
    public static function values(): array
    {
        $reflection = new ReflectionClass(static::class);
        return array_values($reflection->getConstants());
    }

    public static function hasValue($value): bool
    {
        return in_array($value, static::values(), true);
    }

    public static function options(): array
    {
        $reflection = new ReflectionClass(static::class);
        return $reflection->getConstants();
    }

    public static function label($value)
    {
        $options = static::options();
        return $options[$value] ?? null;
    }

    public static function except(array|string $except): array
    {
        $exceptValues = is_array($except) ? $except : [$except];
        return array_diff(static::values(), $exceptValues);
    }

    public static function get(string $key): ?string
    {
        return self::label($key);
    }

}
