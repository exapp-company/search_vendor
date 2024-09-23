<?php

namespace Database\Seeders;

use App\Models\City;
use App\Services\GeoService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeoSitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all();
        $geoService = new GeoService();
        foreach ($cities as $city) {
            $geoService->setGeoToModel($city);
        }
    }
}
