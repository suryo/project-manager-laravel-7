<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoacToProjectsAndTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('mgmt_phase', ['Planning', 'Organizing', 'Actuating', 'Controlling'])->default('Planning')->after('project_status_id');
            $table->longText('mgmt_planning_notes')->nullable();
            $table->longText('mgmt_organizing_notes')->nullable();
            $table->longText('mgmt_actuating_notes')->nullable();
            $table->longText('mgmt_controlling_notes')->nullable();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('mgmt_phase', ['Planning', 'Organizing', 'Actuating', 'Controlling'])->default('Actuating')->after('status');
            $table->text('mgmt_notes')->nullable();
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
            $table->dropColumn(['mgmt_phase', 'mgmt_planning_notes', 'mgmt_organizing_notes', 'mgmt_actuating_notes', 'mgmt_controlling_notes']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['mgmt_phase', 'mgmt_notes']);
        });
    }
}
