<?php

namespace App\Contracts;

interface EnumContract
{
    public static function values(): array;

    public static function readable(string $value): ?string;

    public static function default(): string;

}
