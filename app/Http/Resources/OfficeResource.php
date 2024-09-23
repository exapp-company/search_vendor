<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resp =   parent::toArray($request);
        $resp['files'] = FileResource::collection($this->whenLoaded('files'));
        return parent::toArray($request);
    }
}
