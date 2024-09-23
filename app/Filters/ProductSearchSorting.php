<?php

namespace App\Filters;

use Illuminate\Support\Facades\Log;

class ProductSearchSorting
{
    public static function apply($query, string $sortBy, string $sortDirection): void
    {
        $query->orderBy($sortBy, $sortDirection);
    }
}
