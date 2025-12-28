@extends('layout.app')

@section('konten')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Semua Mentor</h1>
        <p class="text-gray-600">Kelola dan pantau semua mentor dari seluruh room</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Mentor</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $mentor->count() }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Mentor Aktif</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $mentor->filter(fn($m) => $m->mentor && $m->mentor->rooms->count() > 0)->count() }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="searchInput" placeholder="Cari nama atau username mentor..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="md:w-64">
                <select id="filterRoom" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">Semua Room</option>
                    @php
                        $allRooms = collect();
                        foreach($mentor as $m) {
                            if($m->mentor && $m->mentor->rooms) {
                                $allRooms = $allRooms->merge($m->mentor->rooms);
                            }
                        }
                        $allRooms = $allRooms->unique('id')->sortBy('nama_room');
                    @endphp
                    @foreach($allRooms as $room)
                        <option value="{{ $room->nama_room }}">{{ $room->nama_room }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Handphone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="mentorTableBody">
                    @forelse($mentor as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 mentor-row" 
                        data-nama="{{ strtolower($item->nama) }}" 
                        data-username="{{ strtolower($item->username) }}"
                        data-rooms="{{ $item->mentor && $item->mentor->rooms ? $item->mentor->rooms->pluck('nama_room')->implode(',') : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item->username }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->mentor->handphone ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @if($item->mentor && $item->mentor->rooms && $item->mentor->rooms->count() > 0)
                                    @foreach($item->mentor->rooms as $room)
                                        <a href="#" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors duration-200">
                                            {{ $room->nama_room }}
                                        </a>
                                    @endforeach
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="width: 200px;">
                            <div class="flex items-center gap-2">
                                <a href="#" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                                
                                <button onclick="confirmDelete({{ $item->id }}, '{{ $item->nama }}')" 
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                            
                            <!-- Form Hapus (Hidden) -->
                            <form id="delete-form-{{ $item->id }}" 
                                  action="{{ route('admin.mentor.destroy', $item->id) }}" 
                                  method="POST" 
                                  class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Belum ada mentor</p>
                                <p class="text-gray-400 text-sm mt-1">Mentor akan muncul di sini setelah terdaftar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="hidden bg-white rounded-lg shadow-md p-12 text-center mt-6">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <p class="text-gray-500 text-lg font-medium">Tidak ada hasil ditemukan</p>
        <p class="text-gray-400 text-sm mt-1">Coba ubah kata kunci pencarian atau filter Anda</p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterRoom = document.getElementById('filterRoom');
    const rows = document.querySelectorAll('.mentor-row');
    const noResults = document.getElementById('noResults');
    const tableBody = document.getElementById('mentorTableBody');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const roomFilter = filterRoom.value.toLowerCase();
        let visibleCount = 0;

        rows.forEach((row, index) => {
            const nama = row.dataset.nama;
            const username = row.dataset.username;
            const rooms = row.dataset.rooms.toLowerCase();

            const matchesSearch = nama.includes(searchTerm) || username.includes(searchTerm);
            const matchesRoom = !roomFilter || rooms.includes(roomFilter);

            if (matchesSearch && matchesRoom) {
                row.classList.remove('hidden');
                visibleCount++;
                row.querySelector('td:first-child').textContent = visibleCount;
            } else {
                row.classList.add('hidden');
            }
        });

        if (visibleCount === 0 && rows.length > 0) {
            tableBody.classList.add('hidden');
            noResults.classList.remove('hidden');
        } else {
            tableBody.classList.remove('hidden');
            noResults.classList.add('hidden');
        }
    }

    searchInput.addEventListener('input', filterTable);
    filterRoom.addEventListener('change', filterTable);
});

// Konfirmasi hapus
function confirmDelete(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus mentor "${nama}"?\n\nTindakan ini akan:\n- Menghapus mentor dari semua room\n- Menghapus semua data mentor\n- Tidak dapat dibatalkan`)) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection