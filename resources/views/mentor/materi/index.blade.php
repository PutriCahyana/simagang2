@extends('layout/app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-book"></i>
    {{ $judul }}
</h1>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex flex-wrap">
        <a href="{{ route('mentor.materiCreate') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus mr-2"></i>
            Add Materi
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materi as $m)
                    <tr>
                       <td>{{ $loop->iteration }}</td>
                       <td>{{ $m->judul }}</td>
                       <td>{{ $m->room ? $m->room->nama_room : '-' }}</td>
                       <td>{{ $m->deskripsi ?? '-' }}</td>
                       <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                       <td class="text-center">
                            <a href="{{ route('mentor.materiView', $m->materi_id) }}" class="btn btn-success btn-sm" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('mentor.materiEdit', $m->materi_id) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('mentor.materiDestroy', $m->materi_id) }}" method="POST" style="display: inline-block;" 
                                onsubmit="return confirm('Yakin ingin hapus materi ini?')">
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
                        <td colspan="6" class="text-center text-muted">
                            <div class="py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>Belum ada data materi</h5>
                                <p>Klik tombol "Add Materi" untuk membuat materi baru</p>
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
$(document).ready(function() {
    // Cek dulu, kalau tabel belum diinisialisasi baru buat
    if (!$.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Kolom Action tidak bisa di-sort
            ],
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "previous": "Previous",
                    "next": "Next"
                }
            }
        });
    }
});
</script>
@endpush