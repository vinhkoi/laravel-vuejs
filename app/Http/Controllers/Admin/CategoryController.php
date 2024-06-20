<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //
    public function index(){
        $categories = Category::get();
        return Inertia::render("Admin/Product/CategoryList", ['categories' => $categories]);
    }
    public function store(Request $request){
        $category = new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
        if ($request->hasFile('imageUrl')) {
            $categoryImages = $request->file('imageUrl');

            // Loop qua từng ảnh trong mảng
            foreach ($categoryImages as $categoryImage) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $categoryImage->getClientOriginalExtension();

                // Store the image in the public folder with the unique name
                $categoryImage->move('imageUrl', $uniqueName);

                // Assign the unique name to the brand's brand_image field
                $category->imageUrl = 'imageUrl/' . $uniqueName;
            }
        }
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }
    public function update(Request $request,$id){
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        if ($request->hasFile('imageUrl')) {
            $categoryImages = $request->file('imageUrl');

            // Loop qua từng ảnh trong mảng
            foreach ($categoryImages as $categoryImage) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $categoryImage->getClientOriginalExtension();

                // Store the image in the public folder with the unique name
                $categoryImage->move('imageUrl', $uniqueName);

                // Assign the unique name to the brand's brand_image field
                $category->imageUrl = 'imageUrl/' . $uniqueName;
            }
        }
        $category->update();
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }
    public function destroy($id){
        $category = Category::findOrFail($id)->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

}
