<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory()
            ->count(5)
            ->create()
            ->each(function ($product) {
                ProductImage::factory()
                    ->count(5)
                    ->create([
                        'product_id' => $product->id,
                    ]);
            });
    }
}
