@extends('layout/app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-book"></i>
    {{ $judul }}
</h1>
    <div class="card">
        <div class="card-header d-flex flex-wrap b">
            {{-- ini nanti dihapus --}}
            <a href="{{ route('roomCreate') }}" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addRoomModal">
                <i class="fas fa-plus mr-2"></i>
                Add Room</a>
                
                <!-- Include modal create -->
                @include('admin.room.create')

                <!-- Include modal edit -->
                @include('admin.room.edit')
        </div>
        <div class="card-body">
                <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead class="bg-primary text-white">
                <tr class="text-center">
                    <th>No</th>
                    <th>Room</th>
                    <th>Description</th>
                    <th>Mentor</th>
                    <th>Created at</th>
                    <th>Code</th>
                    <th style="width: 140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $index => $room)
                <tr>
                   <td>{{ $loop->iteration }}</td>
                   <td>{{ $room->nama_room }}</td>
                   <td>{{ $room->deskripsi ?? '-' }}</td>
                   <td>{{ $room->mentor->user->nama ?? 'by Admin' }}</td> 
                   <td>{{ $room->created_at->format('d/m/Y H:i') }}</td>
                  <td>
                        <div class="d-flex align-items-center justify-content-start">
                            <span id="code-{{ $room->id }}" class="fw-bold me-3">{{ $room->code }}</span>
                            <button 
                                type="button" 
                                class="btn btn-sm btn-outline-primary"
                                onclick="copyCode('{{ $room->id }}')"
                                title="Salin kode"
                            >
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </td>



               <td class="text-center" style="white-space: nowrap;">
                    <a href="{{ route('room.show', $room->room_id) }}" class="btn btn-success btn-sm" title="Lihat">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button type="button" 
                            class="btn btn-warning btn-sm" 
                            data-toggle="modal" 
                            data-target="#editRoomModal"
                            data-id="{{ $room->room_id }}" 
                            data-nama="{{ $room->nama_room }}" 
                            data-deskripsi="{{ $room->deskripsi ?? '' }}"
                            title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('room.destroy', $room->room_id) }}" method="POST" style="display: inline-block;" 
                        onsubmit="return confirm('Yakin ingin hapus room ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <div class="py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <h5>Belum ada data room</h5>
                            <p>Klik tombol "Add Room" untuk membuat room baru</p>
                        </div>
                    </td>
                </tr>
                    
                @endforelse
            </tbody>
        </table>
        </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// Copy code function
function copyCode(roomId) {
    const codeElement = document.getElementById('code-' + roomId);
    const code = codeElement.innerText;
    
    navigator.clipboard.writeText(code).then(function() {
        alert('Kode berhasil disalin: ' + code);
    });
}

// Edit room modal handler
$('#editRoomModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const roomId = button.data('id');
    const roomNama = button.data('nama');
    const roomDeskripsi = button.data('deskripsi');
    
    // Update form action
    $('#editRoomForm').attr('action', '/admin/room/' + roomId);
    
    // Fill form fields
    $('#edit_room_id').val(roomId);
    $('#edit_nama_room').val(roomNama);
    $('#edit_deskripsi').val(roomDeskripsi || '');
});
</script>
@endpush