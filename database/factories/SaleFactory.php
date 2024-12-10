<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        // Get a random product from the existing products in the database
        $product = Product::inRandomOrder()->first();  // Get a random existing product

        // Fixed payment methods
        $paymentMethods = ['online', 'credit card', 'cash'];

        return [
            'product_id' => $product->id,  // Use the id of the selected product
            'quantity' => $this->faker->numberBetween(1, 5),  // Random quantity between 1 and 5
            'total_price' => $this->faker->randomFloat(2, 10, 100),  // Random total price
            'payment_method' => $this->faker->randomElement($paymentMethods),  // Random payment method
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'customer_address' => $this->faker->address(),
            'user_id' => $this->faker->numberBetween(1, 5),  // Ensure users with IDs 1-5 exist in the database
        ];
    }
}

