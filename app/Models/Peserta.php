<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';
    
    protected $fillable = [
        'peserta_id',
        'nim',
        'institut',
        'fungsi',
        'email',
        'periode_start',
        'periode_end',
    ];

    protected $casts = [
        'periode_start' => 'date',
        'periode_end' => 'date',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'peserta_id', 'id');
    }
}