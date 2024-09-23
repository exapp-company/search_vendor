<?php

namespace App\Http\Resources;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Shop */
class ShopResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'website' => $this->website,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo' => $this->logo ? url(Storage::url($this->logo->path)) : null,
            'address' => $this->address,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'updated_at' => $this->updated_at->format('d-m-Y H:i:s'),
            'supplier' => new UserResource($this->whenLoaded('supplier')),
            'offices' => OfficeResource::collection($this->whenLoaded('offices')),
            'status' => $this->status,
            'import_progress' => $this->import_progress,
            'city' => new CityResource($this->city),
            'feed_type' => $this->feed_type,
            'feed_url' => $this->feed_url,
            'feed_mapping' => $this->feed_mapping,
            'feed_item' => $this->feed_item,
            'files' => FileResource::collection($this->whenLoaded('files')),
            'has_subdomain' => $this->has_subdomain,
            'chat_is_active' => $this->whenLoaded('chat', function () {
                return $this->chat->is_active;
            }),        ];
    }
}
