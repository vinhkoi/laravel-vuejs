<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderSummaryController extends Controller
{
    //
    public function show($orderId)
    {
        $order = Order::with([
            'order_items.product' => function ($query) {
                $query->with('product_images');
            },
            'order_items.product.brand',
            'order_items.product.category',
            'user',
            'user_address'
        ])->findOrFail($orderId);
        return Inertia::render("User/OrderSummary", ['order' =>  $order]);
    }
}
