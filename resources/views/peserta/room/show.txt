@extends('layout.app')

@section('title', $room->nama_room ?? 'Classroom')

@section('konten')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="card shadow mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="card-body text-white p-4">
            <h1 class="h2 mb-2 font-weight-bold">{{ $room->nama_room ?? 'Web Development 101' }}</h1>
            <p class="mb-2">
                <i class="fas fa-user-tie mr-2"></i>
                Mentor: <strong>{{ $room->mentor->user->nama ?? 'Mentor' }}</strong>
            </p>
            <p class="mb-0 text-white-50">{{ $room->deskripsi ?? 'Pelajari web development dari dasar hingga mahir dengan praktik langsung.' }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Main Panel -->
        <div class="col-lg-8 mb-4">
            <!-- Tabs Navigation -->
            <div class="card shadow mb-3" style="border: none;">
                <div class="card-header p-0" style="background: white; border-bottom: 1px solid #e3e6f0;">
                    <ul class="nav nav-tabs border-0" id="roomTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-semibold px-4 py-3" id="tugas-tab" data-toggle="tab" href="#tugas" role="tab">
                                📋 Tugas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-semibold px-4 py-3" id="materi-tab" data-toggle="tab" href="#materi" role="tab">
                                📚 Materi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-semibold px-4 py-3" id="pengumuman-tab" data-toggle="tab" href="#pengumuman" role="tab">
                                📢 Pengumuman
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="roomTabsContent">
                <!-- Tab: Tugas -->
                <div class="tab-pane fade show active" id="tugas" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0 font-weight-bold">Daftar Tugas</h4>
                    </div>
                    
                    <!-- Filter Status -->
                    <div class="mb-3">
                        <button class="btn btn-sm btn-primary rounded-pill mr-2">Semua</button>
                        <button class="btn btn-sm btn-outline-success rounded-pill mr-2">Selesai</button>
                        <button class="btn btn-sm btn-outline-warning rounded-pill mr-2">Pending</button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill">Terlambat</button>
                    </div>

                    <!-- Daftar Tugas -->
                    @php
                        $tugas = [
                            [
                                'id' => 1,
                                'judul' => 'Buat Landing Page Responsive',
                                'deskripsi' => 'Buat halaman landing menggunakan HTML, CSS, dan JavaScript yang responsive di semua perangkat.',
                                'deadline' => '2025-12-05',
                                'status' => 'selesai',
                                'submitted_at' => '2025-11-28',
                                'grade' => '95'
                            ],
                            [
                                'id' => 2,
                                'judul' => 'Membuat Form Validasi',
                                'deskripsi' => 'Buat form dengan validasi client-side dan server-side menggunakan JavaScript dan PHP.',
                                'deadline' => '2025-12-10',
                                'status' => 'pending',
                                'submitted_at' => null,
                                'grade' => null
                            ],
                            [
                                'id' => 3,
                                'judul' => 'API CRUD Sederhana',
                                'deskripsi' => 'Buat API REST untuk CRUD (Create, Read, Update, Delete) produk menggunakan Laravel.',
                                'deadline' => '2025-12-12',
                                'status' => 'terlambat',
                                'submitted_at' => null,
                                'grade' => null
                            ],
                            [
                                'id' => 4,
                                'judul' => 'Database Design',
                                'deskripsi' => 'Desain database untuk aplikasi e-commerce sederhana dan buat ERD.',
                                'deadline' => '2025-12-15',
                                'status' => 'pending',
                                'submitted_at' => null,
                                'grade' => null
                            ]
                        ];
                    @endphp

                    @foreach($tugas as $item)
                        <div class="card shadow-sm mb-3 border-left-{{ $item['status'] == 'selesai' ? 'success' : ($item['status'] == 'terlambat' ? 'danger' : 'warning') }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h5 class="font-weight-bold text-gray-800 mb-1">{{ $item['judul'] }}</h5>
                                        <p class="text-muted small mb-0">{{ $item['deskripsi'] }}</p>
                                    </div>
                                    <span class="badge badge-{{ $item['status'] == 'selesai' ? 'success' : ($item['status'] == 'terlambat' ? 'danger' : 'warning') }} ml-3">
                                        @if($item['status'] == 'selesai') ✓ Selesai
                                        @elseif($item['status'] == 'terlambat') ⚠ Terlambat
                                        @else ⏳ Pending @endif
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="text-sm text-muted">
                                        <span class="mr-3">📅 <strong>{{ date('d M Y', strtotime($item['deadline'])) }}</strong></span>
                                        @if($item['submitted_at'])
                                            <span class="mr-3">📤 {{ date('d M Y', strtotime($item['submitted_at'])) }}</span>
                                        @endif
                                        @if($item['grade'])
                                            <span class="badge badge-warning">Nilai: {{ $item['grade'] }}/100</span>
                                        @endif
                                    </div>
                                    <button onclick="openTaskDetail({{ $item['id'] }})" class="btn btn-sm btn-primary">
                                        Lihat <i class="fas fa-arrow-right ml-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tab: Materi -->
                <div class="tab-pane fade" id="materi" role="tabpanel">
                    <h4 class="mb-3 font-weight-bold">Materi Pembelajaran</h4>
                    
                    @php
                        $materi = [
                            [
                                'id' => 1,
                                'judul' => 'Dasar HTML & CSS',
                                'deskripsi' => 'Pelajari struktur HTML dan styling dengan CSS dari nol.',
                                'tipe' => 'video',
                                'durasi' => '2 jam',
                                'url' => '#'
                            ],
                            [
                                'id' => 2,
                                'judul' => 'JavaScript Fundamentals',
                                'deskripsi' => 'Memahami konsep dasar JavaScript: variabel, function, dan DOM manipulation.',
                                'tipe' => 'pdf',
                                'durasi' => '45 menit',
                                'url' => '#'
                            ],
                            [
                                'id' => 3,
                                'judul' => 'Responsive Web Design',
                                'deskripsi' => 'Teknik membuat website yang responsive di berbagai ukuran layar.',
                                'tipe' => 'video',
                                'durasi' => '1.5 jam',
                                'url' => '#'
                            ],
                            [
                                'id' => 4,
                                'judul' => 'Intro to Laravel Framework',
                                'deskripsi' => 'Setup Laravel dan memahami struktur project Laravel.',
                                'tipe' => 'artikel',
                                'durasi' => '30 menit',
                                'url' => '#'
                            ]
                        ];
                    @endphp

                    @foreach($materi as $m)
                        <div class="card shadow-sm mb-3">
                            <div class="card-body d-flex align-items-start">
                                <div class="mr-3" style="font-size: 36px;">
                                    @if($m['tipe'] == 'video') 🎥
                                    @elseif($m['tipe'] == 'pdf') 📄
                                    @else 📝 @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="font-weight-bold mb-1">{{ $m['judul'] }}</h5>
                                    <p class="text-muted small mb-2">{{ $m['deskripsi'] }}</p>
                                    <div>
                                        <small class="text-muted mr-3">⏱️ {{ $m['durasi'] }}</small>
                                        <span class="badge badge-primary text-capitalize">{{ $m['tipe'] }}</span>
                                    </div>
                                </div>
                                <a href="{{ $m['url'] }}" class="btn btn-sm btn-outline-primary align-self-center">
                                    Buka <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tab: Pengumuman -->
                <div class="tab-pane fade" id="pengumuman" role="tabpanel">
                    <h4 class="mb-3 font-weight-bold">Pengumuman</h4>
                    
                    @php
                        $pengumuman = [
                            [
                                'id' => 1,
                                'judul' => 'Jadwal Quiz Online',
                                'isi' => 'Quiz online akan dilaksanakan pada hari Jumat, 13 Desember 2025 pukul 14:00 - 15:30. Durasi 90 menit dengan 50 soal pilihan ganda.',
                                'tanggal' => '2025-11-27',
                                'penting' => true
                            ],
                            [
                                'id' => 2,
                                'judul' => 'Update Materi Pembelajaran',
                                'isi' => 'Materi tentang "Advanced JavaScript" telah diupload. Silakan pelajari dan siapkan pertanyaan untuk sesi live coding minggu depan.',
                                'tanggal' => '2025-11-26',
                                'penting' => false
                            ],
                            [
                                'id' => 3,
                                'judul' => 'Perubahan Deadline Tugas',
                                'isi' => 'Deadline tugas "API CRUD Sederhana" diperpanjang menjadi 15 Desember 2025 pukil 23:59. Terima kasih atas pengertiannya.',
                                'tanggal' => '2025-11-25',
                                'penting' => true
                            ]
                        ];
                    @endphp

                    @foreach($pengumuman as $p)
                        <div class="card shadow-sm mb-3 @if($p['penting']) border-left-danger @endif">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div>
                                        <h5 class="font-weight-bold mb-1">
                                            {{ $p['judul'] }}
                                            @if($p['penting'])
                                                <span class="badge badge-danger ml-2">PENTING</span>
                                            @endif
                                        </h5>
                                        <p class="text-muted mb-0">{{ $p['isi'] }}</p>
                                    </div>
                                </div>
                                <small class="text-muted">{{ date('d M Y H:i', strtotime($p['tanggal'])) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Progress Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Progress</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Tugas Selesai</small>
                            <small class="font-weight-bold">1/4</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 25%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Materi Dikuasai</small>
                            <small class="font-weight-bold">3/4</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadline -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">⏰ Deadline Terdekat</h6>
                </div>
                <div class="card-body">
                    <p class="font-weight-bold mb-1">Membuat Form Validasi</p>
                    <h2 class="mb-2 text-warning">5 Hari</h2>
                    <small class="text-muted">Deadline: 10 Desember 2025</small>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Rata-rata Nilai</span>
                        <span class="font-weight-bold text-success">95</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tugas Terlambat</span>
                        <span class="font-weight-bold text-danger">1</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Total Jam Belajar</span>
                        <span class="font-weight-bold text-primary">12 jam</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Detail Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div>
                    <h4 class="modal-title font-weight-bold" id="modalTitle">Judul Tugas</h4>
                    <small id="modalDeadline">Deadline: -</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold">Deskripsi</h6>
                    <p id="modalDesc" class="text-muted">-</p>
                </div>

                <div id="submissionSection" class="alert alert-success" style="display: none;">
                    <h6 class="font-weight-bold mb-3">Status Pengumpulan</h6>
                    <p class="mb-2"><i class="fas fa-check-circle"></i> <span id="submissionStatus">Sudah dikumpulkan</span></p>
                    <p class="mb-1 small" id="submissionDate">Waktu pengumpulan: -</p>
                    <p class="mb-0 small" id="submissionGrade">Nilai: -</p>
                </div>

                <div id="submitSection">
                    <h6 class="font-weight-bold mb-3">Kumpulkan Tugas</h6>
                    <div class="border border-dashed rounded p-4 text-center mb-3" id="dropZone" style="cursor: pointer; border-width: 2px;">
                        <p class="mb-1">📎 Drag & drop file atau klik untuk memilih</p>
                        <small class="text-muted">Maksimal 50MB (PDF, DOC, ZIP, dll)</small>
                        <input type="file" id="fileInput" class="d-none">
                    </div>
                    <div id="fileName" class="text-muted small mb-3" style="display: none;">
                        File: <span id="selectedFile" class="font-weight-bold"></span>
                        <button onclick="clearFile()" class="btn btn-sm btn-link text-danger p-0 ml-2">Hapus</button>
                    </div>
                    <button onclick="submitTask()" class="btn btn-primary btn-block">
                        <i class="fas fa-upload mr-2"></i>Kumpulkan Tugas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentTaskId = null;

    function openTaskDetail(taskId) {
        currentTaskId = taskId;
        const tugas = {
            1: {
                judul: 'Buat Landing Page Responsive',
                deskripsi: 'Buat halaman landing menggunakan HTML, CSS, dan JavaScript yang responsive di semua perangkat.',
                deadline: '2025-12-05',
                status: 'selesai',
                submitted_at: '2025-11-28',
                grade: '95'
            },
            2: {
                judul: 'Membuat Form Validasi',
                deskripsi: 'Buat form dengan validasi client-side dan server-side menggunakan JavaScript dan PHP.',
                deadline: '2025-12-10',
                status: 'pending',
                submitted_at: null,
                grade: null
            },
            3: {
                judul: 'API CRUD Sederhana',
                deskripsi: 'Buat API REST untuk CRUD (Create, Read, Update, Delete) produk menggunakan Laravel.',
                deadline: '2025-12-12',
                status: 'terlambat',
                submitted_at: null,
                grade: null
            },
            4: {
                judul: 'Database Design',
                deskripsi: 'Desain database untuk aplikasi e-commerce sederhana dan buat ERD.',
                deadline: '2025-12-15',
                status: 'pending',
                submitted_at: null,
                grade: null
            }
        };

        const task = tugas[taskId];
        document.getElementById('modalTitle').textContent = task.judul;
        document.getElementById('modalDeadline').textContent = 'Deadline: ' + new Date(task.deadline).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        document.getElementById('modalDesc').textContent = task.deskripsi;

        const submissionSection = document.getElementById('submissionSection');
        const submitSection = document.getElementById('submitSection');

        if (task.status === 'selesai') {
            submissionSection.style.display = 'block';
            submitSection.style.display = 'none';
            document.getElementById('submissionStatus').textContent = 'Sudah dikumpulkan';
            document.getElementById('submissionDate').textContent = 'Waktu pengumpulan: ' + new Date(task.submitted_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            document.getElementById('submissionGrade').textContent = 'Nilai: ' + task.grade + '/100';
        } else {
            submissionSection.style.display = 'none';
            submitSection.style.display = 'block';
            clearFile();
        }

        $('#taskModal').modal('show');
    }

    function clearFile() {
        document.getElementById('fileInput').value = '';
        document.getElementById('fileName').style.display = 'none';
        document.getElementById('selectedFile').textContent = '';
    }

    function submitTask() {
        const fileInput = document.getElementById('fileInput');
        if (!fileInput.value) {
            alert('Silakan pilih file terlebih dahulu');
            return;
        }
        
        alert('Tugas berhasil dikumpulkan!\nTugas ID: ' + currentTaskId + '\nFile: ' + fileInput.files[0].name + '\n\nHubungkan ke backend untuk implementasi sebenarnya');
        $('#taskModal').modal('hide');
    }

    // File input handler
    $(document).ready(function() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileNameDiv = document.getElementById('fileName');
        const selectedFileSpan = document.getElementById('selectedFile');

        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                selectedFileSpan.textContent = this.files[0].name;
                fileNameDiv.style.display = 'block';
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#4e73df';
            dropZone.style.backgroundColor = '#f8f9fc';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '';
            dropZone.style.backgroundColor = '';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '';
            dropZone.style.backgroundColor = '';
            fileInput.files = e.dataTransfer.files;
            if (fileInput.files.length > 0) {
                selectedFileSpan.textContent = fileInput.files[0].name;
                fileNameDiv.style.display = 'block';
            }
        });
    });
</script>
@endpush

@endsection