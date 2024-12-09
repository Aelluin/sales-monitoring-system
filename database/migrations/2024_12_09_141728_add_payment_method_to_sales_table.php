<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // In a new migration file (create using `php artisan make:migration add_payment_method_to_sales`)
public function up()
{
    Schema::table('sales', function (Blueprint $table) {
        $table->string('payment_method')->nullable(); // or required, depending on your use case
    });
}

public function down()
{
    Schema::table('sales', function (Blueprint $table) {
        $table->dropColumn('payment_method');
    });
}

};
