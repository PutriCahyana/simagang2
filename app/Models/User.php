<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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
        return $this->belongsToMany(Room::class, 'room_user', 'user_id', 'room_id')
                ->withPivot('status', 'end_date')
                ->withTimestamps();
    }

    public function mentor()
    {
        return $this->hasOne(Mentor::class, 'mentor_id', 'id');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class, 'user_id');
    }

    public function approvedLogbooks()
    {
        return $this->hasMany(Logbook::class, 'approved_by');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_user', 'user_id', 'room_id')
                    ->withTimestamps();
    }

    public function managedRooms()
    {
        return $this->hasManyThrough(
            Room::class,
            Mentor::class,
            'mentor_id',
            'mentor_id',
            'id',
            'mentor_id'
        );
    }
}