<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductListRequest;
use App\Http\Resources\ProductListResource;
use App\Models\ProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductListController extends Controller
{
    public function index(ProductList $productList)
    {
        return new ProductListResource($productList->load(
            [
                'products' => [
                    'productLists',
                    'shop',
                    'stocks' => [
                        'office' => [
                            'city'
                        ]
                    ]
                ]
            ]
        ));
    }

    public function store(ProductListRequest $request)
    {
        $data = $request->validated();

        $list = ProductList::create();

        $title = Arr::get($data, 'title', null) ?? "Cписок №$list->id";
        $list->title = $title;
        $list->save();

        $productData = Arr::get($data, 'ids', []);

        $syncData = [];
        foreach ($productData as $productId => $count) {
            $syncData[$productId] = ['count' => $count];
        }

        $list->products()->sync($syncData);

        return new ProductListResource($list);
    }

}
