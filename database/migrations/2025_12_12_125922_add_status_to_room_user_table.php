<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('room_user', function (Blueprint $table) {
            // Pakai enum biar lebih jelas status-nya
            $table->enum('status', ['active', 'completed', 'terminated'])
                  ->default('active')
                  ->after('room_id');
        });
    }

    public function down()
    {
        Schema::table('room_user', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};