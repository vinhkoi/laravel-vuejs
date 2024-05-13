<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFlashSaleResource extends JsonResource
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
            'discount_rate' => $this->discount_rate,
            'discounted_price' => $this->discounted_price,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,

        ];
    }
}
