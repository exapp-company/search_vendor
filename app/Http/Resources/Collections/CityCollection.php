<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\CityResource;
use App\Traits\Paginatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class CityCollection extends ResourceCollection
{

    use Paginatable;

    public static $wrap = 'items';

    public function toArray(Request $request): AnonymousResourceCollection|array|JsonSerializable|Arrayable
    {
        return CityResource::collection($this->collection);
    }
}
