<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTicketUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ticket_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['ticket_id', 'user_id']);
        });

        // Migrate existing data
        $tickets = DB::table('tickets')->whereNotNull('assigned_to')->get();
        foreach ($tickets as $ticket) {
            DB::table('ticket_user')->insert([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->assigned_to,
                'assigned_at' => $ticket->assigned_at ?? $ticket->updated_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop old columns
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'assigned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Add columns back
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
        });

        // Restore data (take first assignee if multiple)
        $assignments = DB::table('ticket_user')->get();
        foreach ($assignments as $assignment) {
            // Only update if not already set (simplistic restore)
            DB::table('tickets')
                ->where('id', $assignment->ticket_id)
                ->whereNull('assigned_to')
                ->update([
                    'assigned_to' => $assignment->user_id,
                    'assigned_at' => $assignment->assigned_at
                ]);
        }

        Schema::dropIfExists('ticket_user');
    }
}