<?php

namespace App\Http\Resources;

use App\Helper\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        [$products, $cartItems] = $this->resource;
        return [
            'count' => Cart::getCount(),
            // 'total' => $products->reduce(fn (?float $carry, Product $product) => $carry + $product->price * $cartItems[$product->id]['quantity']),
            'total' => $products->reduce(function (?float $carry, Product $product) use ($cartItems) {
                $price = $product->flashSale->first() && $product->flashSale->first()->discounted_price
                    ? $product->flashSale->first()->discounted_price
                    : $product->price;
                return $carry + $price * $cartItems[$product->id]['quantity'];
            }),
            'items' => $cartItems,
            'products' => ProductResource::collection($products),
        ];
    }
}
