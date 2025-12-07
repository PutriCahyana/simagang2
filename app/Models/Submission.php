<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submission';
    protected $primaryKey = 'submission_id';

    protected $fillable = [
        'task_id',
        'user_id',        // Menggunakan user_id sesuai database
        'file_path',
        'link',
        'catatan',
        'status',
        'nilai',
        'feedback',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Task
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'task_id');
    }

    // Relasi ke User/Peserta
    public function peserta()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Alias untuk user (karena kita pakai user_id, bukan peserta_id)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Helper: cek apakah submission ini adalah file atau link
    public function isFileSubmission()
    {
        return !empty($this->file_path);
    }

    public function isLinkSubmission()
    {
        return !empty($this->link);
    }

    // Helper: get file name
    public function getFileName()
    {
        if (!$this->file_path) return null;
        return basename($this->file_path);
    }

    // Helper: cek apakah sudah dinilai
    public function isGraded()
    {
        return $this->nilai !== null;
    }

    // Helper: get status badge
    public function getStatusBadge()
    {
        $statuses = [
            'pending' => ['class' => 'warning', 'text' => 'Pending'],
            'graded' => ['class' => 'success', 'text' => 'Dinilai'],
            'late' => ['class' => 'danger', 'text' => 'Terlambat'],
        ];

        return $statuses[$this->status] ?? ['class' => 'secondary', 'text' => 'Unknown'];
    }
}