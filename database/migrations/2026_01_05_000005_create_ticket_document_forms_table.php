<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketDocumentFormsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ticket_document_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_document_id')->constrained()->cascadeOnDelete();
            $table->json('form_data'); // Store form fields as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ticket_document_forms');
    }
}