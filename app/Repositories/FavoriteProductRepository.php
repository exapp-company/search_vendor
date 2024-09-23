<?php

namespace App\Repositories;

use App\Models\FavoriteProduct;
use App\Models\Product;
use App\Models\User;


class FavoriteProductRepository
{


    public function addToFavorites(User $user, Product $product)
    {
        return FavoriteProduct::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }


    public function removeFromFavorites(User $user, Product $product): ?bool
    {
        return FavoriteProduct::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();
    }


    public function isProductInFavorites(User $user, Product $product): bool
    {
        return FavoriteProduct::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
    }


    public static function isFavorite(User $user, Product $product): bool
    {
        return (new self())->isProductInFavorites($user, $product);
    }

}
