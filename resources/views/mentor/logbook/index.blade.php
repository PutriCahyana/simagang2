@extends('layout.app')

@section('konten')
<div class="container mt-4">
    <h2 class="mb-4">Logbook Peserta</h2>

    <!-- Tab Navigation: Active vs Archive -->
    <ul class="nav nav-pills mb-4" id="logbookTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $mode === 'active' ? 'active' : '' }}" 
               href="{{ route('mentor.logbook.index', array_merge(request()->except('mode'), ['mode' => 'active'])) }}">
                <i class="bi bi-person-check me-2"></i>
                Peserta Aktif
                <span class="badge bg-white text-primary ms-2 fw-bold">{{ $activeCount }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $mode === 'archive' ? 'active' : '' }}" 
               href="{{ route('mentor.logbook.index', array_merge(request()->except('mode'), ['mode' => 'archive'])) }}">
                <i class="bi bi-archive me-2"></i>
                Archive (Selesai)
                <span class="badge bg-white text-dark ms-2 fw-bold">{{ $archiveCount }}</span>
            </a>
        </li>
    </ul>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" action="{{ route('mentor.logbook.index') }}" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="mode" value="{{ $mode }}">
                
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
                    <a href="{{ route('mentor.logbook.index', ['mode' => $mode]) }}" class="btn btn-secondary w-100" style="padding: 0.6rem;">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Banner untuk Archive Mode -->
    @if($mode === 'archive')
        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-3" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Mode Archive</strong> - Menampilkan logbook dari peserta yang sudah menyelesaikan magang (periode_end sudah lewat).
            </div>
        </div>
    @endif

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
                            <th class="text-center" style="min-width: 250px;">Aksi</th>
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
                                    @if($mode === 'archive' && $logbook->user->peserta)
                                        <br>
                                        <span class="badge bg-secondary text-white" style="font-size: 0.75rem; font-weight: 600;">
                                            <i class="bi bi-calendar-x"></i> End: {{ $logbook->user->peserta->periode_end->format('d/m/Y') }}
                                        </span>
                                    @elseif($mode === 'active' && $logbook->user->peserta && $logbook->user->peserta->periode_end)
                                        <br>
                                        <span class="badge bg-success text-white" style="font-size: 0.75rem; font-weight: 600;">
                                            <i class="bi bi-calendar-check"></i> End: {{ $logbook->user->peserta->periode_end->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $logbook->date->format('d/m/Y') }}</td>
                                <td>{{ $logbook->jam_masuk }} - {{ $logbook->jam_keluar }}</td>
                                <td>{{ Str::limit($logbook->aktivitas, 50) }}</td>
                                <td>
                                    <span class="badge rounded-pill shadow-sm px-3 py-2 text-white keterangan-badge" 
                                          data-logbook-id="{{ $logbook->id }}"
                                          style="font-size: 0.85rem; font-weight: 600;
                                        @if($logbook->keterangan == 'offline_kantor') background-color: #0d6efd;
                                        @elseif($logbook->keterangan == 'online') background-color: #0dcaf0;
                                        @elseif($logbook->keterangan == 'sakit') background-color: #ffc107;
                                        @elseif($logbook->keterangan == 'izin') background-color: #6c757d;
                                        @else background-color: #dc3545;
                                        @endif">
                                        @if($logbook->keterangan == 'offline_kantor') Offline Kantor
                                        @elseif($logbook->keterangan == 'online') Online
                                        @elseif($logbook->keterangan == 'sakit') Sakit
                                        @elseif($logbook->keterangan == 'izin') Izin
                                        @else Alpha
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $logbook->room->nama_room }}</td>
                                <td class="text-center">
                                    @if($mode === 'archive')
                                        <!-- Di mode archive, hanya tampilkan status -->
                                        @if($logbook->is_approved)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="bi bi-check-circle"></i> Approved
                                            </span>
                                        @else
                                            <span class="badge bg-warning px-3 py-2">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        @endif
                                    @else
                                        <!-- Di mode active, tampilkan tombol aksi seperti biasa -->
                                        <div class="action-buttons" data-logbook-id="{{ $logbook->id }}">
                                            @if($logbook->is_approved)
                                                <!-- Jika sudah approved, tampilkan button BATAL -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger shadow-sm btn-unapprove"
                                                        data-logbook-id="{{ $logbook->id }}"
                                                        data-url="{{ route('mentor.logbook.unapprove', $logbook->id) }}">
                                                    <i class="bi bi-x-circle"></i> Batal
                                                </button>
                                            @else
                                                <!-- Jika belum approved, tampilkan button APPROVE + 3 button keterangan lain -->
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <!-- Button APPROVE (hijau) -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success shadow-sm btn-approve" 
                                                            data-logbook-id="{{ $logbook->id }}"
                                                            data-url="{{ route('mentor.logbook.approve', $logbook->id) }}"
                                                            title="Approve sesuai keterangan peserta">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>

                                                    <!-- Button keterangan LAINNYA (selain yang dipilih peserta) -->
                                                    @if($logbook->keterangan != 'offline_kantor')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-primary shadow-sm btn-approve-keterangan" 
                                                                data-logbook-id="{{ $logbook->id }}"
                                                                data-url="{{ route('mentor.logbook.approve-keterangan', $logbook->id) }}"
                                                                data-keterangan="offline_kantor"
                                                                title="Offline Kantor">
                                                            O
                                                        </button>
                                                    @endif

                                                    @if($logbook->keterangan != 'sakit')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-warning text-white shadow-sm btn-approve-keterangan" 
                                                                data-logbook-id="{{ $logbook->id }}"
                                                                data-url="{{ route('mentor.logbook.approve-keterangan', $logbook->id) }}"
                                                                data-keterangan="sakit"
                                                                title="Sakit">
                                                            S
                                                        </button>
                                                    @endif

                                                    @if($logbook->keterangan != 'izin')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-secondary shadow-sm btn-approve-keterangan" 
                                                                data-logbook-id="{{ $logbook->id }}"
                                                                data-url="{{ route('mentor.logbook.approve-keterangan', $logbook->id) }}"
                                                                data-keterangan="izin"
                                                                title="Izin">
                                                            I
                                                        </button>
                                                    @endif

                                                    @if($logbook->keterangan != 'alpha')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger shadow-sm btn-approve-keterangan" 
                                                                data-logbook-id="{{ $logbook->id }}"
                                                                data-url="{{ route('mentor.logbook.approve-keterangan', $logbook->id) }}"
                                                                data-keterangan="alpha"
                                                                title="Alpha">
                                                            A
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">
                                        @if($mode === 'archive')
                                            Belum ada logbook dari peserta yang sudah selesai magang.
                                        @else
                                            Belum ada logbook dari peserta aktif.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $logbooks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF token untuk AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle APPROVE button
    $(document).on('click', '.btn-approve', function() {
        const btn = $(this);
        const logbookId = btn.data('logbook-id');
        const url = btn.data('url');
        const actionContainer = btn.closest('.action-buttons');

        // Disable button sementara
        btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            success: function(response) {
                if (response.success && response.is_approved) {
                    // Ganti semua button dengan button BATAL
                    actionContainer.html(`
                        <button type="button" 
                                class="btn btn-sm btn-danger shadow-sm btn-unapprove"
                                data-logbook-id="${logbookId}"
                                data-url="${url.replace('/approve', '/unapprove')}">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                btn.prop('disabled', false);
            }
        });
    });

    // Handle APPROVE dengan KETERANGAN (O, S, I, A)
    $(document).on('click', '.btn-approve-keterangan', function() {
        const btn = $(this);
        const logbookId = btn.data('logbook-id');
        const url = btn.data('url');
        const keterangan = btn.data('keterangan');
        const actionContainer = btn.closest('.action-buttons');
        const keteranganBadge = $(`.keterangan-badge[data-logbook-id="${logbookId}"]`);

        // Disable semua button sementara
        actionContainer.find('button').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                keterangan: keterangan
            },
            success: function(response) {
                if (response.success && response.is_approved) {
                    // Update badge keterangan
                    keteranganBadge.css('background-color', response.keterangan_color);
                    keteranganBadge.text(response.keterangan_label);

                    // Ganti semua button dengan button BATAL
                    actionContainer.html(`
                        <button type="button" 
                                class="btn btn-sm btn-danger shadow-sm btn-unapprove"
                                data-logbook-id="${logbookId}"
                                data-url="${url.replace('/approve-keterangan', '/unapprove')}">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                actionContainer.find('button').prop('disabled', false);
            }
        });
    });

    // Handle UNAPPROVE button (BATAL)
    $(document).on('click', '.btn-unapprove', function() {
        const btn = $(this);
        const logbookId = btn.data('logbook-id');
        const url = btn.data('url');
        const actionContainer = btn.closest('.action-buttons');

        // Disable button sementara
        btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            success: function(response) {
                if (response.success && !response.is_approved) {
                    // Refresh halaman untuk menampilkan kembali button approve
                    // Karena kita perlu tahu keterangan yang dipilih peserta
                    location.reload();
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection