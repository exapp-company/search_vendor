<?php

namespace App\Services;

use App\Models\City;
use App\Services\Geo\YandexGeoService;
use Illuminate\Support\Facades\DB;

class GeoService
{
    public function setGeoToModel($model)
    {
        if ($model->lat && $model->lon) {
            return;
        }
        $geoName = $model->getGeoName();
        if (!$geoName) {
            return;
        }
        $geoService = new YandexGeoService();
        $coors = $geoService->getCoors($geoName);
        if (is_null($coors)) {
            return;
        }
        $model->lat = $coors->latitude;
        $model->lon = $coors->longitude;
        $model->save();
    }
    public function getCityByCoors(float $lon, float $lat): ?City
    {
        $sity = City::select("*", DB::raw("(6371 * ACOS(COS(RADIANS($lat)) * COS(RADIANS(lat)) * COS(RADIANS(lon) - RADIANS($lon)) +  SIN(RADIANS($lat)) * SIN(RADIANS(lat)))) AS distance"))
            ->orderBy("distance", "ASC")
            ->first();
        return $sity;
    }
}
