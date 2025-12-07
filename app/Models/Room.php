<?php

namespace App\Models;

use App\Models\Mentor;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'room';
    protected $primaryKey = 'room_id';
    public $timestamps = true;

    protected $fillable = [
        'nama_room',
        'deskripsi',
        'mentor_id',
        'code',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            // generate kode unik, misal: ROOM-2501-ABC1
            do {
                $code = 'ROOM-' . now()->format('ym') . '-' . strtoupper(Str::random(4));
            } while (self::where('code', $code)->exists());

            $room->code = $code;
        });
    }

    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id', 'mentor_id');
    }
    

    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id')
                    ->withTimestamps();
    }

    public function materis()
    {
        return $this->hasMany(Materi::class, 'room_id', 'room_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'room_id', 'room_id');
    }

    public function peserta()
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id')
                    ->where('role', 'peserta')
                    ->withTimestamps();
    }

    // Helper: hitung total peserta
    public function getTotalPeserta()
    {
        return $this->users()->where('role', 'peserta')->count();
    }

    // Helper: hitung total task
    public function getTotalTasks()
    {
        return $this->tasks()->count();
    }
}