<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_counter',
        'format_tetap',
        'suffix',
        'pjs_nama',
        'pjs_jabatan',
        'lokasi'
    ];

    // Helper method untuk generate nomor surat
    public static function generateNomorSurat()
    {
        $settings = self::first();
        
        if (!$settings) {
            // Create default settings if not exists
            $settings = self::create([
                'nomor_counter' => 203,
                'format_tetap' => 'PAG1300',
                'suffix' => 'S0',
                'lokasi' => 'Lhokseumawe'
            ]);
        }
        
        // Format: [counter]/[format_tetap]/[tahun]-[suffix]
        // Example: 203/PAG1300/2024-S0
        $nomorSurat = sprintf(
            '%d/%s/%d-%s',
            $settings->nomor_counter,
            $settings->format_tetap,
            now()->year,
            $settings->suffix
        );
        
        // Increment counter untuk next time
        $settings->increment('nomor_counter');
        
        return $nomorSurat;
    }

    // Helper untuk get settings (singleton pattern)
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'nomor_counter' => 203,
                'format_tetap' => 'PAG1300',
                'suffix' => 'S0',
                'lokasi' => 'Lhokseumawe'
            ]);
        }
        
        return $settings;
    }
}