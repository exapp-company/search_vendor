<?php

namespace App\Filters;

class ProductSearchFilter
{
    public static function apply($query, ?array $priceRange, ?array $cities): void
    {
        if ($priceRange) {
            [$minPrice, $maxPrice] = $priceRange;
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        if (!empty($cities)) {
            $query->whereHas('shop.city', function ($query) use ($cities) {
                $query->whereIn('city_id', $cities);
            });
        }
    }
}
