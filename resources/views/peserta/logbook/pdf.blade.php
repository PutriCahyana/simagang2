<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logbook Kegiatan Magang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14pt;
            text-decoration: underline;
        }
        .info-section {
            margin-bottom: 15px;
            font-size: 11pt;
        }
        .info-section table {
            border: none;
        }
        .info-section td {
            padding: 3px 0;
            border: none;
        }
        .info-section td:first-child {
            width: 150px;
        }
        .info-section td:nth-child(2) {
            width: 10px;
        }
        table.logbook-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.logbook-table th,
        table.logbook-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        table.logbook-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        table.logbook-table td:first-child {
            text-align: center;
            width: 30px;
        }
        table.logbook-table td:nth-child(2) {
            width: 100px;
            text-align: center;
        }
        table.logbook-table td:nth-child(3) {
            width: 100px;
            text-align: center;
        }
        table.logbook-table td:nth-child(5) {
            width: 80px;
            text-align: center;
        }
        table.logbook-table td:nth-child(6) {
            width: 100px;
            text-align: center;
        }
        .month-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .month-header {
            margin-bottom: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LOGBOOK KEGIATAN MAGANG</h2>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td>NAMA</td>
                <td>:</td>
                <td>{{ $peserta->nama ?? '' }}</td>
            </tr>
            <tr>
                <td>NIM MAHASISWA</td>
                <td>:</td>
                <td>{{ $peserta->peserta->nim ?? '' }}</td>
            </tr>
            <tr>
                <td>TEMPAT MAGANG</td>
                <td>:</td>
                <td>PT. Perta Arun Gas</td>
            </tr>
        </table>
    </div>

    @php
        $logbooksByMonth = $logbooks->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->date)->format('Y-m');
        });
    @endphp

    @foreach($logbooksByMonth as $monthKey => $monthLogbooks)
        <div class="month-section">
            <div class="month-header">
                <strong>Bulan: {{ \Carbon\Carbon::parse($monthLogbooks->first()->date)->locale('id')->translatedFormat('F, Y') }}</strong>
            </div>

            <table class="logbook-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Hari/Tanggal</th>
                        <th>Waktu Kegiatan (jam)</th>
                        <th>Uraian Kegiatan</th>
                        <th>Paraf Instruktur</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthLogbooks as $index => $logbook)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $logbook->date->locale('id')->translatedFormat('l, d-m-Y') }}</td>
                        <td>{{ $logbook->jam_masuk }}-{{ $logbook->jam_keluar }}</td>
                        <td>{{ $logbook->aktivitas }}</td>
                        <td></td>
                        <td>{{ $logbook->keterangan_label }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    @if($logbooks->isEmpty())
        <div style="text-align: center; padding: 20px;">
            Belum ada logbook yang di-approve
        </div>
    @endif
</body>
</html>