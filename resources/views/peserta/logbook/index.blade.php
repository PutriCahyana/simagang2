@extends('layout.app')

@section('konten')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Logbook Saya</h2>
        <div>
            <a href="{{ route('peserta.logbook.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Logbook
            </a>
            <div class="btn-group ms-2">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('peserta.logbook.export.pdf') }}">
                        <i class="bi bi-file-pdf"></i> Export PDF
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('peserta.logbook.export.excel') }}">
                        <i class="bi bi-file-excel"></i> Export Excel
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Aktivitas</th>
                            <th>Keterangan</th>
                            <th>Room</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbooks as $index => $logbook)
                            <tr>
                                <td>{{ $logbooks->firstItem() + $index }}</td>
                                <td>{{ $logbook->date->format('d/m/Y') }}</td>
                                <td>{{ $logbook->jam_masuk }} - {{ $logbook->jam_keluar }}</td>
                                <td>{{ Str::limit($logbook->aktivitas, 50) }}</td>
                                <td>
                                    <span class="badge rounded-pill shadow-sm px-3 py-2 text-white" 
                                          style="font-size: 0.85rem; font-weight: 600;
                                        @if($logbook->keterangan == 'offline_kantor') background-color: #0d6efd;
                                        @elseif($logbook->keterangan == 'online') background-color: #0dcaf0;
                                        @elseif($logbook->keterangan == 'sakit') background-color: #ffc107;
                                        @elseif($logbook->keterangan == 'izin') background-color: #6c757d;
                                        @else background-color: #dc3545;
                                        @endif">
                                        {{ $logbook->keterangan_label }}
                                    </span>
                                </td>
                                <td>{{ $logbook->room->nama_room }}</td>
                                <td>
                                    @if($logbook->is_approved)
                                        <span class="badge rounded-pill bg-success shadow-sm px-3 py-2 text-white" style="font-size: 0.85rem; font-weight: 600;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Approved
                                        </span>
                                        <small class="d-block text-muted mt-1">
                                            oleh {{ $logbook->approver->nama }}
                                        </small>
                                    @else
                                        <span class="badge rounded-pill shadow-sm px-3 py-2 text-white" style="font-size: 0.85rem; font-weight: 600; background-color: #ffc107;">
                                            <i class="bi bi-clock-fill me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @if(!$logbook->is_approved)
                                            <a href="{{ route('peserta.logbook.edit', $logbook->id) }}" 
                                               class="btn btn-sm btn-outline-primary shadow-sm"
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('peserta.logbook.destroy', $logbook->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus logbook ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger shadow-sm"
                                                        title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">Belum ada logbook. <a href="{{ route('peserta.logbook.create') }}">Tambah sekarang</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $logbooks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection