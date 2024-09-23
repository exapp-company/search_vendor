<?php

namespace App\Traits;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

trait Paginatable
{

    public function paginationInformation($request, $paginated, $default): array|JsonSerializable|Arrayable
    {
        return [
            'current_page' => $default['meta']['current_page'],
            'total' => $default['meta']['total'],
            'count' => $this->count(),
            'per_page' => $default['meta']['per_page'],
            'total_pages' => $default['meta']['last_page']
        ];
    }
}
