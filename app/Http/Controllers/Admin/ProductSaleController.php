<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductFlashSale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductSaleController extends Controller
{
    //
    public function index()
    {
        $product = Product::get();
        $sale = ProductFlashSale::get();
        return Inertia::render("Admin/Product/ProductSaleList", ['sale' => $sale,'product'=>$product]);
    }
    public function store(Request $request)
    {
        $sale = new ProductFlashSale();
        $sale->discount_rate = $request->discount_rate;
        $sale->discounted_price = $request->discounted_price;
        $sale->start_date = $request->start_date;
        $sale->end_date = $request->end_date;
        $sale->product_id = $request->product_id;
        $sale->save();
        return redirect()->route('admin.sale.index')->with('success', 'Product created successfully.');
    }
    public function update(Request $request,$id)
    {
        $sale = ProductFlashSale::findOrFail($id);
        $sale->discount_rate = $request->discount_rate;
        $sale->discounted_price = $request->discounted_price;
        $sale->start_date = $request->start_date;
        $sale->end_date = $request->end_date;
        $sale->product_id = $request->product_id;
        $sale->update();
        return redirect()->route('admin.sale.index')->with('success', 'Product updated successfully.');

    }
    public function destroy($id)
    {
        $sale = ProductFlashSale::findOrFail($id)->delete();
        return redirect()->route('admin.sale.index')->with('success', 'Product deleted successfully.');

    }
}
