<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SimpleShopResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'website' => $this->website,
            'email' => $this->email,
            'logo' => $this->logo ? url(Storage::url($this->logo->path)) : null,
            'description' => $this->description,
            'files' => FileResource::collection($this->whenLoaded('files')),
            'recommended' => $this->recommended
        ];
    }
}
