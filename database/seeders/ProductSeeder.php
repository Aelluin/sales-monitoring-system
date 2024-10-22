<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('products')->insert([
            ['name' => 'Product A', 'price' => 50.00, 'description' => 'A sample product', 'quantity' => 10],
            ['name' => 'Product B', 'price' => 30.00, 'description' => 'Another product', 'quantity' => 20],
            // Add more products as needed...
        ]);
    }
}
