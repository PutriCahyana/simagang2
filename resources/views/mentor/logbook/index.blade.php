@extends('layout.app')

@section('konten')
<div class="container mt-4">
    <h2 class="mb-4">Logbook Peserta</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('mentor.logbook.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Room</label>
                    <select name="room_id" class="form-select">
                        <option value="">Semua Room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->room_id }}" {{ request('room_id') == $room->room_id ? 'selected' : '' }}>
                                {{ $room->nama_room }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter by Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <a href="{{ route('mentor.logbook.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Peserta</th>
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
                                <td>
                                    <strong>{{ $logbook->user->nama }}</strong><br>
                                    <small class="text-muted">{{ $logbook->user->username }}</small>
                                </td>
                                <td>{{ $logbook->date->format('d/m/Y') }}</td>
                                <td>{{ $logbook->jam_masuk }} - {{ $logbook->jam_keluar }}</td>
                                <td>{{ Str::limit($logbook->aktivitas, 50) }}</td>
                                <td>
                                    <form action="{{ route('mentor.logbook.keterangan', $logbook->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="keterangan" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="offline_kantor" {{ $logbook->keterangan == 'offline_kantor' ? 'selected' : '' }}>Offline Kantor</option>
                                            <option value="online" {{ $logbook->keterangan == 'online' ? 'selected' : '' }}>Online</option>
                                            <option value="sakit" {{ $logbook->keterangan == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="izin" {{ $logbook->keterangan == 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="alpha" {{ $logbook->keterangan == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                    </form>
                                </td>
                                <td>{{ $logbook->room->nama_room }}</td>
                                <td>
                                    @if($logbook->is_approved)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Approved
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('mentor.logbook.approve', $logbook->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $logbook->is_approved ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                title="{{ $logbook->is_approved ? 'Unapprove' : 'Approve' }}">
                                            @if($logbook->is_approved)
                                                <i class="fas fa-x-circle"></i>
                                            @else
                                                <i class="fas fa-check-circle"></i>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Belum ada logbook dari peserta.
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