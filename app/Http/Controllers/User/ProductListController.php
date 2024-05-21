<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductListController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'brand', 'product_images');
        $filterProducts = $products->filtered()->paginate(12)->withQueryString();

        $categories = Category::get();
        $brands = Brand::get();

        return Inertia::render(
            'User/ProductList',
            [
                'categories'=>$categories,
                'brands'=>$brands,
                'products' => ProductResource::collection($filterProducts)
            ]
        );
    }
    public function search(Request $request)
{
    $query = $request->input('query'); // Lấy từ khóa từ query parameter

    // Tìm kiếm sản phẩm (sử dụng Eloquent hoặc truy vấn SQL)
    $products = Product::with('category', 'brand', 'product_images')
        ->where('title', 'like', "%$query%") // Tìm theo tên sản phẩm
        ->orWhere('description', 'like', "%$query%") // Tìm theo mô tả
        ->filtered()
        ->paginate(12)
        ->withQueryString();

    // Trả về kết quả tìm kiếm
    return Inertia::render('User/ProductSearch', [
        'products' => ProductResource::collection($products),
        'query' => $query,
    ]);
}

}
