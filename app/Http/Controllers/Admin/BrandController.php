<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    //
    public function index(){
        $brands = Brand::get();
        return Inertia::render("Admin/Product/BrandList", ['brands' => $brands]);
    }
    public function store(Request $request){
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->save();
        if ($request->hasFile('brand_image')) {
            $brandImage= $request->file('brand_image');
            // Loop through each uploaded image
            foreach ($brandImage as $brand_image) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $brand_image->getClientOriginalExtension();

                // Store the image in the public folder with the unique name
                $brand_image->move('brand_image', $uniqueName);

                // Create a new product image record with the product_id and unique name
                $brand->brand_image = 'brand_image/'.$uniqueName;
                $brand->save();
            }
        }
        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }
    public function update(Request $request,$id){
        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->update();
        if ($request->hasFile('brand_image')) {
            $brandImage= $request->file('brand_image');
            // Loop through each uploaded image
            foreach ($brandImage as $brand_image) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $brand_image->getClientOriginalExtension();

                // Store the image in the public folder with the unique name
                $brand_image->move('brand_image', $uniqueName);

                // Create a new product image record with the product_id and unique name
                $brand->brand_image = 'brand_image/'.$uniqueName;
                $brand->save();
            }
        }
        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }
    public function destroy($id){
            $brand = Brand::findOrFail($id)->delete();
            return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
        }
}
