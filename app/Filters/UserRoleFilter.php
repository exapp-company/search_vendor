<?php


namespace App\Filters;

use App\Enums\UserRoles;

class UserRoleFilter
{
    public static function apply($query, $role): void
    {
        if (self::isValidRole($role)) {
            $query->where('role', UserRoles::get($role));
        }
    }

    protected static function isValidRole($role): bool
    {
        return $role && UserRoles::hasValue($role);
    }
}
