<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->collection) {
            // Tính tổng giá trị của tất cả các đơn hàng
            $total = $this->collection->sum('total_price');

        } else {
            $total = 0; // hoặc bất kỳ giá trị mặc định nào bạn muốn
        }

        return [
            // Các trường thông tin khác của đơn hàng (nếu có)
            // ...

            // Trường tổng giá trị của tất cả các đơn hàng
            'total' => $total,
        ];
    }
}
