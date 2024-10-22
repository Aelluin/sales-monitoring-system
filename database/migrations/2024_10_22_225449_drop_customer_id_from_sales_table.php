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
        Schema::table('sales', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['customer_id']); // Make sure to use an array here

            // Drop the customer_id column
            $table->dropColumn('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Add the customer_id column back
            $table->unsignedBigInteger('customer_id')->nullable();

            // Optionally recreate the foreign key if needed
            // $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
