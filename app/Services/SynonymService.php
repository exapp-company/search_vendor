<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class SynonymService
{
    private $client;
    public function __construct()
    {
        $host = config('explorer.connection.scheme') . '://' . config('explorer.connection.host') . ':' . config('explorer.connection.port');
        $this->client = new Client([
            'base_uri' => $host,
            'auth' => [config('explorer.connection.auth.username'), config('explorer.connection.auth.password')],
            'verify' => false

        ]);
    }

    public function refreshSynonymSet($synonym_name, $synonyms)
    {
        $synonyms = $synonyms->map(function ($item) {
            return [
                'id' => '' . $item->id,
                "synonyms" => join(",", $item->synonyms)
            ];
        })->toArray();
        $response = $this->client->request('PUT', "/_synonyms/$synonym_name", [
            'json' => [
                'synonyms_set' => $synonyms
            ]
        ]);
        return json_decode($response->getBody());
    }
    public function checkSet($synonym_name)
    {
        try {
            $response = $this->client->request('GET', "/_synonyms/$synonym_name", ['http_errors' => false]);
            return $response->getStatusCode() == 200;
        } catch (ClientException $e) {
            return false;
        }
    }
}
