<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Materi extends Model
{
    protected $table = 'materi';
    protected $primaryKey = 'materi_id';

    protected $fillable = [
        'judul',
        'room_id',
        'deskripsi',
        'konten',
        'file_path',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function getFileType()
    {
        if (!$this->file_path) return null;
        
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        
        $types = [
            'pdf' => 'PDF',
            'doc' => 'Word',
            'docx' => 'Word',
            'ppt' => 'PowerPoint',
            'pptx' => 'PowerPoint',
            'xls' => 'Excel',
            'xlsx' => 'Excel',
            'jpg' => 'Gambar',
            'jpeg' => 'Gambar',
            'png' => 'Gambar',
            'gif' => 'Gambar',
            'mp4' => 'Video',
            'avi' => 'Video',
            'mov' => 'Video',
            'zip' => 'Archive',
            'rar' => 'Archive',
        ];

        return $types[strtolower($extension)] ?? 'File';
    }

    public function getFileIcon()
    {
        if (!$this->file_path) return 'fas fa-file';
        
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        
        $icons = [
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc' => 'fas fa-file-word text-primary',
            'docx' => 'fas fa-file-word text-primary',
            'ppt' => 'fas fa-file-powerpoint text-warning',
            'pptx' => 'fas fa-file-powerpoint text-warning',
            'xls' => 'fas fa-file-excel text-success',
            'xlsx' => 'fas fa-file-excel text-success',
            'jpg' => 'fas fa-file-image text-info',
            'jpeg' => 'fas fa-file-image text-info',
            'png' => 'fas fa-file-image text-info',
            'gif' => 'fas fa-file-image text-info',
            'mp4' => 'fas fa-file-video text-purple',
            'avi' => 'fas fa-file-video text-purple',
            'mov' => 'fas fa-file-video text-purple',
            'zip' => 'fas fa-file-archive text-secondary',
            'rar' => 'fas fa-file-archive text-secondary',
        ];

        return $icons[strtolower($extension)] ?? 'fas fa-file';
    }

    public function getFileSizeFormatted()
    {
        if (!$this->file_path || !Storage::exists('public/' . $this->file_path)) {
            return null;
        }

        $bytes = Storage::size('public/' . $this->file_path);
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}