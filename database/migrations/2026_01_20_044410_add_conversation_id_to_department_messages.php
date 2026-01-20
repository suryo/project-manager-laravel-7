<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConversationIdToDepartmentMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('department_messages', function (Blueprint $table) {
            $table->string('conversation_id')->nullable()->index()->after('department_id');
            $table->boolean('is_from_staff')->default(false)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('department_messages', function (Blueprint $table) {
            $table->dropColumn(['conversation_id', 'is_from_staff']);
        });
    }
}
