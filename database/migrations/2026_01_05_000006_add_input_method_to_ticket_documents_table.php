<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputMethodToTicketDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ticket_documents', function (Blueprint $table) {
            $table->enum('input_method', ['form', 'upload', 'auto'])->default('upload')->after('document_type');
            $table->boolean('allow_multiple')->default(false)->after('input_method');
            $table->unsignedBigInteger('parent_id')->nullable()->after('allow_multiple'); // For grouping multiple files
            $table->foreign('parent_id')->references('id')->on('ticket_documents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('ticket_documents', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['input_method', 'allow_multiple', 'parent_id']);
        });
    }
}