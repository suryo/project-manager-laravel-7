<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateStatusEnumsInTasksAndTickets extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Update tasks table
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo', 'in_progress', 'review', 'test', 'check', 'done') DEFAULT 'todo'");

        // Update tickets table
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open', 'in_progress', 'on_hold', 'review', 'test', 'check', 'completed', 'cancelled') DEFAULT 'open'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert tasks table
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo', 'in_progress', 'done') DEFAULT 'todo'");

        // Revert tickets table
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open', 'in_progress', 'on_hold', 'completed', 'cancelled') DEFAULT 'open'");
    }
}
