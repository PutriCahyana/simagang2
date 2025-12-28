<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table untuk settings sertifikat
        Schema::create('certificate_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_counter')->default(203); // Counter untuk nomor surat
            $table->string('format_tetap')->default('PAG1300'); // Format yang tetap
            $table->string('suffix')->default('S0'); // Suffix di belakang
            $table->string('pjs_nama')->nullable(); // Nama penandatangan
            $table->string('pjs_jabatan')->nullable(); // Jabatan penandatangan
            $table->string('lokasi')->default('Lhokseumawe'); // Lokasi default
            $table->string('signature_path')->nullable(); // Path file tanda tangan
            $table->timestamps();
        });
        
        // Table untuk sertifikat
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_surat')->unique(); // e.g. "203/PAG1300/2024-S0"
            $table->enum('predikat', ['CUKUP BAIK', 'BAIK', 'SANGAT BAIK'])->default('BAIK');
            $table->date('tanggal_terbit'); // Tanggal generate
            $table->enum('status', ['draft', 'approved'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('pdf_data')->nullable(); // Store PDF binary or path
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('certificate_settings');
    }
};