<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Create 10 products for testing
        Product::factory()->count(10)->create();
    }
}

