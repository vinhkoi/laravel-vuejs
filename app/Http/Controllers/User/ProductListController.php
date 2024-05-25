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
    private function getFilteredProducts(Request $request)
    {
        $products = Product::with('category', 'brand', 'product_images');

        // Lọc theo từ khóa tìm kiếm (nếu có)
        if ($query = $request->input('query')) {
            $products->where(function ($q) use ($query) {
                $q->where('title', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%");
            });
        }

        // Lọc theo brand (nếu có)
        if ($brands = $request->input('brands')) {
            $products->whereIn('brand_id', $brands);
        }

        // Lọc theo category (nếu có)
        if ($categories = $request->input('categories')) {
            $products->whereIn('category_id', $categories);
        }

        return $products->filtered()->paginate(12)->withQueryString();
    }
    public function index()
    {
        $products = Product::with('category', 'brand', 'product_images');
        $filterProducts = $products->filtered()->paginate(12)->withQueryString();

        $categories = Category::get();
        $brands = Brand::get();

        return Inertia::render(
            'User/ProductList',
            [
                'categories' => $categories,
                'brands' => $brands,
                'products' => ProductResource::collection($filterProducts)
            ]
        );
    }
    // public function search(Request $request)
    // {
    //     $query = $request->input('query'); // Lấy từ khóa từ query parameter

    //     // Tìm kiếm sản phẩm (sử dụng Eloquent hoặc truy vấn SQL)
    //     $products = Product::with('category', 'brand', 'product_images')
    //         ->where('title', 'like', "%$query%") // Tìm theo tên sản phẩm
    //         ->orWhere('description', 'like', "%$query%") // Tìm theo mô tả
    //         ->filtered()
    //         ->paginate(12)
    //         ->withQueryString();

    //     $categories = Category::get();
    //     $brands = Brand::get();
    //     // Trả về kết quả tìm kiếm
    //     return Inertia::render('User/ProductSearch', [
    //         'products' => ProductResource::collection($products),
    //         'query' => $query,
    //         'categories' => $categories,
    //         'brands' => $brands,
    //     ]);
    // }
    // public function search(Request $request)
    // {
    //     $filterProducts = $this->getFilteredProducts($request);
    //     $categories = Category::get();
    //     $brands = Brand::get();

    //     return Inertia::render('User/ProductSearch', [
    //         'products' => ProductResource::collection($filterProducts),
    //         'query' => $request->input('query'), // Thêm query vào kết quả
    //         'categories' => $categories,
    //         'brands' => $brands,
    //     ]);
    // }
    public function search(Request $request)
{
    $query = $request->input('query');
    $brands = $request->input('brands');
    $categories = $request->input('categories');

    $products = Product::with('category', 'brand', 'product_images');

    // Lọc theo từ khóa tìm kiếm (nếu có)
    if ($query) {
        $products->where(function ($q) use ($query) {
            $q->where('title', 'like', "%$query%")
              ->orWhere('description', 'like', "%$query%");
        });
    }

    // Lọc theo brand (nếu có)
    if ($brands) {
        $products->whereIn('brand_id', $brands);
    }

    // Lọc theo category (nếu có)
    if ($categories) {
        $products->whereIn('category_id', $categories);
    }

    $filterProducts = $products->filtered()->paginate(12)->withQueryString();

    return Inertia::render('User/ProductSearch', [
        'products' => ProductResource::collection($filterProducts),
        'query' => $query,
        'categories' => Category::get(),
        'brands' => Brand::get(),
    ]);
}
}
