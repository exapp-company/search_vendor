<?php

namespace App\Services\Documents;

use App\Enums\ShopStatus;
use App\Models\Shop;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

abstract class Parser
{
    public $feeds = [];

    public function __construct(public Shop $shop) {}

    abstract public function parse(array $feeds): array;

    protected function parseOffer(array $node, array $mapping): array
    {
        $offerData = [];
        foreach ($mapping as $key => $value) {
            $data = $this->handleMappingValue($key, $value, $node);
            $offerValue = $data['value'];
            if ($key == 'price') {
                if (!$offerValue) {
                    $offerValue = 0;
                }
                $offerValue = number_format((float)$offerValue, 2, '.', '');
            }
            if ($key == 'in_stock') {
                $offerValue = boolval($offerValue);
            }
            $offerData[$key] = $offerValue;
        }
        $offerData["url"] = $this->getKey($offerData);
        return $offerData;
    }
    protected function handleMappingValue(string $key, array|null $mapping, array $node): array
    {
        $result = [
            'key' => $key,
            'value' => null
        ];
        if (is_null($mapping)) {
            return $result;
        }
        $name = $mapping["name"];
        if ($mapping["type"] == "attribute") {
            $name = "attr_" . $name;
        }
        $result['value'] = Arr::get($node, $name);
        return $result;
    }
    public function setStocks($products, Shop $shop)
    {
        $offices = $shop->offices->where('status', ShopStatus::active->value);
        foreach ($offices as $office) {
            $feed = $office->use_parent_feed ? $shop->feed_url : $office->feed_url;
            $nodes = $this->feeds[$feed];
            $mapping = $office->use_parent_mapping ? $shop->feed_mapping : $office->feed_mapping;
            if (!$nodes || !$mapping) {
                continue;
            }
            $mapping = Arr::only($mapping, ['in_stock', 'amount', 'url']);
            if (!Arr::has($mapping, 'url')) {
                $mapping['url'] = $shop->feed_mapping['url'];
            }

            foreach ($nodes as $in => $node) {
                $stock = [];
                $url = '';
                if (is_null($node)) {
                    Log::info("Null Node" . $in);
                    continue;
                }
                foreach ($mapping as $key => $value) {
                    $data = $this->handleMappingValue($key, $value, $node);
                    $stockValue = $data["value"];
                    if ($key == 'in_stock') {
                        $stockedValue = filter_var($stockValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                        if (is_null($stockedValue)) {
                            $stockedValue = intval($stockValue);
                        }
                        $stockValue = !!$stockedValue;
                    }
                    if ($key == 'amount') {
                        $stockValue = intval($stockValue);
                    }
                    if ($key == 'url') {
                        $url = $this->getKey(['url' => $stockValue]);
                    } else {
                        $stock[$key] = $stockValue;
                    }
                }
                $stock['office_id'] = $office->id;
                if (!Arr::has($products, $url)) {
                    continue;
                }
                if (!Arr::has($products[$url], 'stocks')) {
                    $products[$url]['stocks'] = [];
                }
                $products[$url]['stocks'][] = $stock;
                if (!$products[$url]['in_stock'] && $stock['in_stock']) {
                    $products[$url]['in_stock'] = true;
                }
            }
        }
        return $products;
    }
    public function getKey(array $product)
    {
        if (!$this->shop->has_subdomain) {
            return $product["url"];
        }
        $url = parse_url($product["url"]);
        $host = $url["host"];
        $host = explode(".", $host);
        $url["host"] = $host[count($host) - 2] . "." . $host[count($host) - 1];
        $result = "https://" . $url["host"] . $url["path"];
        if (Arr::has($url, "query")) {
            $result .= "?" . $url["query"];
        }
        return $result;
    }
    public function validateOffer($item): bool
    {
        if (is_null($item)) {
            return false;
        }
        $validator = Validator::make(
            $item,
            [
                "url" => "required|url",
                "name" => "required|string",
                "picture" => "nullable|url",
                "in_stock" => "required|boolean",
                "price" => "required|numeric|min:1|max:99999999",
                "sku" => "sometimes|nullable",
                "amount" => "sometimes|nullable|numeric|min:0|max:2147483647",
                "wholesale_price" => "sometimes|nullable|numeric|min:0|max:99999999"
            ]
        );
        return !$validator->fails();
    }
}
