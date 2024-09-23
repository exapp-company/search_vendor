<?php

namespace App\Traits;

use App\Services\GeoService;
use Illuminate\Database\Eloquent\Model;

trait HasGeo
{
    public static function bootHasGeo()
    {
        static::created(function (Model $model) {
            $geoService = new GeoService();
            $geoService->setGeoToModel($model);
        });
    }
}
