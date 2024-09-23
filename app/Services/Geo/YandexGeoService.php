<?php

namespace App\Services\Geo;

use App\DTO\GeoCoors;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

class YandexGeoService
{
    private string $apiKey;
    private string $url;
    private const TIMEOUT = 5;
    private const RETRY_TIMES = 3;
    private const RETRY_SLEEP = 100;

    public function __construct()
    {
        $this->apiKey = config('services.yandex.key');
        $this->url = config('services.yandex.url');
    }

    public function get($needle)
    {
        $response = Http::timeout(self::TIMEOUT)
            ->retry(self::RETRY_TIMES, self::RETRY_SLEEP)
            ->get($this->url, [
                'apikey' => $this->apiKey,
                'geocode' => $needle,
                'format' => 'json',
                //'kind' => 'locality'
            ]);

        $response->throw();

        $data = $response->json();
        return $data;
    }

    public function getLocation(float $latitude, float $longitude)
    {
        try {
            $data = $this->get("{$longitude},{$latitude}");
            //dd($data["response"]["GeoObjectCollection"]["featureMember"]);
            Log::debug('Yandex Geocoder raw response', ['response' => $data]);

            return $this->parseResponse($data);
        } catch (RequestException $e) {
            Log::error('Yandex Geocoder request failed', [
                'error' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return null;
        }
    }
    private function getGeoObjects(array $data): array
    {
        $data = $data['response']['GeoObjectCollection']['featureMember'];
        $result = [];
        foreach ($data as $item) {
            $result[] = Arr::get($item, 'GeoObject');
        }
        return $result;
    }
    private function parseResponse(array $data)
    {
        $objects = $data['response']['GeoObjectCollection']['featureMember'];
        foreach ($objects as $object) {
            $geo = $object['GeoObject'];
            $kind = Arr::get($geo, "metaDataProperty.GeocoderMetaData.kind");
            if ($kind == "locality") {
                return Arr::get($geo, "name");
            }
        }
        return null;
        $geoObject = [0]['GeoObject'] ?? null;

        if (!$geoObject) {
            Log::warning('No GeoObject found in Yandex Geocoder response');
            return null;
        }

        $metaData = $geoObject['metaDataProperty']['GeocoderMetaData'] ?? [];
        $addressComponents = $metaData['Address']['Components'] ?? [];
        $addressDetails = $metaData['AddressDetails']['Country'] ?? [];

        $result = [
            'address' => $metaData['text'] ?? null,
            'country' => $this->findComponentByKind($addressComponents, 'country'),
            'city' => $this->findCity($addressComponents, $addressDetails),
            'street' => $this->findComponentByKind($addressComponents, 'street'),
            'house' => $this->findComponentByKind($addressComponents, 'house'),
            'postal_code' => $metaData['Address']['postal_code'] ?? null,
            'kind' => $geoObject['metaDataProperty']['GeocoderMetaData']['kind'] ?? null,
            'precision' => $geoObject['metaDataProperty']['GeocoderMetaData']['precision'] ?? null,
        ];

        Log::info('Yandex Geocoder parsed response', ['parsed' => $result]);

        return $result;
    }

    private function findComponentByKind(array $components, string $kind): ?string
    {
        foreach ($components as $component) {
            if ($component['kind'] == $kind) {
                return $component['name'];
            }
        }
        return null;
    }

    private function findCity(array $components, array $addressDetails): ?string
    {
        $city = $this->findComponentByKind($components, 'locality');

        if ($city === null) {
            $city = $this->findInAddressDetails($addressDetails, [
                ['AdministrativeArea', 'City', 'LocalityName'],
                ['AdministrativeArea', 'SubAdministrativeArea', 'Locality', 'LocalityName'],
                ['Locality', 'LocalityName']
            ]);
        }

        if ($city !== null) {
            return $city;
        }

        $administrativeAreaType = $this->findInAddressDetails($addressDetails, ['AdministrativeArea', 'AdministrativeAreaType']);
        if ($administrativeAreaType === 'city' || $administrativeAreaType === 'metropolis') {
            return $this->findInAddressDetails($addressDetails, ['AdministrativeArea', 'AdministrativeAreaName']);
        }

        return $this->findInAddressDetails($addressDetails, ['AdministrativeArea', 'AdministrativeAreaName']);
    }

    private function findInAddressDetails(array $data, array $path): ?string
    {
        foreach ($path as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return null;
            }
        }
        return is_string($data) ? $data : null;
    }

    public function getCoors($name): ?GeoCoors
    {
        $data = $this->get($name);
        $data = $this->getGeoObjects($data);
        if (count($data) == 0) {
            return null;
        }
        $data = $data[0];
        $point = Arr::get($data, 'Point.pos');
        $point = explode(" ", $point);
        $geoCoors = new GeoCoors();
        $geoCoors->latitude = $point[1];
        $geoCoors->longitude = $point[0];
        return $geoCoors;
    }
}
