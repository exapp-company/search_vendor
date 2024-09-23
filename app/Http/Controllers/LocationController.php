<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationCollection;
use App\Http\Resources\LocationResource;
use App\Models\City;
use App\Models\Location;
use App\Models\Shop;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::all();
        return new LocationCollection($locations->load('city', 'parent', 'children'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request)
    {
        $location = Location::create($request->validated());
        return new LocationResource($location->load('city', 'parent', 'children'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        return new LocationResource($location->load('city', 'parent', 'children'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, Location $location)
    {
        $location->fill($request->validated());
        $location->save();
        return new LocationResource($location->load('city', 'parent', 'children'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $location->children()->update(['parent_id' => null]);
        // $offices = $location->offices;
        $location->offices()->detach();
        $location->delete();
        return response()->noContent();
    }

    public function getLocationByCity(City $city)
    {
        $locations = $city->locations;
        return new LocationCollection($locations->load('parent', 'children'));
    }
    public function getChildren(Location $location)
    {
        $locations = $location->children;
        return new LocationCollection($locations->load('city', 'children'));
    }
}
