{{-- resources/views/peserta/materials/view.blade.php --}}
@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    {{-- Tombol Kembali --}}
    <div class="mb-6">
        <a href="{{ $backUrl ?? route('peserta.materials.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @isset($materi)
    {{-- Header Materi --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
        <div class="p-6 bg-gradient-to-r from-blue-500 to-blue-600 text-white">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-2">{{ $materi->judul }}</h1>
                    <div class="flex items-center text-blue-100">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                        <span class="font-medium">{{ $materi->room->nama_room ?? 'Room' }}</span>
                    </div>
                </div>
                <div class="ml-4">
                    <span class="bg-blue-400 text-white text-sm px-4 py-2 rounded-full font-medium">
                        {{ strtoupper($materi->tipe ?? 'File') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Info Materi --}}
        <div class="p-6 border-b">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Tanggal Upload</p>
                    <p class="font-semibold text-gray-800">{{ $materi->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Diupdate</p>
                    <p class="font-semibold text-gray-800">{{ $materi->updated_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        @if($materi->deskripsi)
            <div class="p-6 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi</h3>
                <p class="text-gray-700 leading-relaxed">{{ $materi->deskripsi }}</p>
            </div>
        @endif

        {{-- Konten (FIXED - render HTML dari CKEditor) --}}
        @if($materi->konten && $materi->tipe !== 'link')
            <div class="p-6 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Konten</h3>
                <div class="materi-content-peserta">
                    {!! $materi->konten !!}
                </div>
            </div>
        @endif

      {{-- Tombol Aksi --}}
    <div class="p-6 flex flex-wrap gap-3">
        @if($materi->file_path)
            {{-- Cek tipe file untuk tampilkan tombol yang sesuai --}}
            @php
                $extension = pathinfo($materi->file_path, PATHINFO_EXTENSION);
                $isPdf = in_array(strtolower($extension), ['pdf']);
                $isVideo = in_array(strtolower($extension), ['mp4', 'avi', 'mov']);
            @endphp

            @if($isPdf)
                <a href="{{ route('peserta.materials.view-pdf', $materi->materi_id) }}" target="_blank"
                class="flex-1 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-file-pdf mr-2"></i> Lihat PDF
                </a>
            @elseif($isVideo)
                <a href="{{ route('peserta.materials.stream', $materi->materi_id) }}" target="_blank"
                class="flex-1 inline-flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-play-circle mr-2"></i> Putar Video
                </a>
            @endif

            {{-- Tombol Download --}}
            <a href="{{ route('peserta.materials.download', $materi->materi_id) }}"
            class="flex-1 inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                <i class="fas fa-download mr-2"></i> Download
            </a>
        @else
            <div class="w-full text-center text-gray-500 py-4">
                <i class="fas fa-info-circle mr-2"></i>
                Materi ini tidak memiliki file lampiran
            </div>
        @endif
    </div>
    </div>

    {{-- Materi Lain di Room yang Sama --}}
    @if($materi->room && $materi->room->materis->where('materi_id', '!=', $materi->materi_id)->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Materi Lain dari {{ $materi->room->nama_room ?? 'Room ini' }}
            </h2>
            <div class="space-y-3">
                @foreach($materi->room->materis->where('materi_id', '!=', $materi->materi_id)->take(5) as $otherMateri)
                    <a href="{{ route('peserta.materials.view', $otherMateri->materi_id) }}"
                       class="block p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $otherMateri->judul }}</h3>
                                <p class="text-sm text-gray-500">{{ $otherMateri->created_at->format('d M Y') }}</p>
                            </div>
                            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded ml-3">
                                {{ strtoupper($otherMateri->tipe ?? 'File') }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @else
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <i class="fas fa-exclamation-circle text-6xl text-gray-400 mb-4"></i>
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Materi tidak ditemukan</h2>
            <p class="text-gray-500 mb-4">Materi yang Anda cari tidak tersedia atau telah dihapus.</p>
            <a href="{{ route('peserta.materials') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Daftar Materi
            </a>
        </div>
    @endisset
</div>
@endsection

@push('styles')
<style>
    /* Styling untuk konten materi dari CKEditor di halaman peserta */
    .materi-content-peserta {
        line-height: 1.8;
        font-size: 16px;
        color: #333;
        word-wrap: break-word;
    }
    
    /* Heading Styles */
    .materi-content-peserta h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-top: 1.5em;
        margin-bottom: 0.75em;
        color: #2c3e50;
        border-bottom: 3px solid #4e73df;
        padding-bottom: 10px;
    }
    
    .materi-content-peserta h2 {
        font-size: 2rem;
        font-weight: bold;
        margin-top: 1.5em;
        margin-bottom: 0.75em;
        color: #34495e;
        border-bottom: 2px solid #858796;
        padding-bottom: 8px;
    }
    
    .materi-content-peserta h3 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        color: #34495e;
    }
    
    .materi-content-peserta h4 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 1.25em;
        margin-bottom: 0.5em;
        color: #5a6c7d;
    }
    
    /* Paragraph */
    .materi-content-peserta p {
        margin-bottom: 1.25em;
        text-align: justify;
    }
    
    /* Lists */
    .materi-content-peserta ul,
    .materi-content-peserta ol {
        margin-left: 30px;
        margin-bottom: 1.5em;
        padding-left: 10px;
    }
    
    .materi-content-peserta ul li,
    .materi-content-peserta ol li {
        margin-bottom: 0.5em;
        line-height: 1.8;
    }
    
    .materi-content-peserta ul {
        list-style-type: disc;
    }
    
    .materi-content-peserta ol {
        list-style-type: decimal;
    }
    
    /* Links */
    .materi-content-peserta a {
        color: #4e73df;
        text-decoration: underline;
    }
    
    .materi-content-peserta a:hover {
        color: #2e59d9;
        text-decoration: none;
    }
    
    /* Blockquote */
    .materi-content-peserta blockquote {
        border-left: 4px solid #4e73df;
        background-color: #f8f9fc;
        padding: 15px 20px;
        margin: 1.5em 0;
        font-style: italic;
        color: #5a5c69;
    }
    
    /* Table */
    .materi-content-peserta table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5em 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .materi-content-peserta table thead {
        background-color: #4e73df;
        color: white;
    }
    
    .materi-content-peserta table th,
    .materi-content-peserta table td {
        border: 1px solid #e3e6f0;
        padding: 12px 15px;
        text-align: left;
    }
    
    .materi-content-peserta table tbody tr:nth-child(even) {
        background-color: #f8f9fc;
    }
    
    .materi-content-peserta table tbody tr:hover {
        background-color: #eaecf4;
    }
    
    /* Code Inline */
    .materi-content-peserta code {
        background-color: #f4f4f4;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 2px 6px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: #c7254e;
    }
    
    /* Code Block */
    .materi-content-peserta pre {
        background-color: #2d2d2d;
        color: #f8f8f2;
        padding: 20px;
        border-radius: 6px;
        overflow-x: auto;
        margin: 1.5em 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .materi-content-peserta pre code {
        background-color: transparent;
        border: none;
        color: #f8f8f2;
        padding: 0;
    }
    
    /* Strong & Emphasis */
    .materi-content-peserta strong {
        font-weight: bold;
        color: #2c3e50;
    }
    
    .materi-content-peserta em {
        font-style: italic;
    }
    
    /* Images */
    .materi-content-peserta img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1.5em auto;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    /* Horizontal Rule */
    .materi-content-peserta hr {
        border: none;
        border-top: 2px solid #e3e6f0;
        margin: 2em 0;
    }
</style>
@endpush