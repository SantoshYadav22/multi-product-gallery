<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'image_path' => 'product_images/' . $this->faker->image('public/storage/product_images', 640, 480, null, false),
                        'image_path' => 'product_images/' . $this->faker->uuid . '.jpg',

        ];
    }
}
