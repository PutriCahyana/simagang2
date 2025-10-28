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
                            <th>Aksi</th>
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
                                    <span class="badge 
                                        @if($logbook->keterangan == 'offline_kantor') bg-primary
                                        @elseif($logbook->keterangan == 'online') bg-info
                                        @elseif($logbook->keterangan == 'sakit') bg-warning
                                        @elseif($logbook->keterangan == 'izin') bg-secondary
                                        @else bg-danger
                                        @endif">
                                        {{ $logbook->keterangan_label }}
                                    </span>
                                </td>
                                <td>{{ $logbook->room->nama_room }}</td>
                                <td>
                                    @if($logbook->is_approved)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Approved
                                        </span>
                                        <small class="d-block text-muted mt-1">
                                            oleh {{ $logbook->approver->nama }}
                                        </small>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$logbook->is_approved)
                                        <a href="{{ route('peserta.logbook.edit', $logbook->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('peserta.logbook.destroy', $logbook->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus logbook ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Belum ada logbook. <a href="{{ route('peserta.logbook.create') }}">Tambah sekarang</a>
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