<?php

namespace App\Exports;

use App\Models\Logbook;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LogbookExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Ambil data logbook yang di-approve
     */
    public function collection()
    {
        return Logbook::where('user_id', $this->userId)
            ->where('is_approved', true)
            ->with(['room', 'approver.mentor'])
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Tentukan kolom header
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Aktivitas',
            'Keterangan',
            'Room',
            'Approved By',
            'Tanda Tangan',
        ];
    }

    /**
     * Mapping tiap baris data
     */
    public function map($logbook): array
    {
        static $no = 1;

        $signatureText = '(Belum ada TTD)';
        $signaturePath = $logbook->approver->mentor->signature_path ?? null;

        if ($signaturePath && Storage::disk('public')->exists($signaturePath)) {
            $signatureText = '✓ Tersedia';
        }

        return [
            $no++,
            $logbook->date->format('d/m/Y'),
            $logbook->jam_masuk,
            $logbook->jam_keluar,
            $logbook->aktivitas,
            $logbook->keterangan_label,
            $logbook->room->nama_room ?? '-',
            $logbook->approver->nama ?? '-',
            $signatureText,
        ];
    }

    /**
     * Styling dasar untuk header dan border
     */
    public function styles(Worksheet $sheet)
    {
        // Header bold + center
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Border untuk semua data
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto size kolom
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
