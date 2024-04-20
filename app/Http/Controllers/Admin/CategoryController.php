<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $category->save();
        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }
    public function update(Request $request,$id){
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->update();
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }
    public function destroy($id){
        $category = Category::findOrFail($id)->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

}
