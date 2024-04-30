<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        $totalRevenue = $orders->sum('total_price');
        $user = User::get();
        $totalUser = $user->count();
        return Inertia::render('Admin/Dashboard', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalUser' => $totalUser,
        ]);}
}
