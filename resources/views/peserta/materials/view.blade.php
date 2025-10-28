{{-- resources/views/peserta/materials/view.blade.php --}}
@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    {{-- Tombol Kembali --}}
    <div class="mb-6">
        <a href="{{ route('peserta.materials') }}" 
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow transition-all duration-200">
            <i class="fas fa-arrow-left"></i>
            Kembali
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

        {{-- Konten (tambahan dari File 2) --}}
        @if($materi->konten && $materi->tipe !== 'link')
            <div class="p-6 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Konten</h3>
                <div class="text-gray-700 leading-relaxed prose max-w-none">
                    {!! nl2br(e($materi->konten)) !!}
                </div>
            </div>
        @endif

        {{-- Tombol Aksi --}}
        <div class="p-6 flex flex-wrap gap-3">
            @if($materi->tipe === 'pdf')
                <a href="{{ route('peserta.materials.view-pdf', $materi->materi_id) }}" target="_blank"
                   class="flex-1 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-file-pdf mr-2"></i> Lihat PDF
                </a>
            @elseif($materi->tipe === 'video')
                <a href="{{ route('peserta.materials.stream', $materi->materi_id) }}" target="_blank"
                   class="flex-1 inline-flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-play-circle mr-2"></i> Putar Video
                </a>
            @elseif($materi->tipe === 'link')
                <a href="{{ $materi->konten }}" target="_blank"
                   class="flex-1 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-external-link-alt mr-2"></i> Buka Link
                </a>
            @endif

            @if($materi->tipe !== 'link')
                <a href="{{ route('peserta.materials.download', $materi->materi_id) }}"
                   class="flex-1 inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    <i class="fas fa-download mr-2"></i> Download
                </a>
            @endif
        </div>
    </div>

    {{-- Materi Lain di Room yang Sama --}}
    {{-- Ganti 'materi' dengan 'materis' sesuai nama relasi di model Room --}}
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