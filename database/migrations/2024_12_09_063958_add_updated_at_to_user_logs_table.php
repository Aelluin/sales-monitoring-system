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
        Schema::table('user_logs', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable(); // Add the updated_at column
        });
    }

    public function down()
    {
        Schema::table('user_logs', function (Blueprint $table) {
            $table->dropColumn('updated_at'); // Rollback if necessary
        });
    }
};
