@extends('layout/app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-book"></i>
    {{ $judul }}
</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Room</h6>
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="fas fa-plus mr-2"></i>
            Add Room
        </a>
        
        <!-- Include modal create -->
        @include('mentor.room.create')
        
        <!-- Include modal edit -->
        @include('mentor.room.edit')
    </div>
    
    <div class="card-body">
        @forelse ($rooms as $room)
        <div class="card border-left-primary shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <!-- Info Room -->
                    <div class="col-md-8">
                        <h5 class="font-weight-bold text-primary mb-2">
                            <i class="fas fa-door-open mr-2"></i>
                            {{ $room->nama_room }}
                        </h5>
                        
                        <p class="text-gray-700 mb-3">
                            {{ $room->deskripsi ?? 'Tidak ada deskripsi' }}
                        </p>
                        
                        <div class="row text-sm">
                            <div class="col-md-6 mb-2">
                                <i class="fas fa-user text-gray-600 mr-2"></i>
                                <span class="text-gray-600">Mentor:</span>
                                <span class="font-weight-bold">{{ $room->mentor->user->nama ?? 'by Admin' }}</span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <i class="fas fa-calendar text-gray-600 mr-2"></i>
                                <span class="text-gray-600">Dibuat:</span>
                                <span class="font-weight-bold">{{ $room->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        
                        <!-- Room Code -->
                        <div class="mt-3">
                            <div class="d-inline-flex align-items-center bg-light border rounded px-3 py-2">
                                <span class="text-gray-600 mr-2">Kode Room:</span>
                                <span id="code-{{ $room->room_id }}" class="font-weight-bold text-primary mr-3">{{ $room->code }}</span>
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-outline-primary"
                                    onclick="copyCode('{{ $room->room_id }}')"
                                    title="Salin kode"
                                >
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-md-4 d-flex flex-column justify-content-center align-items-end">
                        <a href="{{ route('mentor.room.show', $room->room_id) }}" class="btn-action btn-action-primary mb-2 w-100">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail
                        </a>
                        <button type="button" class="btn-action btn-action-primary mb-2 w-100" onclick="editRoom('{{ $room->room_id }}')">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Room
                        </button>
                        <form action="{{ route('mentor.room.destroy', $room->room_id) }}" method="POST" class="w-100" 
                            onsubmit="return confirm('Yakin ingin hapus room ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-action-danger w-100">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Room
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">Belum ada data room</h5>
            <p class="text-gray-500 mb-4">Klik tombol "Add Room" untuk membuat room baru</p>
            <a href="{{ route('mentor.roomCreate') }}" class="btn btn-primary" data-bs-toggle="modal" data-target="#addRoomModal">
                <i class="fas fa-plus mr-2"></i>
                Buat Room Pertama
            </a>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function copyCode(roomId) {
    const codeElement = document.getElementById('code-' + roomId);
    const code = codeElement.textContent;
    const button = event.currentTarget;
    
    navigator.clipboard.writeText(code).then(function() {
        const originalHTML = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        button.disabled = true;
        
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
            button.disabled = false;
        }, 2000);
    }).catch(function(err) {
        alert('Gagal menyalin kode');
    });
}

function editRoom(roomId) {
    // Fetch data room via AJAX
    fetch(`/mentor/room/${roomId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Isi form dengan data room
            document.getElementById('edit_room_id').value = data.room_id;
            document.getElementById('edit_nama_room').value = data.nama_room;
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';
            
            // Set action URL form
            document.getElementById('editRoomForm').action = `/mentor/room/${data.room_id}`;
            
            // Tampilkan modal
            $('#editRoomModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data room');
        });
}
</script>
@endpush

@push('styles')
<style>
.btn-action {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    display: inline-block;
}

.btn-action-primary {
    background-color: #4e73df;
    color: white;
}

.btn-action-primary:hover {
    background-color: #2e59d9;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
}

.btn-action-danger {
    background-color: #e74a3b;
    color: white;
}

.btn-action-danger:hover {
    background-color: #c02d1f;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(231, 74, 59, 0.3);
}
</style>
@endpush

@endsection