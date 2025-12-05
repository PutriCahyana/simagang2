<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    

    // Hapus atau ubah jadi
    public function mentorProfile()
    {
        return $this->hasOne(Mentor::class, 'mentor_id', 'id');
    }


    public function peserta()
    {
        return $this->hasOne(Peserta::class, 'peserta_id', 'id');
    }

    public function joinedRooms()
    {
        // ini untuk peserta → room (via pivot)
        return $this->belongsToMany(Room::class, 'room_user', 'user_id', 'room_id');
    }

    public function mentor()
    {
        return $this->hasOne(Mentor::class, 'mentor_id', 'id');
    }

/**
 * Relationship: Logbook yang dibuat oleh user (peserta)
 */
    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'user_id');
    }

/**
 * Relationship: Logbook yang di-approve oleh user (mentor)
 */
    public function approvedLogbooks()
    {
        return $this->hasMany(Logbook::class, 'approved_by');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_user', 'user_id', 'room_id')
                    ->withTimestamps();
    }

    // ============================================
// UNTUK MENTOR: rooms yang dia kelola
// ============================================
    public function managedRooms()
    {
        // Via tabel mentor dulu, baru ke rooms
        return $this->hasManyThrough(
            Room::class,      // Model tujuan
            Mentor::class,    // Model perantara
            'mentor_id',      // FK di tabel mentor (kolom di tabel mentor yang referensi ke users.id)
            'mentor_id',      // FK di tabel room (kolom di tabel room yang referensi ke mentor.mentor_id)
            'id',             // PK di tabel users
            'mentor_id'       // PK di tabel mentor
        );
    }

}
