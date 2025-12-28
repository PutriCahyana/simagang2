<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('room_user', function (Blueprint $table) {
            // Tambah kolom end_date untuk tanggal selesai magang di room ini
            $table->date('end_date')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('room_user', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};