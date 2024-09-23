<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleOfficeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'name' => $this->name,
            'phone' => $this->phone,
            'city' => $this->whenLoaded('city'),
            'lon' => $this->lon,
            'lat' => $this->lat,
            'files' => FileResource::collection($this->whenLoaded('files'))
        ];
    }
}
