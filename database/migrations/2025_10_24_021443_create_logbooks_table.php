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
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->unsignedBigInteger('user_id'); // peserta
            $table->unsignedBigInteger('room_id');
            
            // Logbook Data
            $table->date('date');
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->text('aktivitas');
            $table->enum('keterangan', ['offline_kantor', 'sakit', 'izin', 'online', 'alpha'])->default('offline_kantor');
            
            // Approval
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('room_id')->on('room')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique: 1 peserta hanya bisa buat 1 logbook per tanggal per room
            $table->unique(['user_id', 'room_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};