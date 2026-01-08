<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTicketApprovalsForPublicAccess extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ticket_approvals', function (Blueprint $table) {
            $table->foreignId('approver_id')->nullable()->change(); // Make nullable for external approvers
            $table->string('approver_name')->nullable()->after('approver_id');
            $table->string('approver_email')->nullable()->after('approver_name');
            $table->string('approval_token')->unique()->nullable()->after('approver_email');
            $table->string('ip_address')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('ticket_approvals', function (Blueprint $table) {
            $table->dropColumn(['approver_name', 'approver_email', 'approval_token', 'ip_address']);
            $table->foreignId('approver_id')->nullable(false)->change();
        });
    }
}