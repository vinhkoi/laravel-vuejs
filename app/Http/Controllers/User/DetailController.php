<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Chirp;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DetailController extends Controller
{
    //
    public function view($id){
        $chirps = Chirp::with('user')->latest()->get();
        $products = Product::with('product_images')->get();
        $product = Product::with('product_images', 'flashSale', 'category', 'brand')->findOrFail($id);
        return Inertia::render('User/Detail', [
            'product' => $product,
            'products' => $products,
            'chirps'=>$chirps
        ]);
    }
    public function store(Request $request)
    {
        //
        // $user = $request->user();

        $chirps = new Chirp;
        $chirps->message = $request->message;
        $chirps->user_id = $request->user()->id;
        $chirps->save();
        return redirect()->back()->with('success', 'Chirp created successfully.');


    }
    public function update(Request $request,$id){
        $chirps = Chirp::findOrFail($id);
        $chirps->message = $request->message;
        $chirps->user_id = $request->user()->id;

        $chirps->save(); // Lưu lại thay đổi
        return redirect()->back()->with('success', 'Chirp updated successfully.');


    }
}
