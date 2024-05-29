<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    //
    public function index()
    {
        $banner = Banner::get();
        return Inertia::render("Admin/Product/BannerList", ['banner' => $banner]);
    }
    public function store(Request $request)
    {
        $banner = new Banner();
        $banner->title = $request->title;
        if ($request->hasFile('image_url')) {
            $bannerImages = $request->file('image_url');

            // Loop qua từng ảnh trong mảng
            foreach ($bannerImages as $bannerImage) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $bannerImage->getClientOriginalExtension();

                // Store the image in the public folder with the unique name
                $bannerImage->move('image_url', $uniqueName);

                // Assign the unique name to the brand's brand_image field
                $banner->image_url = 'image_url/' . $uniqueName;
                $banner->save();
            }
        }
        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->title = $request->title;
        if ($request->hasFile('image_url')) {
            $bannerImages = $request->file('image_url');

            // Loop qua từng ảnh trong mảng
            foreach ($bannerImages as $bannerImage) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $bannerImage->getClientOriginalExtension();

                // Store the image in the public folder with the unique name
                $bannerImage->move('image_url', $uniqueName);

                // Assign the unique name to the brand's brand_image field
                $banner->image_url = 'image_url/' . $uniqueName;
            }
        }
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id)->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
