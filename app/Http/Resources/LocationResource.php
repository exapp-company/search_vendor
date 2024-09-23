<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'city' => new CityResource($this->whenLoaded('city')),
            'parent' => new LocationResource($this->whenLoaded('parent')),
            'children' => new LocationCollection($this->whenLoaded('children'))
        ];
    }
}
