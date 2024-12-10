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
        // Check if the foreign key exists, if not, add it
        if (!Schema::hasTable('sales') || !Schema::hasColumn('sales', 'user_id')) {
            return; // Exit if 'user_id' does not exist
        }

        // Check if foreign key exists
        if (!Schema::hasColumn('sales', 'user_id') || !Schema::hasTable('users')) {
            return;
        }

        // Add the foreign key constraint if it doesn't already exist
        Schema::table('sales', function (Blueprint $table) {
            // Add the foreign key constraint
            if (!Schema::hasColumn('sales', 'user_id')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Check and drop the foreign key if it exists
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
