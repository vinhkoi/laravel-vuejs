<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Product::create([
            'title'=>'abc',
            'slug'=>'dell',
            'quantity'=>15,
            'description'=>'This is a description of product abc.',
            'published'=>1,
            'instock'=>1,
            'price'=>20,
            'category_id'=>1,
            'brand_id'=>1,
        ]);
        Product::create([
            'title'=>'abc',
            'slug'=>'samsung',
            'quantity'=>15,
            'description'=>'This is a description of product abc.',
            'published'=>1,
            'instock'=>1,
            'price'=>20,
            'category_id'=>2,
            'brand_id'=>2,
        ]);
    }
}
