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
            <form id="filterForm" action="{{ route('mentor.logbook.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold mb-2" style="font-size: 0.95rem;">
                        <i class="bi bi-door-open me-1"></i> Filter by Room
                    </label>
                    <select name="room_id" class="form-select form-select-lg" onchange="this.form.submit()" style="font-size: 1rem;">
                        <option value="">Semua Room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->room_id }}" {{ request('room_id') == $room->room_id ? 'selected' : '' }}>
                                {{ $room->nama_room }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold mb-2" style="font-size: 0.95rem;">
                        <i class="bi bi-check-circle me-1"></i> Filter by Status
                    </label>
                    <select name="status" class="form-select form-select-lg" onchange="this.form.submit()" style="font-size: 1rem;">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('mentor.logbook.index') }}" class="btn btn-secondary w-100" style="padding: 0.6rem;">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
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
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logbooks as $index => $logbook)
                        <tr>
                            <td>{{ $logbooks->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('mentor.logbook.peserta', $logbook->user->id) }}" class="text-decoration-none">
                                    <strong class="text-primary">{{ $logbook->user->nama }}</strong>
                                </a>
                                <br>
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
                                    <span class="badge rounded-pill bg-success shadow-sm px-3 py-2 text-white" style="font-size: 0.85rem; font-weight: 600;">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved
                                    </span>
                                @else
                                    <span class="badge rounded-pill shadow-sm px-3 py-2 text-white" style="font-size: 0.85rem; font-weight: 600; background-color: #ffc107;">
                                        <i class="bi bi-clock-fill me-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('mentor.logbook.approve', $logbook->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($logbook->is_approved)
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger shadow-sm"
                                                title="Unapprove"
                                                style="min-width: 80px;">
                                            <i class="bi bi-x-circle"></i> Batal
                                        </button>
                                    @else
                                        <button type="submit" 
                                                class="btn btn-sm btn-success shadow-sm"
                                                title="Approve"
                                                style="min-width: 80px;">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">Belum ada logbook dari peserta.</p>
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