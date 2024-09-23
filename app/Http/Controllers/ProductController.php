<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Enums\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ApiController;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\Collections\SearchCollection;
use App\Http\Resources\Collections\ProductCollection;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ids = $request->input('ids', []);
        $products = Product::whereIn('id', $ids)->with('favoriteByUser')->get();
        return new ProductCollection($products->load('shop', 'stocks.office.city'));
    }


    public function productsForShop($id)
    {
        $products = Product::where('shop_id', $id)->paginate(30);
        // $products->load('shop');
        // $products->load('stocks.office.city');

        return new ProductCollection($products);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $product = Product::create($data);


        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product->load(['shop.files', 'stocks' => ['office' => ['city', 'files']]]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->delete()) {
            return $this->success(__('Товар успешно удален.'));
        } else {
            return $this->error(__('Произошла ошибка при удалении Товара.'), HttpStatus::internalServerError);
        }
    }
}
