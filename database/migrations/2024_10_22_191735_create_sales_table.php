<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Reference to the product
            $table->decimal('total_price', 8, 2); // Total price for the sale
            $table->timestamps(); // This will automatically add created_at and updated_at

            // Foreign key to reference the products table
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop foreign key constraint before dropping the table
            $table->dropForeign(['product_id']);
        });

        // Now we can safely drop the table
        Schema::dropIfExists('sales');
    }
};
