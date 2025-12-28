<?php

namespace App\Models;

use App\Models\Room;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $fillable = ['user_id', 'room_id', 'type', 'description', 'created_at'];
    public $timestamps = true;

    // Relationship ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship ke Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}