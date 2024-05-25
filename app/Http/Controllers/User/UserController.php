<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Models\ProductFlashSale;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {

        // Lấy ngày trước 1 tháng từ hiện tại
        $oneMonthAgo = Carbon::now()->subMonth();

        // Lấy danh sách sản phẩm mới trong 1 tháng dựa trên thời gian tạo
        $newProducts = Product::with('brand', 'category', 'product_images')
            ->where('created_at', '>=', $oneMonthAgo)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();
        // $sale = ProductFlashSale::with('product',)->get();
        $sale = Product::with('brand', 'category', 'product_images','flashSale')
        ->whereHas('flashSale') // Kiểm tra mối quan hệ với bảng flashSale
        ->get();
        $products = Product::with('brand', 'category', 'product_images')->orderBy('id','desc')->limit(12)->get();
        $banner = Banner::get();
        return Inertia::render('User/Index', [
            'sales'=>$sale,
            'products'=>$products,
            'banner'=>$banner,
            'newProducts'=>$newProducts,
            'canLogin' => app('router')->has('login'),
            'canRegister' => app('router')->has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
    public function about(){
        return Inertia::render('User/About');
    }
    public function contact(){
        return Inertia::render('User/Contact');
    }
}
