<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    //
    public function index(){
        $brands = Brand::get();
        $categories = Category::get();
        $orderItems = OrderItems::with('product.brand', 'product.category')->get();
        $orders = Order::with('order_items.product.brand', 'order_items.product.category')->get();
        return Inertia::render('User/Dashboard',['orders'=>$orders,'brands' => $brands, 'categories' => $categories, 'orderItems'=>$orderItems]);
    }
}
