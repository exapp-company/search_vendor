<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\CityRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\Collections\CityCollection;
use App\Models\City;

class CityController extends ApiController
{
    public function index()
    {
        $cities = City::orderBy('name', 'asc')->get();
        return new CityCollection($cities->load("country", "shops"));
    }

    public function store(CityRequest $request)
    {
        return new CityResource(City::create($request->validated()));
    }

    public function show(City $city)
    {
        return new CityResource($city->load("country", "shops"));
    }

    public function update(CityRequest $request, City $city)
    {
        $city->update($request->validated());

        return new CityResource($city);
    }

    public function destroy(City $city)
    {
        if ($city->delete()) {
            return $this->success(__('Город успешно удален.'));
        } else {
            return $this->error(__('Произошла ошибка при удалении объекта.'), HttpStatus::internalServerError);
        }
    }
}
