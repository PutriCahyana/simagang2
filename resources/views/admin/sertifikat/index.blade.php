@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Kelola Sertifikat</h1>
            <p class="text-gray-600">Generate dan kelola sertifikat peserta magang</p>
        </div>
        <a href="{{ route('admin.sertifikat.settings') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings Sertifikat
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Belum Generate</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $belumGenerate }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Draft</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $draft }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Approved</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $approved }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="switchTab('belum')" id="tab-belum" 
                        class="tab-button active py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                    Belum Generate ({{ $belumGenerate }})
                </button>
                <button onclick="switchTab('draft')" id="tab-draft" 
                        class="tab-button py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                    Draft ({{ $draft }})
                </button>
                <button onclick="switchTab('approved')" id="tab-approved" 
                        class="tab-button py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                    Approved ({{ $approved }})
                </button>
            </nav>
        </div>

        <!-- Filter -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Cari nama peserta..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:w-48">
                    <select id="filterPeriode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Periode</option>
                        @foreach($periodeList as $periode)
                            <option value="{{ $periode }}">{{ $periode }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:w-48">
                    <select id="filterFungsi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Fungsi</option>
                        @foreach($fungsiList as $fungsi)
                            <option value="{{ $fungsi->nama_room }}">{{ $fungsi->nama_room }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tab Content: Belum Generate -->
        <div id="content-belum" class="tab-content">
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-600">
                        <span id="selected-count-belum">0</span> peserta dipilih
                    </div>
                    <button onclick="generateSelected()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="btn-generate-belum" disabled>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Generate Sertifikat
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="checkAll-belum" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fungsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tbody-belum">
                            @forelse($pesertaBelum as $index => $peserta)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 data-row" 
                                data-nama="{{ strtolower($peserta->nama) }}"
                                data-periode="{{ $peserta->peserta->periode_label ?? '' }}"
                                data-fungsi="{{ $peserta->joinedRooms->pluck('nama_room')->implode(',') }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                           data-id="{{ $peserta->id }}" data-tab="belum">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 row-number">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($peserta->nama, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $peserta->nama }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $peserta->peserta->nim ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($peserta->joinedRooms as $room)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $room->nama_room }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $peserta->peserta->periode_label ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-gray-500 text-lg font-medium">Semua peserta sudah memiliki sertifikat</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab Content: Draft -->
        <div id="content-draft" class="tab-content hidden">
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-600">
                        <span id="selected-count-draft">0</span> sertifikat dipilih
                    </div>
                    <div class="flex gap-2">
                        <button onclick="reviewSelected()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="btn-review-draft" disabled>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Review & Approve
                        </button>
                        <button onclick="deleteSelected('draft')" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="btn-delete-draft" disabled>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="checkAll-draft" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fungsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tbody-draft">
                            @forelse($pesertaDraft as $index => $cert)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 data-row" 
                                data-nama="{{ strtolower($cert->user->nama) }}"
                                data-periode="{{ $cert->user->peserta->periode_label ?? '' }}"
                                data-fungsi="{{ $cert->user->joinedRooms->pluck('nama_room')->implode(',') }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                           data-id="{{ $cert->id }}" data-tab="draft">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 row-number">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($cert->user->nama, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $cert->user->nama }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $cert->user->peserta->nim ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($cert->user->joinedRooms as $room)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $room->nama_room }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $cert->user->peserta->periode_label ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($cert->created_at)->format('d M Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-gray-500 text-lg font-medium">Belum ada sertifikat draft</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab Content: Approved -->
        <div id="content-approved" class="tab-content hidden">
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fungsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Approve</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tbody-approved">
                            @forelse($pesertaApproved as $index => $cert)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 data-row" 
                                data-nama="{{ strtolower($cert->user->nama) }}"
                                data-periode="{{ $cert->user->peserta->periode_label ?? '' }}"
                                data-fungsi="{{ $cert->user->joinedRooms->pluck('nama_room')->implode(',') }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 row-number">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($cert->user->nama, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $cert->user->nama }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $cert->user->peserta->nim ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($cert->user->joinedRooms as $room)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $room->nama_room }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $cert->user->peserta->periode_label ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($cert->approved_at)->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.sertifikat.preview', $cert->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-gray-500 text-lg font-medium">Belum ada sertifikat yang approved</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="hidden bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <p class="text-gray-500 text-lg font-medium">Tidak ada hasil ditemukan</p>
        <p class="text-gray-400 text-sm mt-1">Coba ubah kata kunci pencarian atau filter</p>
    </div>
</div>

@push('scripts')
<script>
// Tab Management
let currentTab = 'belum';

function switchTab(tabName) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    // Show selected content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active to selected button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    
    currentTab = tabName;
    
    // Reset filters and checkboxes
    updateSelectedCount(tabName);
    filterTable();
}

// Checkbox Management
document.addEventListener('DOMContentLoaded', function() {
    // Check All functionality
    ['belum', 'draft'].forEach(tab => {
        const checkAll = document.getElementById('checkAll-' + tab);
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll(`#tbody-${tab} .row-checkbox:not(.hidden)`);
                checkboxes.forEach(cb => {
                    if (!cb.closest('tr').classList.contains('hidden')) {
                        cb.checked = this.checked;
                    }
                });
                updateSelectedCount(tab);
            });
        }
    });
    
    // Individual checkbox change
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const tab = this.dataset.tab;
            updateSelectedCount(tab);
            
            // Update checkAll state
            const allCheckboxes = document.querySelectorAll(`#tbody-${tab} .row-checkbox:not(.hidden)`);
            const visibleCheckboxes = Array.from(allCheckboxes).filter(cb => !cb.closest('tr').classList.contains('hidden'));
            const checkedCheckboxes = visibleCheckboxes.filter(cb => cb.checked);
            const checkAll = document.getElementById('checkAll-' + tab);
            
            if (checkAll) {
                checkAll.checked = visibleCheckboxes.length > 0 && checkedCheckboxes.length === visibleCheckboxes.length;
                checkAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < visibleCheckboxes.length;
            }
        });
    });
    
    // Filter functionality
    const searchInput = document.getElementById('searchInput');
    const filterPeriode = document.getElementById('filterPeriode');
    const filterFungsi = document.getElementById('filterFungsi');
    
    searchInput.addEventListener('input', filterTable);
    filterPeriode.addEventListener('change', filterTable);
    filterFungsi.addEventListener('change', filterTable);
});

function updateSelectedCount(tab) {
    const checkboxes = document.querySelectorAll(`#tbody-${tab} .row-checkbox:checked`);
    const count = checkboxes.length;
    const countElement = document.getElementById('selected-count-' + tab);
    
    if (countElement) {
        countElement.textContent = count;
    }
    
    // Enable/disable buttons based on selection
    if (tab === 'belum') {
        const btnGenerate = document.getElementById('btn-generate-belum');
        if (btnGenerate) {
            btnGenerate.disabled = count === 0;
        }
    } else if (tab === 'draft') {
        const btnReview = document.getElementById('btn-review-draft');
        const btnDelete = document.getElementById('btn-delete-draft');
        if (btnReview) btnReview.disabled = count === 0;
        if (btnDelete) btnDelete.disabled = count === 0;
    }
}

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const periodeFilter = document.getElementById('filterPeriode').value;
    const fungsiFilter = document.getElementById('filterFungsi').value.toLowerCase();
    
    // Filter all tabs
    ['belum', 'draft', 'approved'].forEach(tab => {
        const tbody = document.getElementById('tbody-' + tab);
        if (!tbody) return;
        
        const rows = tbody.querySelectorAll('.data-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const nama = row.dataset.nama || '';
            const periode = row.dataset.periode || '';
            const fungsi = (row.dataset.fungsi || '').toLowerCase();
            
            const matchesSearch = nama.includes(searchTerm);
            const matchesPeriode = !periodeFilter || periode === periodeFilter;
            const matchesFungsi = !fungsiFilter || fungsi.includes(fungsiFilter);
            
            if (matchesSearch && matchesPeriode && matchesFungsi) {
                row.classList.remove('hidden');
                visibleCount++;
                const numberCell = row.querySelector('.row-number');
                if (numberCell) {
                    numberCell.textContent = visibleCount;
                }
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Show/hide no results message for current tab
        if (tab === currentTab) {
            const noResults = document.getElementById('noResults');
            const hasData = rows.length > 0;
            
            if (hasData && visibleCount === 0) {
                tbody.parentElement.parentElement.classList.add('hidden');
                noResults.classList.remove('hidden');
            } else {
                tbody.parentElement.parentElement.classList.remove('hidden');
                noResults.classList.add('hidden');
            }
        }
    });
    
    // Update checkAll after filtering
    ['belum', 'draft'].forEach(tab => {
        const checkAll = document.getElementById('checkAll-' + tab);
        if (checkAll) {
            checkAll.checked = false;
            checkAll.indeterminate = false;
        }
    });
    
    // Update counts
    ['belum', 'draft'].forEach(tab => {
        updateSelectedCount(tab);
    });
}

// Action Functions
function generateSelected() {
    const selected = Array.from(document.querySelectorAll('#tbody-belum .row-checkbox:checked'))
        .map(cb => cb.dataset.id);
    
    if (selected.length === 0) {
        alert('Pilih minimal 1 peserta untuk generate sertifikat');
        return;
    }
    
    // Redirect ke halaman generate dengan selected IDs
    const params = new URLSearchParams();
    selected.forEach(id => params.append('ids[]', id));
    window.location.href = `{{ route('admin.sertifikat.generate') }}?${params.toString()}`;
}

function reviewSelected() {
    const selected = Array.from(document.querySelectorAll('#tbody-draft .row-checkbox:checked'))
        .map(cb => cb.dataset.id);
    
    if (selected.length === 0) {
        alert('Pilih minimal 1 sertifikat untuk direview');
        return;
    }
    
    // Redirect ke halaman review dengan selected IDs
    const params = new URLSearchParams();
    selected.forEach(id => params.append('ids[]', id));
    window.location.href = `{{ route('admin.sertifikat.review') }}?${params.toString()}`;
}

function deleteSelected(tab) {
    const selected = Array.from(document.querySelectorAll(`#tbody-${tab} .row-checkbox:checked`))
        .map(cb => cb.dataset.id);
    
    if (selected.length === 0) {
        alert('Pilih minimal 1 sertifikat untuk dihapus');
        return;
    }
    
    if (confirm(`Apakah Anda yakin ingin menghapus ${selected.length} sertifikat draft?\n\nTindakan ini tidak dapat dibatalkan.`)) {
        // Submit form untuk delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.sertifikat.delete-bulk') }}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection