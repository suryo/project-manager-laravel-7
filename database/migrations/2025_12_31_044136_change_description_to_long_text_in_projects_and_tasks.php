<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDescriptionToLongTextInProjectsAndTasks extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->longText('description')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->longText('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }
}