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
    Schema::create('products', function (Blueprint $table) {
        $table->id(); // Auto-incrementing ID
        $table->string('name'); // Product name
        $table->decimal('price', 8, 2); // Product price (8 digits total, 2 after the decimal)
        $table->text('description')->nullable(); // Optional product description
        $table->integer('quantity')->default(0); // Current stock quantity
        $table->timestamps(); // Created_at and updated_at timestamps
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
