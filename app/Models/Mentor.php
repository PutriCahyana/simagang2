<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentor extends Model
{
    use HasFactory;
    protected $table = 'mentor'; 
    protected $primaryKey = 'mentor_id';

    protected $fillable = [
        'mentor_id',
        'handphone',
        'fungsi',
        'signature_path',
    ];
    
    // Accessor untuk nomor_hp (alias dari handphone)
    public function getNomorHpAttribute()
    {
        return $this->handphone;
    }
    
    // Mutator untuk nomor_hp (alias dari handphone)
    public function setNomorHpAttribute($value)
    {
        $this->attributes['handphone'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'mentor_id', 'id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'mentor_id', 'mentor_id');
    }

    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'mentor_id', 'mentor_id');
    }

}
