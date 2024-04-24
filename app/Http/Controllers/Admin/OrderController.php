<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    //
    public function index(){
        $orders = Order::with('order_items.product.brand', 'order_items.product.category','user','user_address')->get();

        // return Inertia::render("Admin/Product/OrderList", ['orders' => $orders]);
        return Inertia::render("Admin/Product/OrderListTest", ['orders' => $orders]);

    }
    public function destroy($id){
        $orders = Order::findOrFail($id)->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
