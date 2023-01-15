<?php

namespace Database\Factories;

use App\Models\Product;
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
    public function definition()
    {
        return [
            'product_id' => Product::all()->random()->id,
            'url' => $this->faker->imageUrl(640, 480, 'cats', true, 'Faker'),
            "is_thumbnail" => $this->faker->boolean,
        ];
    }
}
