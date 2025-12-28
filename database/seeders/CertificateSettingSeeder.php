<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateSetting;

class CertificateSettingSeeder extends Seeder
{
    public function run(): void
    {
        CertificateSetting::create([
            'nomor_counter' => 203,
            'format_tetap' => 'PAG1300',
            'suffix' => 'S0',
            'pjs_nama' => 'Safril',
            'pjs_jabatan' => 'Pjs. Manager HR Development',
            'lokasi' => 'Lhokseumawe'
        ]);
    }
}