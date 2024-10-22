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
        $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Links to products table
        $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Links to customers table
        $table->integer('quantity'); // Quantity sold
        $table->decimal('total_price', 8, 2); // Total price
        $table->timestamp('sale_date'); // Date of the sale
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
