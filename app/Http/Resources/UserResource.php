<?php

namespace App\Http\Resources;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
            'role_readable' => UserRoles::readable($this->role),
            'phone' => $this->phone,
            'birthdate' => $this->birthdate,
            'updated_at' => $this->updated_at->format('d.m.Y H:i:s'),
            'created_at' => $this->created_at->format('d.m.Y H:i:s'),
            'shops' => ShopResource::collection($this->whenLoaded('shops')),
            'favorite_products' => $this->whenLoaded('favoriteProducts', function () {
                return $this->favoriteProducts->mapWithKeys(function ($favoriteProduct) {
                    return [$favoriteProduct->pivot->product_id => $favoriteProduct->pivot->count];
                });
            }, []),
        ];
    }
}
