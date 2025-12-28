@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Settings Sertifikat</h1>
            <p class="text-gray-600">Konfigurasi format dan template sertifikat</p>
        </div>
        <a href="{{ route('admin.sertifikat.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-red-700 font-medium mb-2">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.sertifikat.settings.update') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Format Nomor Surat -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                    Format Nomor Surat
                </h2>

                <div class="space-y-4">
                    <!-- Counter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Counter Saat Ini
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="nomor_counter" 
                               value="{{ old('nomor_counter', $settings->nomor_counter) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Nomor ini akan otomatis bertambah setiap kali generate sertifikat</p>
                    </div>

                    <!-- Format Tetap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Format Tetap (Bagian Tengah)
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="format_tetap" 
                               value="{{ old('format_tetap', $settings->format_tetap) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="PAG1300"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Bagian ini tidak akan berubah</p>
                    </div>

                    <!-- Suffix -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Suffix (Bagian Akhir)
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="suffix" 
                               value="{{ old('suffix', $settings->suffix) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="S0"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Bagian ini juga tidak akan berubah</p>
                    </div>

                    <!-- Preview -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Preview Nomor Surat:</p>
                        <div class="bg-white rounded px-4 py-3 border-2 border-blue-300">
                            <p class="font-mono text-lg text-gray-800 text-center" id="preview-nomor">
                                {{ $settings->nomor_counter }}/{{ $settings->format_tetap }}/{{ now()->year }}-{{ $settings->suffix }}
                            </p>
                        </div>
                        <p class="mt-2 text-xs text-gray-600 text-center">
                            Nomor berikutnya: 
                            <span class="font-semibold">{{ $settings->nomor_counter + 1 }}/{{ $settings->format_tetap }}/{{ now()->year }}-{{ $settings->suffix }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Data Penandatangan -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Data Penandatangan
                </h2>

                <div class="space-y-4">
                    <!-- Nama PJS -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Penandatangan
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="pjs_nama" 
                               value="{{ old('pjs_nama', $settings->pjs_nama) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Safril"
                               required>
                    </div>

                    <!-- Jabatan PJS -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jabatan
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="pjs_jabatan" 
                               value="{{ old('pjs_jabatan', $settings->pjs_jabatan) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Pjs. Manager HR Development"
                               required>
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="lokasi" 
                               value="{{ old('lokasi', $settings->lokasi) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Lhokseumawe"
                               required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end gap-4">
            <a href="{{ route('admin.sertifikat.index') }}" 
               class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200 font-medium">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-8 bg-amber-50 border-l-4 border-amber-500 p-6 rounded-lg">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-amber-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-amber-800 font-semibold mb-2">Informasi Penting</h3>
                <ul class="text-amber-700 space-y-1 text-sm">
                    <li>• Nomor counter akan otomatis bertambah setiap kali generate sertifikat</li>
                    <li>• Tahun akan otomatis mengikuti tahun sistem saat generate</li>
                    <li>• Format: <strong>[Counter]/[Format Tetap]/[Tahun]-[Suffix]</strong></li>
                    <li>• Contoh: <strong>203/PAG1300/2024-S0</strong></li>
                    <li>• Sertifikat menggunakan tanda tangan basah (manual)</li>
                    <li>• Template bingkai: <code class="bg-white px-2 py-0.5 rounded">public/assets/certificates/template-bc.png</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Live preview nomor surat
function updatePreview() {
    const counter = document.querySelector('input[name="nomor_counter"]').value || '203';
    const format = document.querySelector('input[name="format_tetap"]').value || 'PAG1300';
    const suffix = document.querySelector('input[name="suffix"]').value || 'S0';
    const year = new Date().getFullYear();
    
    const preview = `${counter}/${format}/${year}-${suffix}`;
    document.getElementById('preview-nomor').textContent = preview;
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['nomor_counter', 'format_tetap', 'suffix'];
    inputs.forEach(name => {
        const input = document.querySelector(`input[name="${name}"]`);
        if (input) {
            input.addEventListener('input', updatePreview);
        }
    });
});
</script>
@endpush
@endsection