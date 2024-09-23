<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\ProductResource;
use App\Traits\Paginatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class SearchCollection extends ResourceCollection
{

    use Paginatable;

    public static $wrap = 'items';

    public function toArray(Request $request): array|Collection|JsonSerializable|Arrayable
    {
        return ProductResource::collection($this->collection);
    }
}
