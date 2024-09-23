<?php

namespace App\Http\Controllers\API\V1;


use App\Models\City;
use App\Models\Shop;
use App\Models\Product;
use App\Models\FeedStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Resources\CityResource;
use Illuminate\Support\Facades\Auth;
use App\Services\Geo\YandexGeoService;
use App\Http\Controllers\ApiController;
use App\Http\Resources\FeedStatusResource;
use App\Http\Resources\Collections\CityCollection;
use App\Services\GeoService;

class MainController extends ApiController
{
    public function __construct(public GeoService $geoService) {}
    public function index(Request $request)
    {
        //
    }

    public function init()
    {


        $suppliers = Shop::select('id', 'name')->where('status', 'active')->get();

        //$suppliersCollection = new UserCollection($suppliers);


        return [
            'max_price' => (int) Product::max('price'),
            'user' => Auth::user() ?? null,
            'cities' => new CityCollection(City::orderBy('name', 'asc')->with("shops")->get()),
            'feed_statuses' => FeedStatusResource::collection(FeedStatus::all()),
            'app_locale' => App::getLocale(),
            'suppliers' => $suppliers,
        ];
    }



    public function geolocation(Request $request)
    {
        $city = null;
        if ($request->has('latitude') && $request->has('longitude')) {
            $city = $this->geoService->getCityByCoors(floatval($request->longitude), floatval($request->latitude));
        }
        // $latitude = $request->input('latitude');
        // $longitude = $request->input('longitude');

        // $yandexGeoService = new YandexGeoService();
        // $locationName = $yandexGeoService->getLocation((float) $latitude, (float) $longitude);
        // dd($locationName);
        // if (is_null($locationName)) {
        //     return response()->json([]);
        // }

        // $city = City::where('name', $locationName)->first();
        if (is_null($city)) {
            return response()->json([]);
        }
        return new CityResource($city);
    }
}
