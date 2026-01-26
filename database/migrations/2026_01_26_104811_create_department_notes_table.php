<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('department_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('color')->default('yellow'); // yellow, blue, green, pink, purple, orange
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            
            $table->index(['department_id', 'is_pinned', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('department_notes');
    }
}
