<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom link untuk submission via URL
     */
    public function up()
    {
        Schema::table('submission', function (Blueprint $table) {
            // Tambah kolom link jika belum ada
            if (!Schema::hasColumn('submission', 'link')) {
                $table->text('link')->nullable()->after('file_path');
            }
            
            // Rename user_id ke peserta_id untuk konsistensi (opsional)
            // Uncomment jika ingin rename
            // if (Schema::hasColumn('submission', 'user_id')) {
            //     $table->renameColumn('user_id', 'peserta_id');
            // }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('submission', function (Blueprint $table) {
            if (Schema::hasColumn('submission', 'link')) {
                $table->dropColumn('link');
            }
        });
    }
};