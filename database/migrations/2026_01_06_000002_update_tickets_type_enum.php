<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateTicketsTypeEnum extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Update type enum to match public form values
        DB::statement("ALTER TABLE tickets MODIFY COLUMN type ENUM('feature', 'bug', 'support', 'enhancement', 'new_feature', 'update', 'bug_fix') DEFAULT 'feature'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE tickets MODIFY COLUMN type ENUM('new_feature', 'update', 'bug_fix', 'enhancement') DEFAULT 'update'");
    }
}