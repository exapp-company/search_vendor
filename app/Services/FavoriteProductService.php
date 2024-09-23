<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Repositories\FavoriteProductRepository;

class FavoriteProductService
{



    public function __construct(
        protected FavoriteProductRepository $favoriteProductRepository,
    ) {
    }


    public function addToFavorites(User $user, Product $product)
    {
        return $this->favoriteProductRepository->addToFavorites($user, $product);
    }


    public function removeFromFavorites(User $user, Product $product)
    {
        return $this->favoriteProductRepository->removeFromFavorites($user, $product);
    }


    public function isProductInFavorites(User $user, Product $product)
    {
        return $this->favoriteProductRepository->isProductInFavorites($user, $product);
    }


}
