<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectStatusIdToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('project_status_id')->nullable()->after('description')->constrained('project_statuses')->nullOnDelete();
            // We will drop the enum status column later or keep it for backup? 
            // Let's drop it to force usage of new system.
            // But we can't drop it immediately if we want to migrate data. 
            // For simplicity in this dev phase, we'll just drop it.
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_status_id']);
            $table->dropColumn('project_status_id');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
        });
    }
}