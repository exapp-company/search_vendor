<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'pagetitle' => $this->pagetitle,
            'decription' => $this->description,
            'introtext' => $this->introtext,
            'slug' => $this->slug,
            'is_published' => $this->is_published,
            'query' => $this->query,
            'city' => new CityResource($this->whenLoaded('city'))
        ];
    }
}
