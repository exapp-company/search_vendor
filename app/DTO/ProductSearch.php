<?php

namespace App\DTO;

use Illuminate\Support\Arr;

class ProductSearch
{

    public $query, $sortBy, $sortDirection, $priceMin, $priceMax, $suppliers, $city_ids, $city_id, $locations;
    public function __construct(
        array $data
    ) {
        $this->query = Arr::get($data, 'query', '');
        $this->sortBy = Arr::get($data, 'sortBy', null);
        $this->sortDirection = Arr::get($data, 'sortDirection', 'ASC');
        $this->priceMin = Arr::get($data, 'priceMin');
        $this->priceMax = Arr::get($data, 'priceMax');
        $this->suppliers = Arr::get($data, 'supplier', []);
        if (!is_array($this->suppliers)) {
            $this->suppliers = [$this->suppliers];
        }
        $this->city_id = (int) Arr::get($data, 'city_id');
        if (!is_null($this->city_id) && $this->city_id < 0) {
            $this->city_id = null;
        }
        $this->locations = Arr::get($data, 'locations', []);
        $this->locations = Arr::only($this->locations, ['district', 'mart', 'transport_stop', 'metro']);
    }
}
