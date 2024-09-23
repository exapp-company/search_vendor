<?php

namespace App\Enums;

use App\Contracts\EnumContract;
use App\Traits\Enumerable;

enum ShopStatus: string implements EnumContract
{
    use Enumerable;

    case pending = 'pending';
    case active = 'active';
    case inactive = 'inactive';
    case rejected = 'rejected';
    // private const active = 'active';
    // private const inactive = 'inactive';
    // private const pending = 'pending';
    // private const rejected = 'rejected';



    public static function readable(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($value) {
            self::active => 'Активный',
            self::inactive => 'Неактивный',
            self::pending => 'На модерации',
            self::rejected => 'Отклонен',
            default => null,
        };
    }

    public static function default(): string
    {
        return self::active;
    }
    public function toString(): ?string
    {
        return match ($this) {
            self::active => 'Активный',
            self::inactive => 'Неактивный',
            self::pending  => 'На модерации',
            self::rejected => 'Отклонен',
            default => null,
        };
    }
}
