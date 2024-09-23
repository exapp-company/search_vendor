<?php

namespace App\Http\Controllers;

use App\Enums\ShopStatus;
use App\Filters\OfficeFilter;
use App\Http\Requests\OfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Http\Resources\OfficeResource;
use App\Models\Office;
use App\Models\Product;
use App\Models\Shop;
use App\Services\StatusService;
use Elasticsearch\Endpoints\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OfficeController extends Controller
{
    public function __construct(public StatusService $statusService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, OfficeFilter $filter)
    {
        $user = $request->user();
        $offices = Office::filter($filter);
        if (!$user->isAdmin()) {
            $offices = $offices->whereIn('shop_id', $user->shops()->select('id'));
        }
        $offices = $offices->get();
        return OfficeResource::collection($offices->load('shop', 'city', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OfficeRequest $request)
    {
        $office = new Office($request->all());
        $office->shop_id = $request->input('shop_id');
        $office->save();
        if ($request->filled('locations')) {
            $office->locations()->sync($request->locations());
        }
        $this->statusService->changeStatus($office, ShopStatus::pending);
        return new OfficeResource($office->load('locations', 'shop', 'city', 'files'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office)
    {
        return new OfficeResource($office->load('locations', 'shop', 'city', 'files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfficeRequest $request, Office $office)
    {
        $old_mapping = $office->feed_mapping;
        $office->fill($request->validated());
        $office->save();
        $new_mapping = $office->feed_mapping;
        $is_location_changed = false;

        if ($request->has('locations')) {
            $locations = $office->locations->pluck('id')->sort()->toArray();
            $newLocations = array_values(collect($request->input('locations'))->sort()->toArray());
            if (json_encode($locations) != json_encode($newLocations)) {
                $is_location_changed = true;
                $office->locations()->sync($newLocations);
            }
        }


        if (
            count(Arr::except($office->getChanges(), 'feed_mapping'))
            || $old_mapping != $new_mapping
            || $is_location_changed
        ) {
            $this->statusService->changeStatus($office, ShopStatus::pending);
        }

        return new OfficeResource($office->load('locations', 'shop', 'city', 'files'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Office $office)
    {
        $shop = $office->shop;
        if ($shop->offices->count() == 1) {
            abort(422, "Должен существовать хотя бы один офис");
        }
        $stocks = $office->stocks;
        $stocks->load('product');

        $products = $stocks->pluck('product');
        $products_ids = $products->pluck('id');
        $office->stocks()->delete();
        Product::whereIn('id', $products_ids)->searchable();
        $office->delete();
        return response()->noContent();
    }
}
