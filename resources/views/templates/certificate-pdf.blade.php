<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $certificate->user->nama }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
        }
        
        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            object-fit: cover;
        }
        
        .content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding: 12% 20%;
            z-index: 2;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2.5%;
        }
        
        .header h1 {
            font-size: 1.75rem;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 0.15em;
            margin-bottom: 0.3rem;
        }
        
        .header .nomor {
            font-size: 0.875rem;
            color: #1f2937;
        }
        
        .intro {
            text-align: center;
            font-size: 0.95rem;
            color: #1f2937;
            margin-bottom: 3%;
        }
        
        .data-table {
            margin-bottom: 3.5%;
            padding-left: 8%;
            padding-right: 8%;
        }
        
        .data-table table {
            width: 100%;
            font-size: 0.95rem;
            line-height: 1.9;
            color: #1f2937;
        }
        
        .data-table td {
            padding-bottom: 0.4rem;
            vertical-align: top;
        }
        
        .data-table .label {
            width: 150px;
        }
        
        .data-table .separator {
            width: 30px;
        }
        
        .data-table .value {
            font-weight: 400;
        }
        
        .completion-text {
            text-align: center;
            font-size: 0.95rem;
            color: #1f2937;
            margin-bottom: 0.8%;
        }
        
        .predikat-section {
            text-align: center;
            margin-bottom: 6%;
        }
        
        .predikat-section .main-text {
            font-size: 1.1rem;
            font-weight: bold;
            color: #1f2937;
            line-height: 1.6;
            margin-bottom: 0.2rem;
        }
        
        .predikat-section .predikat {
            text-transform: uppercase;
        }
        
        .predikat-section .company {
            font-size: 0.95rem;
            color: #1f2937;
        }
        
        .signature {
            position: absolute;
            bottom: 12%;
            right: 20%;
            text-align: left;
        }
        
        .signature p {
            font-size: 0.875rem;
            line-height: 1.6;
            color: #1f2937;
            margin-bottom: 0.1rem;
        }
        
        .signature .name-space {
            margin-bottom: 5rem;
        }
        
        .signature .name {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Background Image (Base64 Encoded) -->
        @php
            $imagePath = public_path('assets/certificates/sertif-template.jpg');
            $imageData = base64_encode(file_get_contents($imagePath));
            $src = 'data:image/jpeg;base64,' . $imageData;
        @endphp
        <img src="{{ $src }}" alt="Certificate Background" class="background">
        
        <!-- Content Overlay -->
        <div class="content">
            <!-- Header -->
            <div class="header">
                <h1>SURAT KETERANGAN</h1>
                <p class="nomor">No. {{ $certificate->nomor_surat }}</p>
            </div>
            
            <p class="intro">Dengan ini menerangkan bahwa :</p>
            
            <!-- Data Peserta -->
            <div class="data-table">
                <table>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $certificate->user->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIM</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $certificate->user->peserta->nim ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Universitas</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $certificate->user->peserta->institut ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Fungsi</td>
                        <td class="separator">:</td>
                        <td class="value" style="text-transform: capitalize;">{{ $certificate->user->joinedRooms->pluck('nama_room')->first() ?? 'Technical' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Masa PKL</td>
                        <td class="separator">:</td>
                        <td class="value">
                            {{ \Carbon\Carbon::parse($certificate->user->peserta->periode_start)->format('d M Y') }} s.d 
                            {{ \Carbon\Carbon::parse($certificate->user->peserta->periode_end)->format('d M Y') }}
                        </td>
                    </tr>
                </table>
            </div>
            
            <p class="completion-text">Telah Menyelesaikan :</p>
            
            <!-- Predikat -->
            <div class="predikat-section">
                <p class="main-text">
                    Praktik Kerja Lapangan dengan "<span class="predikat">{{ $certificate->predikat }}</span>"
                </p>
                <p class="company">di PT Perta Arun Gas Lhokseumawe</p>
            </div>
            
            <!-- Signature Area -->
            <div class="signature">
                <p>{{ $settings->lokasi ?? 'Lhokseumawe' }}, {{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('d M Y') }}</p>
                <p>PT Perta Arun Gas</p>
                <p class="name-space">{{ $settings->pjs_jabatan ?? 'Pjs. Manager HR Development' }}</p>
                <p class="name">{{ $settings->pjs_nama ?? 'Safril' }}</p>
            </div>
        </div>
    </div>
</body>
</html>