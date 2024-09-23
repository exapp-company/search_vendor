<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productService = new ProductService();
        $products = Product::whereNull('slug')->get();
        foreach ($products as $product) {
            $productService->createSlug($product);
        }
    }
}
