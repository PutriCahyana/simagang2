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
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id('pengumuman_id');
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('mentor_id');
            $table->string('judul', 255);
            $table->text('isi'); // max 500 karakter, validasi di controller
            $table->boolean('is_penting')->default(false);
            $table->integer('durasi_tampil'); // dalam jam: 24, 72 (3 hari), 168 (7 hari), 720 (30 hari)
            $table->timestamp('tanggal_kadaluarsa');
            $table->timestamps();

            // Foreign keys
            $table->foreign('room_id')
                  ->references('room_id')
                  ->on('room')
                  ->onDelete('cascade');
            
            $table->foreign('mentor_id')
                  ->references('mentor_id')
                  ->on('mentor')
                  ->onDelete('cascade');

            // Index untuk query performance
            $table->index(['room_id', 'tanggal_kadaluarsa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};