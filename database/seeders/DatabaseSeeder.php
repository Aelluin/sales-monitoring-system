<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Seed products first to ensure that product IDs are available
        \App\Models\Product::factory(50)->create();  // Adjust the number of products as needed

        // Then seed sales, ensuring product_id is valid
        \App\Models\Sale::factory(50)->create(); // Generate 50 sales records with random data
    }
}
