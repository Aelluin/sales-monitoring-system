<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,           // Random product name
            'price' => $this->faker->randomFloat(2, 10, 100), // Random price between 10 and 100
            'description' => $this->faker->sentence, // Random product description
            // Add other fields here based on the Product model's structure
        ];
    }
}
