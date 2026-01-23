<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixProjectsUserIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop the foreign key constraint that references users_old
            $table->dropForeign(['user_id']);
            
            // Re-create the foreign key correctly referencing the users table
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            
            // Restore it to users_old (though not recommended, this is the inverse of the fix)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users_old')
                  ->onDelete('cascade');
        });
    }
}
