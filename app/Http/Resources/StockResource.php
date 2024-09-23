<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'in_stock' => $this->in_stock,
            'amount' => $this->amount,
            'office' => new SimpleOfficeResource($this->office)
        ];
    }
}
