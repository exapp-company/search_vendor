<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\DTO\ProductSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\FavoriteProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $productSearch = new ProductSearch($request->all());
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'price' => $this->price,
            'description' => $this->description,
            'url' => $this->url,
            'picture' => $this->picture,
            'in_stock' => $this->in_stock,
            'amount' => $this->amount,
            'sku' => $this->sku,
            'wholesale_price' => $this->wholesale_price,
            'slug' => $this->slug,
            'shop' => new SimpleShopResource($this->shop),
            'stocks' => $this->when(
                $this->relationLoaded('stocks'),
                new StockCollection(
                    $this->stocks->filter(function ($item) use ($productSearch) {
                        $in_stock = $item->in_stock;
                        if ($productSearch->city_id) {
                            $in_stock = $in_stock && $item->office->city_id == $productSearch->city_id;
                        }
                        return $in_stock;
                    })
                )
            ),
            'favorite_count' => $this->when(
                $this->relationLoaded('favoriteByUser') || $this->relationLoaded('productLists'),
                function () {
                    if ($this->relationLoaded('favoriteByUser')) {
                        return $this->favoriteByUser->pluck('count')->first();
                    }

                    if ($this->relationLoaded('productLists')) {
                        return $this->productLists->pluck('pivot.count')->first();
                    }
                }
            ),

        ];
    }
}
