<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToTicketsAndUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('estimation_in_days')->nullable()->after('priority');
            $table->string('asset_url')->nullable()->after('description');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('monthly_energy_limit')->default(176)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['estimation_in_days', 'asset_url']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('monthly_energy_limit');
        });
    }
}