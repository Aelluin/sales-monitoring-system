<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        // Get a random product from the existing products in the database
        $product = Product::inRandomOrder()->first();  // Ensure only existing products are used

        // Fixed payment methods
        $paymentMethods = ['online', 'credit card', 'cash'];

        // Calculate the total price as quantity * product price
        $quantity = $this->faker->numberBetween(1, 5);
        $totalPrice = $product->price * $quantity;

        // Generate random dates between 2021 and 2024 for created_at and updated_at
        $randomDate = $this->faker->dateTimeBetween('2021-01-01', '2024-12-31');

        return [
            'product_id' => $product->id,  // Use the id of the selected product
            'quantity' => $quantity,  // Random quantity between 1 and 5
            'total_price' => $totalPrice,  // Total price based on product price and quantity
            'payment_method' => $this->faker->randomElement($paymentMethods),  // Random payment method
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'customer_address' => $this->faker->address(),
            'user_id' => User::inRandomOrder()->first()->id,  // Use a random existing user
            'created_at' => $randomDate,  // Random created_at date between 2021 and 2024
            'updated_at' => $randomDate,  // Use the same date for updated_at (or make it different if you want)
        ];
    }
}

