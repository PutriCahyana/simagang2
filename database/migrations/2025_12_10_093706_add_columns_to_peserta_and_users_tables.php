<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto_profil')->nullable()->after('role');
        });

        // Tambah kolom di tabel peserta
        Schema::table('peserta', function (Blueprint $table) {
            $table->string('nim')->nullable()->after('peserta_id');
            $table->string('fungsi')->nullable()->after('institut');
            $table->string('email')->nullable()->after('fungsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto_profil');
        });

        Schema::table('peserta', function (Blueprint $table) {
            $table->dropColumn(['nim', 'fungsi', 'email']);
        });
    }
};