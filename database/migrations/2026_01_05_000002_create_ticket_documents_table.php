<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ticket_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', [
                // Mandatory Documents
                'request_form',
                'user_requirements',
                'functional_spec',
                'project_plan',
                'user_manual',
                'bast',
                // Supporting Documents
                'user_story',
                'requirement_signoff',
                'change_request',
                'uat_report',
                'installation_report'
            ]);
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size'); // bytes
            $table->string('mime_type');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ticket_documents');
    }
}