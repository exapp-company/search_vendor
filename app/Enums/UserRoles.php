<?php

namespace App\Enums;

use App\Contracts\EnumContract;
use App\Traits\Enumerable;

enum UserRoles implements EnumContract
{
    use Enumerable;

    private const admin = 'admin';
    private const user = 'user';
    private const moder = 'moder';
    private const manager = 'manager';
    private const supplier = 'supplier';


    public static function readable(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($value) {
            self::admin => 'Администратор',
            self::user => 'Пользователь',
            self::moder => 'Модератор',
            self::manager => 'Менеджер',
            self::supplier => 'Поставщик',
            default => null,
        };
    }

    public static function default(): string
    {
        return self::user;
    }
}
