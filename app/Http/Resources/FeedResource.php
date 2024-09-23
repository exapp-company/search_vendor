<?php

namespace App\Http\Resources;

use App\Models\Feed;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Feed */
class FeedResource extends JsonResource
{

    public static $wrap = null;


    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'url' => $this->url,
            'offers_count' => $this->offers_count ?? 0,
            'processed_offers_count' => $this->processed_offers_count ?? 0,
            'updated_at' => $this->updated_at->format('d-m-Y H:i:s'),
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
            'status' => new FeedStatusResource($this->whenLoaded('status')),
            'shop' => new ShopResource($this->whenLoaded('shop')),
        ];
    }
}
