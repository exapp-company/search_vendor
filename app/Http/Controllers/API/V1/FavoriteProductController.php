<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Collections\ProductCollection;
use App\Models\Product;
use App\Models\User;
use App\Services\FavoriteProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class FavoriteProductController extends ApiController
{

    public function __construct(
        protected FavoriteProductService $favoriteProductService
    ) {
    }

    public function index(Request $request)
    {
        //$favoriteProducts = Auth::user()->favoriteProducts;
        //$products = $favoriteProducts->pluck('product');
        $products = $request->user()->favoriteProducts()->paginate($request->input('count', 30));
        return new ProductCollection($products);
    }


    public function add(Request $request, Product $product)
    {
        $user = $request->user();
        $favorite_product = $user->favoriteProducts()->where('product_id', $product->id)->first();

        if ($favorite_product) {
            $newCount = $favorite_product->pivot->count + 1;
            $user->favoriteProducts()->updateExistingPivot($product->id, ['count' => $newCount]);

            return response()->json([$product->id => $newCount]);
        }

        $user->favoriteProducts()->attach($product->id, ['count' => 1]);

        return response()->json([$product->id => 1]);
    }

    public function addSome(Request $request)
    {
        $user = $request->user();
        $products = $request->input();

        $product_ids = array_keys($products);

        $in_favorite = $user->favoriteProducts()->whereIn('product_id', $product_ids)->get();

        $in_favorite_ids = $in_favorite->keyBy('pivot.product_id');

        $new_ids = [];
        $pivotData = [];

        foreach ($products as $id => $count) {
            if (isset($in_favorite_ids[$id])) {
                $user->favoriteProducts()->updateExistingPivot($id, ['count' => $count]);
            } else {
                $new_ids[] = $id;
                $pivotData[$id] = ['count' => $count];
            }
        }

        if (count($new_ids)) {
            $user->favoriteProducts()->attach($pivotData);
        }

        return response()->json($products);
    }


    public function removeSome(Request $request)
    {
        $user = $request->user();
        $ids = $request->input('ids', []);

        $in_favorite = $user->favoriteProducts()->whereIn('product_id', $ids)->get();

        $remove_ids = [];

        foreach ($in_favorite as $product) {
            $currentCount = $product->pivot->count - 1;

            if ($currentCount > 0) {
                $user->favoriteProducts()->updateExistingPivot($product->id, ['count' => $currentCount]);
            } else {
                $remove_ids[] = $product->id;
            }
        }

        if (count($remove_ids)) {
            $user->favoriteProducts()->detach($remove_ids);
        }

        return response()->json(array_values($remove_ids));
    }

    public function remove(Request $request, Product $product)
    {
        $user = $request->user();
        $favorite_product = $user->favoriteProducts()->where('product_id', $product->id)->first();

        if (!$favorite_product) {
            return response()->json(false);
        }

        $currentCount = $favorite_product->pivot->count - 1;

        if ($currentCount > 0) {
            $user->favoriteProducts()->updateExistingPivot($product->id, ['count' => $currentCount]);

            return response()->json([$product->id => $currentCount]);
        } else {
            $user->favoriteProducts()->detach($product->id);

            return response()->json([$product->id => 0]);
        }
    }




    public function clear(Request $request)
    {
        $user = $request->user();
        $count = $user->favoriteProducts()->detach();
        return $count;
    }


    public function share()
    {
    }
}
