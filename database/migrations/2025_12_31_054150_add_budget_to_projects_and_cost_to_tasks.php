<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBudgetToProjectsAndCostToTasks extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('budget', 15, 2)->default(0)->after('end_date');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('cost', 15, 2)->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('budget');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
}