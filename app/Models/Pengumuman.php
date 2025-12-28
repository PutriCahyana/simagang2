<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Mentor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';
    protected $primaryKey = 'pengumuman_id';
    public $timestamps = true;

    protected $fillable = [
        'room_id',
        'mentor_id',
        'judul',
        'isi',
        'is_penting',
        'durasi_tampil',
        'tanggal_kadaluarsa',
    ];

    protected $casts = [
        'durasi_tampil' => 'integer', // ✅ TAMBAHKAN INI - PERBAIKAN UTAMA
        'is_penting' => 'boolean',
        'tanggal_kadaluarsa' => 'datetime',
    ];

    /**
     * Relasi ke Room
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    /**
     * Relasi ke Mentor
     */
    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id', 'mentor_id');
    }

    /**
     * Scope: Ambil pengumuman yang masih aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('tanggal_kadaluarsa', '>=', now());
    }

    /**
     * Scope: Ambil pengumuman berdasarkan room
     */
    public function scopeByRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Accessor: Format durasi tampil dalam bentuk readable
     */
    public function getDurasiTampilTextAttribute()
    {
        $jam = $this->durasi_tampil;
        
        if ($jam == 24) return '24 Jam';
        if ($jam == 72) return '3 Hari';
        if ($jam == 168) return '7 Hari';
        if ($jam == 720) return '30 Hari';
        
        return $jam . ' Jam';
    }

    /**
     * Accessor: Cek apakah pengumuman masih aktif
     */
    public function getIsAktifAttribute()
    {
        return $this->tanggal_kadaluarsa >= now();
    }

    /**
     * Helper: Set tanggal kadaluarsa otomatis
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($pengumuman) {
            if (!$pengumuman->tanggal_kadaluarsa) {
                // ✅ Cast ke integer untuk keamanan ekstra (opsional karena sudah di-cast di property)
                $durasi = (int) $pengumuman->durasi_tampil;
                $pengumuman->tanggal_kadaluarsa = now()->addHours($durasi);
            }
        });
    }
}