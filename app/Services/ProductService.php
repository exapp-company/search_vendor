<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProductService
{
    public function createSlug(Product $product)
    {
        $slug = Str::slug($product->name . '-shop-' . $product->shop->name . '-' . $product->id, '-');
        $product->slug = $slug;
        Product::withoutSyncingToSearch(fn () => $product->save());
    }
    public function makeSlug(Product $product)
    {
        $date = Carbon::now();
        $slug = Str::slug($product->name . '-' . $product->shop_id . '-' . $date->valueOf() . '-' . rand(100000, 999999));
        return $slug;
    }
}
