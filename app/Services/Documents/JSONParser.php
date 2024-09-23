<?php

namespace App\Services\Documents;

use App\Enums\ShopStatus;
use App\Models\Shop;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class JSONParser extends Parser
{
    public function parse(array $feeds): array
    {
        $products = [];

        foreach ($feeds as $feed) {
            $data = file_get_contents($feed['url']);
            //  Log::info("encoding " . mb_detect_encoding($data));
            $data = str_replace(array("\r", "\n"), "", $data);

            $data = str_replace(",]", "]", $data);
            $data = json_decode($data, true);
            if (is_null($data)) {
                continue;
            }
            $this->feeds[$feed['url']] = $data;
            foreach ($data as $item) {
                if ($item) {
                    $offer = $this->parseOffer($item, $feed['mapping']);
                    if ($this->validateOffer($offer)) {
                        $products[$offer['url']] = $offer;
                    }
                }
            }
        }
        return $products;
    }
}
