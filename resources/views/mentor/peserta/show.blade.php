@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('mentor.peserta.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Peserta
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-8 mb-8 text-white">
        <div class="flex items-start justify-between">
            <div class="flex items-center">
                <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center text-blue-600 font-bold text-3xl shadow-lg">
                    {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                </div>
                <div class="ml-6">
                    <h1 class="text-3xl font-bold mb-2">{{ $peserta->nama }}</h1>
                    <p class="text-blue-100 text-lg">{{ '@' . $peserta->username }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500 bg-opacity-50">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                            </svg>
                            Peserta
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Umum -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Umum
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $peserta->nama }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Username</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $peserta->username }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Institut/Universitas</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $peserta->peserta->institut ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($peserta->role) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Periode Magang -->
            @if($peserta->peserta)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Periode Magang
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($peserta->peserta->periode_start)->format('d F Y') }}
                            </p>
                        </div>
                        <div class="px-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($peserta->peserta->periode_end)->format('d F Y') }}
                            </p>
                        </div>
                    </div>
                    
                    @php
                        $start = \Carbon\Carbon::parse($peserta->peserta->periode_start);
                        $end = \Carbon\Carbon::parse($peserta->peserta->periode_end);
                        $now = \Carbon\Carbon::now();
                        
                        $totalDays = round($start->diffInDays($end));
                        $daysElapsed = round($start->diffInDays($now));
                        $daysRemaining = round($now->diffInDays($end));
                        
                        if ($now->lt($start)) {
                            $progress = 0;
                            $status = 'Belum Dimulai';
                            $statusColor = 'bg-gray-100 text-gray-800';
                        } elseif ($now->gt($end)) {
                            $progress = 100;
                            $status = 'Selesai';
                            $statusColor = 'bg-green-100 text-green-800';
                        } else {
                            $progress = ($daysElapsed / $totalDays) * 100;
                            $status = 'Sedang Berlangsung';
                            $statusColor = 'bg-blue-100 text-blue-800';
                        }
                    @endphp
                    
                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress Periode</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ $status }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="mt-2 flex justify-between text-sm text-gray-600">
                            <span>{{ round($progress, 1) }}% selesai</span>
                            @if($now->between($start, $end))
                                <span>{{ $daysRemaining }} hari tersisa</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Room Info -->
        <div class="space-y-6">
            <!-- Room yang Diikuti -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Room yang Diikuti
                    </h2>
                </div>
                <div class="p-6">
                    @if($peserta->joinedRooms->count() > 0)
                        <div class="space-y-3">
                            @foreach($peserta->joinedRooms as $room)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">{{ $room->nama_room }}</h3>
                                        @if($room->deskripsi)
                                            <p class="text-sm text-gray-600 line-clamp-2">{{ $room->deskripsi }}</p>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Total {{ $peserta->joinedRooms->count() }} room
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <p class="text-gray-500">Belum bergabung di room manapun</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Room</span>
                        <span class="text-lg font-bold text-blue-600">{{ $peserta->joinedRooms->count() }}</span>
                    </div>
                    @if($peserta->peserta)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Durasi Magang</span>
                        <span class="text-lg font-bold text-blue-600">
                            {{ \Carbon\Carbon::parse($peserta->peserta->periode_start)->diffInDays(\Carbon\Carbon::parse($peserta->peserta->periode_end)) }} hari
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection