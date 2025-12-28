@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Preview Sertifikat</h1>
            <p class="text-gray-600">{{ $certificate->user->nama }} - {{ $certificate->nomor_surat }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.sertifikat.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Certificate Info Card -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-semibold text-lg">
                    {{ strtoupper(substr($certificate->user->nama, 0, 1)) }}
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $certificate->user->nama }}</h3>
                    <p class="text-sm text-gray-600">{{ $certificate->user->peserta->nim ?? '-' }} - {{ $certificate->user->peserta->institut ?? '-' }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Approved
                </span>
                <p class="text-xs text-gray-600 mt-2">
                    Approved: {{ \Carbon\Carbon::parse($certificate->approved_at)->format('d M Y H:i') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Certificate Preview -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6" id="certificate-print">
        <div class="relative bg-white" style="width: 100%; aspect-ratio: 1.414; max-width: 1200px; margin: 0 auto;">
            <!-- Template Background -->
            <img src="{{ asset('assets/certificates/sertif-template.jpg') }}" 
                 alt="Template" 
                 class="absolute inset-0 w-full h-full object-cover">
            
            <!-- Certificate Content Overlay -->
            <div class="absolute inset-0" style="padding: 12% 20%;">
                
                <!-- Header - Title & Nomor Surat -->
                <div class="text-center" style="margin-bottom: 2.5%;">
                    <h1 class="font-bold text-gray-900" style="font-family: Arial, sans-serif; font-size: 1.75rem; letter-spacing: 0.15em; margin-bottom: 0.3rem;">SURAT KETERANGAN</h1>
                    <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem;">No. {{ $certificate->nomor_surat }}</p>
                </div>
                
                <p class="text-center text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.95rem; margin-bottom: 3%;">Dengan ini menerangkan bahwa :</p>
                
                <!-- Data Peserta -->
                <div style="margin-bottom: 3.5%; padding-left: 8%; padding-right: 8%;">
                    <table style="width: 100%; font-family: Arial, sans-serif; font-size: 0.95rem; line-height: 1.9;">
                        <tr>
                            <td style="width: 150px; padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Nama</td>
                            <td style="width: 30px; padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                            <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">{{ $certificate->user->nama }}</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">NIM</td>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                            <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">{{ $certificate->user->peserta->nim ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Universitas</td>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                            <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">{{ $certificate->user->peserta->institut ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Fungsi</td>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                            <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937; text-transform: capitalize;">{{ $certificate->user->joinedRooms->pluck('nama_room')->first() ?? 'Technical' }}</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Masa PKL</td>
                            <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                            <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">
                                {{ \Carbon\Carbon::parse($certificate->user->peserta->periode_start)->format('d M Y') }} s.d 
                                {{ \Carbon\Carbon::parse($certificate->user->peserta->periode_end)->format('d M Y') }}
                            </td>
                        </tr>
                    </table>
                </div>
                
                <p class="text-center text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.95rem; margin-bottom: 0.8%;">Telah Menyelesaikan :</p>
                
                <!-- Predikat -->
                <div class="text-center" style="margin-bottom: 6%;">
                    <p class="font-bold text-gray-900" style="font-family: Arial, sans-serif; font-size: 1.1rem; line-height: 1.6; margin-bottom: 0.2rem;">
                        Praktik Kerja Lapangan dengan "<span style="text-transform: uppercase;">{{ $certificate->predikat }}</span>"
                    </p>
                    <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.95rem;">di PT Perta Arun Gas Lhokseumawe</p>
                </div>
                
                <!-- Signature Area - Kanan Bawah -->
                <div class="absolute text-left" style="bottom: 12%; right: 20%;">
                    @php
                        $settings = \App\Models\CertificateSetting::first();
                    @endphp
                    <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem; line-height: 1.6; margin-bottom: 0.1rem;">{{ $settings->lokasi ?? 'Lhokseumawe' }}, {{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('d M Y') }}</p>
                    <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem; line-height: 1.6; margin-bottom: 0.1rem;">PT Perta Arun Gas</p>
                    <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem; line-height: 1.6; margin-bottom: 5rem;">{{ $settings->pjs_jabatan ?? 'Pjs. Manager HR Development' }}</p>
                    
                    <p class="font-semibold text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem;">{{ $settings->pjs_nama ?? 'Safril' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-blue-800 font-semibold mb-2">Informasi Sertifikat</h3>
                <ul class="text-blue-700 space-y-1 text-sm">
                    <li>• Sertifikat ini sudah di-approve dan tersedia untuk peserta</li>
                    <li>• Peserta dapat mengunduh sertifikat dari dashboard mereka</li>
                    <li>• Gunakan tombol Print untuk mencetak sertifikat dengan tanda tangan basah</li>
                    <li>• Approved by: {{ $certificate->approver->nama ?? 'System' }} pada {{ \Carbon\Carbon::parse($certificate->approved_at)->format('d M Y H:i') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 0;
    }
    
    body * {
        visibility: hidden;
    }
    
    #certificate-print, #certificate-print * {
        visibility: visible;
    }
    
    #certificate-print {
        position: fixed !important;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        margin: 0;
        padding: 0;
        box-shadow: none !important;
        border-radius: 0 !important;
    }
    
    #certificate-print > div {
        width: 100% !important;
        height: 100% !important;
        max-width: none !important;
    }
    
    button, .bg-gray-600, .bg-green-50, .bg-blue-50, h1, .text-gray-600 {
        display: none !important;
    }
}
</style>
@endsection