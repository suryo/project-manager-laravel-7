<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdatePriorityEnumInTicketsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Modify the enum definition to include new values
        // Note: DB::statement is required for modifying ENUM in MySQL as Schema builder doesn't support changing enum options directly on existing columns easily
        DB::statement("ALTER TABLE tickets MODIFY COLUMN priority ENUM('very_low', 'low', 'medium', 'high', 'very_high', 'urgent', 'super_urgent') DEFAULT 'medium'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert to original allowed values, mapping unknown ones to default 'medium' to avoid data loss on rollback if possible, 
        // but strictly speaking, rollback of enum reduction usually requires data cleanup first.
        // For safety, we will just revert the definition. Any data not fitting might cause issues or be truncated depending on strict mode.
        // We'll try to update invalid values first before reverting schema to prevent errors
        
        DB::table('tickets')
            ->whereIn('priority', ['very_low', 'very_high', 'super_urgent'])
            ->update(['priority' => 'medium']);

        DB::statement("ALTER TABLE tickets MODIFY COLUMN priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium'");
    }
}