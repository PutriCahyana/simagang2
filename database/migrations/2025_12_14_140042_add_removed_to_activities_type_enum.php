<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah ENUM type di tabel activities
        DB::statement("ALTER TABLE activities MODIFY COLUMN type ENUM('task_added', 'task_completed', 'user_joined', 'assignment_submitted', 'removed') NOT NULL");
    }

    public function down()
    {
        // Kembalikan ke ENUM semula (tanpa 'removed' dan 'remove_participant')
        DB::statement("ALTER TABLE activities MODIFY COLUMN type ENUM('task_added', 'task_completed', 'user_joined', 'assignment_submitted') NOT NULL");
    }
};