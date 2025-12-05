<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $primaryKey = 'materi_id';

    protected $fillable = [
        'judul',
        'room_id',
        'deskripsi',
        'konten',
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
        ];

        return $types[strtolower($extension)] ?? 'File';
    }
}
