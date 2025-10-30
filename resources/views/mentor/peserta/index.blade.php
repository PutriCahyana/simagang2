@extends('layout.app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-users mr-2"></i>
    {{ $judul }}
</h1>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Campus</th>
                        <th>Internship Period</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peserta as $p)
                    @php
                    // default values
                    $userName = '-';
                    $userUsername = '-';
                    $kampus = '-';
                    $periode = '-';

                    // jika $p adalah object (Eloquent)
                    if (is_object($p)) {
                    // ambil data user lewat relasi jika ada
                    if (isset($p->user)) {
                    $userName = $p->user->name ?? $p->user->nama ?? ($p->user->username ?? '-');
                    $userUsername = $p->user->username ?? $p->user->email ?? '-';
                    } else {
                    // fallback ke properti peserta langsung
                    $userName = $p->nama ?? $p->name ?? '-';
                    $userUsername = $p->username ?? $p->email ?? '-';
                    }

                    $kampus = $p->institut ?? $p->kampus ?? '-';

                    if (!empty($p->periode_start) && !empty($p->periode_end)) {
                    $periode = \Carbon\Carbon::parse($p->periode_start)->format('d M Y')
                    .' - '.\Carbon\Carbon::parse($p->periode_end)->format('d M Y');
                    }
                    }
                    // jika $p adalah array (dummy)
                    elseif (is_array($p)) {
                    $userName = $p['nama'] ?? $p['name'] ?? ($p['username'] ?? '-');
                    $userUsername = $p['username'] ?? $p['email'] ?? '-';
                    $kampus = $p['institut'] ?? $p['kampus'] ?? '-';
                    if (!empty($p['periode_start']) && !empty($p['periode_end'])) {
                    $periode = \Carbon\Carbon::parse($p['periode_start'])->format('d M Y')
                    .' - '.\Carbon\Carbon::parse($p['periode_end'])->format('d M Y');
                    }
                    }
                    @endphp

                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $userName }}</td>
                        <td>{{ $userUsername }}</td>
                        <td>{{ $kampus }}</td>
                        <td>{{ $periode }}</td>
                        <td class="text-center">
                            <a href="{{ url('peserta/'.$p->id ?? $p['id']) }}" class="btn btn-success btn-sm" title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ url('peserta/'.($p->id ?? $p['id'])) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endpush