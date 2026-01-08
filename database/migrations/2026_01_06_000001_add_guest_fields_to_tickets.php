<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestFieldsToTickets extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('guest_name')->nullable()->after('description');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_department')->nullable()->after('guest_email');
            $table->string('guest_phone')->nullable()->after('guest_department');
            $table->string('tracking_token')->unique()->nullable()->after('guest_phone');
            $table->boolean('is_public_request')->default(false)->after('tracking_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'guest_name',
                'guest_email',
                'guest_department',
                'guest_phone',
                'tracking_token',
                'is_public_request'
            ]);
        });
    }
}