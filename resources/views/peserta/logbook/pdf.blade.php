<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logbook - {{ $peserta->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Arial', sans-serif;
        }
        .signature-img {
            max-width: 100px;
            max-height: 50px;
            object-fit: contain;
        }
    </style>
</head>
<body class="bg-white">
    <div class="max-w-full">
        <!-- Header -->
        <div class="text-center border-b-4 border-gray-800 pb-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-1">LOGBOOK KEGIATAN</h1>
            <p class="text-gray-600">Peserta Magang</p>
        </div>

        <!-- Info Peserta -->
        <div class="mb-6 bg-gray-50 p-4 rounded">
            <table class="w-full text-sm">
                <tr>
                    <td class="font-semibold w-40">Nama Peserta</td>
                    <td class="w-4">:</td>
                    <td>{{ $peserta->nama }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Username</td>
                    <td>:</td>
                    <td>{{ $peserta->username }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Tanggal Export</td>
                    <td>:</td>
                    <td>{{ now()->format('d F Y') }}</td>
                </tr>
            </table>
        </div>

        <!-- Table Logbook -->
        <table class="w-full border-collapse border border-gray-800 text-sm">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-800 p-2 w-8 text-center">No</th>
                    <th class="border border-gray-800 p-2 w-24">Tanggal</th>
                    <th class="border border-gray-800 p-2 w-28">Jam</th>
                    <th class="border border-gray-800 p-2">Aktivitas</th>
                    <th class="border border-gray-800 p-2 w-24">Keterangan</th>
                    <th class="border border-gray-800 p-2 w-32">Room</th>
                    <th class="border border-gray-800 p-2 w-24 text-center">TTD Mentor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logbooks as $index => $logbook)
                    <tr>
                        <td class="border border-gray-800 p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-800 p-2">{{ $logbook->date->format('d/m/Y') }}</td>
                        <td class="border border-gray-800 p-2 text-xs">{{ $logbook->jam_masuk }} - {{ $logbook->jam_keluar }}</td>
                        <td class="border border-gray-800 p-2 text-xs">{{ $logbook->aktivitas }}</td>
                        <td class="border border-gray-800 p-2 text-xs">{{ $logbook->keterangan_label }}</td>
                        <td class="border border-gray-800 p-2 text-xs">{{ $logbook->room->nama_room }}</td>
                        <td class="border border-gray-800 p-2 text-center">
                            @if($logbook->approver && $logbook->approver->mentor && $logbook->approver->mentor->signature_path)
                                <img src="{{ public_path('storage/' . $logbook->approver->mentor->signature_path) }}" 
                                     class="signature-img mx-auto" 
                                     alt="TTD">
                            @else
                                <span class="text-xs text-gray-500">(Belum ada TTD)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border border-gray-800 p-4 text-center text-gray-500">
                            Tidak ada logbook yang telah di-approve
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="mt-6">
            <p class="text-xs text-gray-500 italic">
                Dokumen ini digenerate otomatis pada {{ now()->format('d F Y H:i') }} WIB
            </p>
        </div>
    </div>
</body>
</html>