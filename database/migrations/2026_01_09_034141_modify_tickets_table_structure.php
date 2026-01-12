<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyTicketsTableStructure extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Change 'type' to string to support new types (DM, Design, Web)
            // We can't easily modify ENUM in some DBs, so changing to string is safer and more flexible
            $table->string('type')->change();
            
            // Change 'description' to longText to support large content (base64 images)
            $table->longText('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Revert description to text
            $table->text('description')->change();
            
            // Reverting 'type' back to ENUM is tricky if data contains new values.
            // We will leave it as string or attempt to revert if data allows.
            // Ideally, we shouldn't revert strictly to the old ENUM if we have 'Web' data.
            // $table->enum('type', ['new_feature', 'update', 'bug_fix', 'enhancement'])->change();
        });
    }
}