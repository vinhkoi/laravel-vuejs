<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    function welcome(){
        return Inertia::render('User/Components/Welcome');
    }
    function index()
    {
        $user = auth()->user(); // Lấy người dùng đã đăng nhập

        // $orders = Order::with('order_items.product.brand', 'order_items.product.category')->get();
        $orders = Order::with('order_items.product.brand', 'order_items.product.category','user','user_address')
        ->where('user_id', $user->id) // Lọc đơn hàng theo user_id
        ->get();
        return Inertia::render('User/Dashboard', ['orders' => $orders]);
    }
    function address(){
        $user = auth()->user(); // Lấy người dùng đã đăng nhập

        $userAddress = UserAddress::where('user_id', $user->id)->where('isMain', 1)->first();

        return Inertia::render('User/Components/Address',['userAddress' => $userAddress]);
    }
    
    function test(){
        return Inertia::render('Dashboard');
    }
}
