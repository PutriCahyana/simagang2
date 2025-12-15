<?php

namespace App\Exports;

use App\Models\Logbook;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LogbookExcelExport implements FromCollection, WithEvents
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect([]);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $user = User::find($this->userId);
                $logbooks = Logbook::where('user_id', $this->userId)
                    ->where('is_approved', true)
                    ->with(['room', 'approver'])
                    ->orderBy('date', 'asc')
                    ->get();
                
                $bulan = $logbooks->count() > 0 ? \Carbon\Carbon::parse($logbooks->first()->date)->locale('id')->translatedFormat('F, Y') : 'Oktober, 2025';
                
                // Merge cells untuk judul
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'LOGBOOK KEGIATAN MAGANG');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                
                // Info mahasiswa
                $sheet->mergeCells('A2:B2');
                $sheet->setCellValue('A2', 'NAMA');
                $sheet->mergeCells('C2:G2');
                $sheet->setCellValue('C2', ': ' . ($user->nama ?? ''));
                
                $sheet->mergeCells('A3:B3');
                $sheet->setCellValue('A3', 'NIM MAHASISWA');
                $sheet->mergeCells('C3:G3');
                $sheet->setCellValueExplicit('C3', ': ' . ($user->peserta->nim ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
                $sheet->mergeCells('A4:B4');
                $sheet->setCellValue('A4', 'TEMPAT MAGANG');
                $sheet->mergeCells('C4:G4');
                $sheet->setCellValue('C4', ': PT. Perta Arun Gas');
                
                // Group logbook by bulan
                $logbooksByMonth = $logbooks->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->date)->format('Y-m');
                });
                
                $currentRow = 6;
                
                foreach($logbooksByMonth as $monthKey => $monthLogbooks) {
                    $bulanLabel = \Carbon\Carbon::parse($monthLogbooks->first()->date)->locale('id')->translatedFormat('F, Y');
                    
                    // Bulan header
                    $sheet->setCellValue('A' . $currentRow, 'Bulan: ' . $bulanLabel);
                    $sheet->mergeCells('A' . $currentRow . ':G' . $currentRow);
                    $sheet->getStyle('A' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true]
                    ]);
                    
                    $currentRow += 2; // Skip 1 row
                    
                    // Header tabel
                    $headerRow = $currentRow;
                    $sheet->setCellValue('A' . $headerRow, 'No');
                    $sheet->setCellValue('B' . $headerRow, 'Hari/Tanggal');
                    $sheet->setCellValue('C' . $headerRow, 'Waktu Kegiatan (jam)');
                    $sheet->setCellValue('D' . $headerRow, 'Uraian Kegiatan');
                    $sheet->setCellValue('E' . $headerRow, 'Paraf Instruktur');
                    $sheet->setCellValue('F' . $headerRow, 'Keterangan');
                    
                    // Style header tabel
                    $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F0F0F0']
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ]
                    ]);
                    
                    $currentRow++;
                    
                    // Isi data logbook untuk bulan ini
                    $no = 1;
                    foreach($monthLogbooks as $logbook) {
                        $sheet->setCellValue('A' . $currentRow, $no);
                        $sheet->setCellValue('B' . $currentRow, $logbook->date->locale('id')->translatedFormat('l, d-m-Y'));
                        $sheet->setCellValue('C' . $currentRow, $logbook->jam_masuk . '-' . $logbook->jam_keluar);
                        $sheet->setCellValue('D' . $currentRow, $logbook->aktivitas);
                        $sheet->setCellValue('E' . $currentRow, '');
                        $sheet->setCellValue('F' . $currentRow, $logbook->keterangan_label);
                        
                        $currentRow++;
                        $no++;
                    }
                    
                    // Apply borders untuk tabel bulan ini
                    $lastRow = $currentRow - 1;
                    $sheet->getStyle('A' . $headerRow . ':F' . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);
                    
                    // Center alignment untuk kolom tertentu
                    $sheet->getStyle('A' . $headerRow . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $headerRow . ':B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('C' . $headerRow . ':C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $headerRow . ':E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F' . $headerRow . ':F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Vertical alignment top
                    $sheet->getStyle('A' . $headerRow . ':F' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                    
                    // Wrap text untuk kolom aktivitas
                    $dataStartRow = $headerRow + 1;
                    $sheet->getStyle('D' . $dataStartRow . ':D' . $lastRow)->getAlignment()->setWrapText(true);
                    
                    $currentRow += 2; // Spacing before next month
                }
            }
        ];
    }
}