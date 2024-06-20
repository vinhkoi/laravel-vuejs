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

    public function index(Request $request)
    {
        $products = Product::with('category', 'brand', 'product_images');
        if ($categoryId = $request->query('category')) {
            $products->where('category_id', $categoryId);
        }
        if ($sort = $request->query('sort')) {
            if ($sort === 'Newest') {
                $products->latest();
            } elseif ($sort === 'Price: Low to High') {
                $products->orderBy('price', 'asc');
            } elseif ($sort === 'Price: High to Low') {
                $products->orderBy('price', 'desc');
            }
        }
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
public function category(Request $request)
{
    $products = Product::with('category', 'brand', 'product_images');

    // Lọc theo category (nếu có)
    if ($categoryId = $request->query('category')) {
        $products->where('category_id', $categoryId);
    }

    $filterProducts = $products->filtered()->paginate(12)->withQueryString();

    // ... (phần còn lại của phương thức index)
}
}
