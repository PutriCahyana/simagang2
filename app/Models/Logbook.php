<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'date',
        'jam_masuk',
        'jam_keluar',
        'aktivitas',
        'keterangan',
        'keterangan_asli',  // ← TAMBAHKAN INI
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationship: Peserta yang membuat logbook
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relationship: Room tempat logbook dibuat
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    /**
     * Relationship: Mentor yang approve logbook
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Helper: Set default jam berdasarkan hari
     */
    public static function getDefaultJam($date)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        
        // Jumat (5)
        if ($dayOfWeek == Carbon::FRIDAY) {
            return [
                'jam_masuk' => '07:30',
                'jam_keluar' => '17:00',
            ];
        }
        
        // Senin - Kamis (1-4)
        return [
            'jam_masuk' => '07:30',
            'jam_keluar' => '16:30',
        ];
    }

    /**
     * Helper: Cek apakah tanggal adalah weekend
     */
    public static function isWeekend($date)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        return in_array($dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);
    }

    /**
     * Helper: Get label keterangan
     */
    public function getKeteranganLabelAttribute()
    {
        $labels = [
            'offline_kantor' => 'Offline Kantor',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'online' => 'Online',
            'alpha' => 'Alpha',
        ];

        return $labels[$this->keterangan] ?? $this->keterangan;
    }
}