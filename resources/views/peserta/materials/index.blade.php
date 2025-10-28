{{-- resources/views/peserta/materials/index.blade.php --}}
@extends('layout.app')

@section('konten')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-purple-50/20 py-8">
    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-2">
                Materi Pembelajaran
            </h1>
            <p class="text-gray-600">Akses materi dari room yang Anda ikuti</p>
        </div>

        {{-- Materi General --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2 rounded-lg shadow-sm">
                            <i class="fas fa-globe text-white text-sm"></i>
                        </div>
                        Materi General
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Dapat diakses oleh semua peserta</p>
                </div>
                <span class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm">
                    {{ $generalMaterials->count() }} Materi
                </span>
            </div>

            @if($generalMaterials->isEmpty())
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm p-8 text-center border border-gray-100">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada materi general tersedia</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                    @foreach($generalMaterials as $materi)
                        <a href="{{ route('peserta.materials.view', $materi->materi_id) }}" 
                           class="group bg-white/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-200/50 hover:border-transparent hover:shadow-xl hover:shadow-blue-100/50 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden">
                            
                            {{-- Gradient Overlay on Hover --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <div class="relative z-10">
                                {{-- Icon & Badge --}}
                                <div class="flex items-start justify-between mb-3">
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 group-hover:from-blue-100 group-hover:to-indigo-100 p-3 rounded-xl transition-all duration-300 shadow-sm">
                                        @if($materi->tipe === 'video')
                                            <i class="fas fa-play-circle bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent text-2xl"></i>
                                        @elseif($materi->tipe === 'link')
                                            <i class="fas fa-link bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent text-2xl"></i>
                                        @else
                                            <i class="fas fa-file-pdf bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent text-2xl"></i>
                                        @endif
                                    </div>
                                    <span class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                                        {{ strtoupper($materi->tipe ?? 'FILE') }}
                                    </span>
                                </div>

                                {{-- Content --}}
                                <h3 class="font-bold text-base text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                    {{ $materi->judul }}
                                </h3>
                                
                                @if($materi->deskripsi)
                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                                        {{ $materi->deskripsi }}
                                    </p>
                                @endif

                                {{-- Footer --}}
                                <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $materi->created_at->format('d M Y') }}
                                    </span>
                                    <i class="fas fa-arrow-right text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Materi Room Saya --}}
        <div>
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-2 rounded-lg shadow-sm">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                    Materi Room Saya
                </h2>
                <p class="text-sm text-gray-500 mt-1">Dari room yang sudah Anda ikuti</p>
            </div>

            @if($joinedRooms->isEmpty())
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm p-10 text-center border border-gray-100">
                    <i class="fas fa-user-plus text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">Anda belum bergabung dengan room manapun</p>
                    <a href="{{ route('peserta.rooms') }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-medium px-6 py-2.5 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fas fa-plus-circle"></i>
                        Gabung Room
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($joinedRooms as $room)
                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/50 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            {{-- Room Header --}}
                            <div class="bg-gradient-to-r from-emerald-50 via-teal-50/50 to-cyan-50/30 px-6 py-4 border-b border-gray-200/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-2.5 rounded-xl shadow-sm">
                                            <i class="fas fa-door-open text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">
                                                {{ $room->nama_room }}
                                            </h3>
                                            @if($room->deskripsi)
                                                <p class="text-sm text-gray-600">{{ $room->deskripsi }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm">
                                        {{ $room->materis->count() }} Materi
                                    </span>
                                </div>
                            </div>

                            {{-- Room Materials --}}
                            <div class="p-6">
                                @if($room->materis->isEmpty())
                                    <div class="text-center py-8 bg-gray-50/50 rounded-xl">
                                        <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                                        <p class="text-gray-500 text-sm">Belum ada materi di room ini</p>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                        @foreach($room->materis as $materi)
                                            <a href="{{ route('peserta.materials.view', $materi->materi_id) }}" 
                                               class="group bg-gradient-to-br from-gray-50 to-gray-50/50 hover:from-white hover:to-white rounded-2xl p-5 border border-gray-200/50 hover:border-transparent hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden">
                                                
                                                {{-- Gradient Overlay on Hover --}}
                                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                
                                                <div class="relative z-10">
                                                    {{-- Icon & Badge --}}
                                                    <div class="flex items-start justify-between mb-3">
                                                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 group-hover:from-emerald-100 group-hover:to-teal-100 p-3 rounded-xl transition-all duration-300 shadow-sm">
                                                            @if($materi->tipe === 'video')
                                                                <i class="fas fa-play-circle bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent text-2xl"></i>
                                                            @elseif($materi->tipe === 'link')
                                                                <i class="fas fa-link bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent text-2xl"></i>
                                                            @else
                                                                <i class="fas fa-file-pdf bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent text-2xl"></i>
                                                            @endif
                                                        </div>
                                                        <span class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                                                            {{ strtoupper($materi->tipe ?? 'FILE') }}
                                                        </span>
                                                    </div>

                                                    {{-- Content --}}
                                                    <h4 class="font-bold text-base text-gray-800 mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors">
                                                        {{ $materi->judul }}
                                                    </h4>

                                                    @if($materi->deskripsi)
                                                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                                                            {{ $materi->deskripsi }}
                                                        </p>
                                                    @endif

                                                    {{-- Footer --}}
                                                    <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
                                                        <span class="flex items-center gap-1">
                                                            <i class="fas fa-clock"></i>
                                                            {{ $materi->created_at->diffForHumans() }}
                                                        </span>
                                                        <i class="fas fa-arrow-right text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Smooth gradient animation */
    @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
</style>
@endpush

@endsection