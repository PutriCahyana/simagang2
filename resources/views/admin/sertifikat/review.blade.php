@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Review & Approve Sertifikat</h1>
            <p class="text-gray-600">Review sertifikat draft sebelum approve</p>
        </div>
        <a href="{{ route('admin.sertifikat.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    @if($certificates->isEmpty())
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-yellow-700 font-medium">Tidak ada sertifikat yang dipilih. Silakan kembali dan pilih sertifikat terlebih dahulu.</p>
        </div>
    </div>
    @else
    <form action="{{ route('admin.sertifikat.approve') }}" method="POST" id="approveForm">
        @csrf

        <!-- Summary Card -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm text-green-600 font-medium">Siap untuk Approve</p>
                        <p class="text-2xl font-bold text-green-900">{{ $certificates->count() }} Sertifikat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificates Preview -->
        <div class="space-y-6 mb-6">
            @foreach($certificates as $cert)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Certificate Preview -->
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
                            <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem;">No. {{ $cert->nomor_surat }}</p>
                        </div>
                        
                        <p class="text-center text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.95rem; margin-bottom: 3%;">Dengan ini menerangkan bahwa :</p>
                        
                        <!-- Data Peserta -->
                        <div style="margin-bottom: 3.5%; padding-left: 8%; padding-right: 8%;">
                            <table style="width: 100%; font-family: Arial, sans-serif; font-size: 0.95rem; line-height: 1.9;">
                                <tr>
                                    <td style="width: 150px; padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Nama</td>
                                    <td style="width: 30px; padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                                    <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">{{ $cert->user->nama }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">NIM</td>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                                    <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">{{ $cert->user->peserta->nim ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Universitas</td>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                                    <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">{{ $cert->user->peserta->institut ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Fungsi</td>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                                    <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937; text-transform: capitalize;">{{ $cert->user->joinedRooms->pluck('nama_room')->first() ?? 'Technical' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">Masa PKL</td>
                                    <td style="padding-bottom: 0.4rem; vertical-align: top; color: #1f2937;">:</td>
                                    <td style="padding-bottom: 0.4rem; font-weight: 400; color: #1f2937;">
                                        {{ \Carbon\Carbon::parse($cert->user->peserta->periode_start)->format('d M Y') }} s.d 
                                        {{ \Carbon\Carbon::parse($cert->user->peserta->periode_end)->format('d M Y') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <p class="text-center text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.95rem; margin-bottom: 0.8%;">Telah Menyelesaikan :</p>
                        
                        <!-- Predikat -->
                        <div class="text-center" style="margin-bottom: 6%;">
                            <p class="font-bold text-gray-900" style="font-family: Arial, sans-serif; font-size: 1.1rem; line-height: 1.6; margin-bottom: 0.2rem;">
                                Praktik Kerja Lapangan dengan "<span style="text-transform: uppercase;">{{ $cert->predikat }}</span>"
                            </p>
                            <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.95rem;">di PT Perta Arun Gas Lhokseumawe</p>
                        </div>
                        
                        <!-- Signature Area - Kanan Bawah -->
                        <div class="absolute text-left" style="bottom: 12%; right: 20%;">
                            <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem; line-height: 1.6; margin-bottom: 0.1rem;">{{ $settings->lokasi ?? 'Lhokseumawe' }}, {{ \Carbon\Carbon::parse($cert->tanggal_terbit)->format('d M Y') }}</p>
                            <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem; line-height: 1.6; margin-bottom: 0.1rem;">PT Perta Arun Gas</p>
                            <p class="text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem; line-height: 1.6; margin-bottom: 5rem;">{{ $settings->pjs_jabatan ?? 'Pjs. Manager HR Development' }}</p>
                            
                            <p class="font-semibold text-gray-900" style="font-family: Arial, sans-serif; font-size: 0.875rem;">{{ $settings->pjs_nama ?? 'Safril' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Checkbox Approve -->
                <div class="p-4 bg-gray-50 border-t flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="certificate_ids[]" value="{{ $cert->id }}" 
                               class="rounded border-gray-300 text-green-600 focus:ring-green-500 w-5 h-5 mr-3" checked>
                        <div>
                            <span class="text-sm font-semibold text-gray-900">{{ $cert->user->nama }}</span>
                            <span class="text-sm text-gray-600 ml-2">({{ $cert->nomor_surat }})</span>
                        </div>
                    </label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $cert->predikat == 'SANGAT BAIK' ? 'bg-green-100 text-green-800' : ($cert->predikat == 'BAIK' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ $cert->predikat }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Warning Box -->
        <div class="bg-amber-50 border-l-4 border-amber-500 p-6 rounded-lg mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-amber-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-amber-800 font-semibold mb-2">Perhatian</h3>
                    <ul class="text-amber-700 space-y-1 text-sm">
                        <li>• Setelah di-approve, sertifikat akan tersimpan dan dapat dilihat oleh peserta</li>
                        <li>• Sertifikat yang sudah di-approve tidak dapat dihapus</li>
                        <li>• Pastikan semua data sudah benar sebelum approve</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.sertifikat.index') }}" 
               class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200 font-medium">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Approve Sertifikat
            </button>
        </div>
    </form>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('approveForm')?.addEventListener('submit', function(e) {
    const checked = this.querySelectorAll('input[name="certificate_ids[]"]:checked');
    
    if (checked.length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 sertifikat untuk di-approve');
        return;
    }
    
    if (!confirm(`Approve ${checked.length} sertifikat?\n\nSetelah di-approve, sertifikat tidak dapat diubah atau dihapus.`)) {
        e.preventDefault();
        return;
    }
    
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Approving...';
});
</script>
@endpush
@endsection