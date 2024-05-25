<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WishlistController extends Controller
{

    public function view(Request $request)
    {
        $wishlist = $request->user()->wishlists()->with('product.product_images', 'product.flashSale')->get(); // Eager load sản phẩm


        return Inertia::render('User/WishList', [
            'wishlist' => WishlistResource::collection($wishlist),
        ]);
    }

    public function store(Request $request,Product $product)
    {
         // 1. Kiểm tra người dùng đã đăng nhập
    if (!$request->user()) {
        return redirect()->route('login');
    }

    // 2. Kiểm tra sản phẩm đã có trong wishlist
    $existingWishlistItem = Wishlist::where('user_id', $request->user()->id)
                                    ->where('product_id', $product->id) // Sử dụng $product->id
                                    ->first();

    if ($existingWishlistItem) {
        return redirect()->back()->with('info', 'Sản phẩm đã có trong wishlist của bạn.');
    } else {
        $request->user()->wishlists()->create([
            'product_id' => $product->id, // Sử dụng $product->id
        ]);

        return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào wishlist.');
    }

    }



    public function delete(Request $request,Product $product)
    {
        if (!$request->user()) {
            return redirect()->route('login'); // Hoặc trả về response lỗi
        }

        // 2. Tìm sản phẩm trong wishlist của người dùng
        $wishlistItem = $request->user()->wishlists()->where('product_id', $product->id)->first();

        // 3. Kiểm tra wishlist item có tồn tại không
        if (!$wishlistItem) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại trong wishlist.');
        }

        // 4. Kiểm tra quyền xóa (chỉ chủ sở hữu wishlist mới được xóa)
        if ($wishlistItem->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền xóa mục này.');
        }

        // 5. Xóa wishlist item
        $wishlistItem->delete();

        // 6. Trả về response thành công
        return redirect()->back()->with('success', 'Sản phẩm đã được xóa khỏi wishlist.');
    }
}
