<?php

namespace App\Services\Geo;

use Dadata\DadataClient;
use GuzzleHttp\Exception\GuzzleException;

class DaDataGeoService
{

    private string $token;
    private string $secret;
    private DadataClient $client;

    public function __construct()
    {
        $this->token = config('services.dadata.token');
        $this->secret = config('services.dadata.secret');
        $this->client = new DadataClient($this->token, $this->secret);
    }

    /**
     * @throws GuzzleException
     */
    public function getLocation(string $ip): ?array
    {
        $response = $this->client->iplocate($ip);

        if (!$response) {
            return null;
        }

        return [
            'country' => $response['data']['country'],
            'city' => $response['data']['city'],
        ];
    }


}
