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
                            <a class="nav-link active font-weight-semibold px-4 py-3" id="tugas-tab" data-bs-toggle="tab" href="#tugas" role="tab">
                                📋 Tugas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-semibold px-4 py-3" id="materi-tab" data-bs-toggle="tab" href="#materi" role="tab">
                                📚 Materi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-semibold px-4 py-3" id="pengumuman-tab" data-bs-toggle="tab" href="#pengumuman" role="tab">
                                📢 Pengumuman
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="card shadow" style="border: none;">
                <div class="card-body">
                    <div class="tab-content" id="roomTabsContent">
                        <!-- Tab: Tugas -->
                        <div class="tab-pane fade show active" id="tugas" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0 font-weight-bold">Daftar Tugas</h4>
                            </div>
                            
                            <!-- Filter Status -->
                            <div class="mb-3">
                                <button class="btn btn-sm btn-primary rounded-pill mr-2 filter-btn active" data-status="all">Semua</button>
                                <button class="btn btn-sm btn-outline-success rounded-pill mr-2 filter-btn" data-status="selesai">Selesai</button>
                                <button class="btn btn-sm btn-outline-warning rounded-pill mr-2 filter-btn" data-status="pending">Pending</button>
                                <button class="btn btn-sm btn-outline-danger rounded-pill filter-btn" data-status="terlambat">Terlambat</button>
                            </div>

                            <!-- Daftar Tugas -->
                            @forelse($tugasList as $item)
                                <div class="card shadow-sm mb-3 border-left-{{ $item['status'] == 'selesai' ? 'success' : ($item['status'] == 'terlambat' ? 'danger' : 'warning') }} task-item" data-status="{{ $item['status'] }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h5 class="font-weight-bold text-gray-800 mb-1">{{ $item['judul'] }}</h5>
                                                <p class="text-muted small mb-0">{{ Str::limit($item['deskripsi'], 100) }}</p>
                                            </div>
                                            <span class="badge badge-{{ $item['status'] == 'selesai' ? 'success' : ($item['status'] == 'terlambat' ? 'danger' : 'warning') }} ml-3">
                                                @if($item['status'] == 'selesai') ✓ Selesai
                                                @elseif($item['status'] == 'terlambat') ⚠ Terlambat
                                                @else ⏳ Pending @endif
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="text-sm text-muted">
                                                <span class="mr-3">📅 <strong>{{ \Carbon\Carbon::parse($item['deadline'])->format('d M Y') }}</strong></span>
                                                @if($item['submitted_at'])
                                                    <span class="mr-3">📤 {{ \Carbon\Carbon::parse($item['submitted_at'])->format('d M Y H:i') }}</span>
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
                            @empty
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i> Belum ada tugas yang tersedia di room ini.
                                </div>
                            @endforelse
                        </div>

                        <!-- Tab: Materi -->
                        <div class="tab-pane fade" id="materi" role="tabpanel">
                            <h4 class="mb-3 font-weight-bold">Materi Pembelajaran</h4>
                            
                            @forelse($materiList as $m)
                                <div class="card shadow-sm mb-3">
                                    <div class="card-body d-flex align-items-start">
                                        <div class="mr-3" style="font-size: 36px;">
                                            @if($m['tipe'] == 'video') 🎥
                                            @elseif($m['tipe'] == 'pdf') 📄
                                            @else 📝 @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-weight-bold mb-1">{{ $m['judul'] }}</h5>
                                            <p class="text-muted small mb-2">{{ Str::limit($m['deskripsi'], 100) }}</p>
                                            <div>
                                                <small class="text-muted mr-3">⏱️ {{ $m['durasi'] }}</small>
                                                <span class="badge badge-primary text-capitalize">{{ $m['tipe'] }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('peserta.materials.view', [
                                            'id' => $m['id'], 
                                            'from' => 'room',
                                            'back' => route('peserta.room.show', $room->room_id)
                                        ]) }}" class="btn btn-sm btn-outline-primary align-self-center">
                                            Buka <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i> Belum ada materi yang tersedia di room ini.
                                </div>
                            @endforelse
                        </div>

                        <!-- Tab: Pengumuman -->
                        <div class="tab-pane fade" id="pengumuman" role="tabpanel">
                            <h4 class="mb-3 font-weight-bold">Pengumuman</h4>
                            
                            @forelse($pengumumanList as $p)
                                <div class="card shadow-sm mb-3 @if($p->is_penting) border-left-danger @endif">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <div>
                                                <h5 class="font-weight-bold mb-1">
                                                    {{ $p->judul }}
                                                    @if($p->is_penting)
                                                        <span class="badge badge-danger ml-2">PENTING</span>
                                                    @endif
                                                </h5>
                                                <p class="text-muted mb-0">{{ $p->isi }}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock mr-1"></i>{{ $p->created_at->format('d M Y H:i') }}
                                            </small>
                                            <small class="text-muted">
                                                Berlaku hingga: {{ $p->tanggal_kadaluarsa->format('d M Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i> Belum ada pengumuman di room ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Statistik</h6>
                </div>
                <div class="card-body">
                    <!-- Rata-rata Nilai -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Rata-rata Nilai</span>
                        @if($averageGrade !== null)
                            <span class="font-weight-bold {{ $averageGrade >= 80 ? 'text-success' : ($averageGrade >= 60 ? 'text-warning' : 'text-danger') }}">
                                {{ $averageGrade }}
                            </span>
                        @else
                            <span class="font-weight-bold text-muted">-</span>
                        @endif
                    </div>

                    <!-- Total Tugas -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Tugas</span>
                        <span class="font-weight-bold text-primary">{{ $tugasList->count() }}</span>
                    </div>

                    <!-- Tugas Selesai -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tugas Selesai</span>
                        <span class="font-weight-bold text-success">{{ $tugasList->where('status', 'selesai')->count() }}</span>
                    </div>

                    <!-- Tugas Pending -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tugas Pending</span>
                        <span class="font-weight-bold text-warning">{{ $tugasList->where('status', 'pending')->count() }}</span>
                    </div>

                    <!-- Tugas Terlambat -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Tugas Terlambat</span>
                        <span class="font-weight-bold text-danger">{{ $tugasList->where('status', 'terlambat')->count() }}</span>
                    </div>

                    {{-- <!-- Total Jam Belajar -->
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Total Jam Belajar</span>
                        <span class="font-weight-bold text-primary">{{ number_format($totalJamBelajar, 1) }} jam</span>
                    </div> --}}
                </div>
            </div>

            <!-- Materi Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📚 Materi</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Total Materi Tersedia</span>
                        <span class="font-weight-bold text-primary">{{ $materiList->count() }}</span>
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
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="font-weight-bold">Deskripsi</h6>
                    <p id="modalDesc" class="text-muted">-</p>
                </div>

                 <div id="taskFileSection" class="mb-4" style="display: none;">
                    <h6 class="font-weight-bold mb-2">📎 File Tugas dari Mentor</h6>
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger mr-2" style="font-size: 24px;"></i>
                                    <div>
                                        <p class="mb-0 font-weight-semibold" id="taskFileName">filename.pdf</p>
                                        <small class="text-muted">File tugas</small>
                                    </div>
                                </div>
                                <a href="#" id="taskFileDownload" class="btn btn-sm btn-outline-primary" download>
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="submissionSection" class="alert alert-success" style="display: none;">
                    <h6 class="font-weight-bold mb-3">Status Pengumpulan</h6>
                    <p class="mb-2"><i class="fas fa-check-circle"></i> <span id="submissionStatus">Sudah dikumpulkan</span></p>
                    <p class="mb-1 small" id="submissionDate">Waktu pengumpulan: -</p>
                    <p class="mb-0 small" id="submissionGrade">Nilai: -</p>
                </div>

                <div id="submitSection">
                    <h6 class="font-weight-bold mb-3">Kumpulkan Tugas</h6>
                    
                    <!-- Pilihan tipe submission -->
                    <div class="mb-3">
                        <label class="font-weight-semibold">Pilih Tipe Pengumpulan:</label>
                        <div class="btn-group btn-group-toggle d-flex" data-bs-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="submission_type" id="type_file" value="file" checked> 📎 Upload File
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="submission_type" id="type_link" value="link"> 🔗 Link URL
                            </label>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div id="fileSection">
                        <div class="border border-dashed rounded p-4 text-center mb-3" id="dropZone" style="cursor: pointer; border-width: 2px;">
                            <p class="mb-1">📎 Drag & drop file atau klik untuk memilih</p>
                            <small class="text-muted">Maksimal 50MB (PDF, DOC, ZIP, dll)</small>
                            <input type="file" id="fileInput" class="d-none">
                        </div>
                        <div id="fileName" class="text-muted small mb-3" style="display: none;">
                            File: <span id="selectedFile" class="font-weight-bold"></span>
                            <button onclick="clearFile()" class="btn btn-sm btn-link text-danger p-0 ml-2">Hapus</button>
                        </div>
                    </div>

                    <!-- Link Section -->
                    <div id="linkSection" style="display: none;">
                        <div class="form-group">
                            <label for="linkInput">Masukkan Link URL:</label>
                            <input type="url" class="form-control" id="linkInput" placeholder="https://example.com/your-submission">
                            <small class="form-text text-muted">Pastikan link dapat diakses oleh mentor</small>
                        </div>
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
    let currentTaskData = {};

    function openTaskDetail(taskId) {
        currentTaskId = taskId;
        
        // Fetch detail tugas dari server
        $.ajax({
            url: `/peserta/room/task/${taskId}/detail`,
            method: 'GET',
            success: function(task) {
                currentTaskData = task;
                
                document.getElementById('modalTitle').textContent = task.judul;
                document.getElementById('modalDeadline').textContent = 'Deadline: ' + new Date(task.deadline).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                document.getElementById('modalDesc').textContent = task.deskripsi;

               const taskFileSection = document.getElementById('taskFileSection');
                if (task.task_file_path && task.task_file_url) {
                    taskFileSection.style.display = 'block';
                    document.getElementById('taskFileName').textContent = task.task_file_name;
                    document.getElementById('taskFileDownload').href = task.task_file_url;
                } else {
                    taskFileSection.style.display = 'none';
                }

                const submissionSection = document.getElementById('submissionSection');
                const submitSection = document.getElementById('submitSection');

                if (task.status === 'selesai') {
                    submissionSection.style.display = 'block';
                    submitSection.style.display = 'none';
                    document.getElementById('submissionStatus').textContent = 'Sudah dikumpulkan';
                    document.getElementById('submissionDate').textContent = 'Waktu pengumpulan: ' + new Date(task.submitted_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    document.getElementById('submissionGrade').textContent = task.grade ? `Nilai: ${task.grade}/100` : 'Nilai: Belum dinilai';
                } else {
                    submissionSection.style.display = 'none';
                    submitSection.style.display = 'block';
                    clearFile();
                    document.getElementById('linkInput').value = '';
                }

                $('#taskModal').modal('show');
            },
            error: function(xhr) {
                alert('Error loading task details: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function clearFile() {
        document.getElementById('fileInput').value = '';
        document.getElementById('fileName').style.display = 'none';
        document.getElementById('selectedFile').textContent = '';
    }

    function submitTask() {
        const submissionType = $('input[name="submission_type"]:checked').val();
        const formData = new FormData();
        
        formData.append('submission_type', submissionType);
        formData.append('_token', '{{ csrf_token() }}');

        if (submissionType === 'file') {
            const fileInput = document.getElementById('fileInput');
            if (!fileInput.files.length) {
                alert('Silakan pilih file terlebih dahulu');
                return;
            }
            formData.append('file', fileInput.files[0]);
        } else {
            const link = document.getElementById('linkInput').value.trim();
            if (!link) {
                alert('Silakan masukkan link terlebih dahulu');
                return;
            }
            try {
                new URL(link);
            } catch (e) {
                alert('Format link tidak valid. Pastikan dimulai dengan http:// atau https://');
                return;
            }
            formData.append('link', link);
        }

        // Show loading
        const submitBtn = event.target;
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';

        $.ajax({
            url: `/peserta/room/task/${currentTaskId}/submit`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Tugas berhasil dikumpulkan!');
                $('#taskModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                let errorMsg = 'Gagal mengumpulkan tugas';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorMsg = 'Data tidak valid. Periksa kembali input Anda.';
                }
                alert('Error: ' + errorMsg);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    $(document).ready(function() {
        // ===== HANDLE HASH URL UNTUK TAB =====
        function openTabFromHash() {
            const hash = window.location.hash;
            if (hash) {
                const tabLink = $(`a[href="${hash}"]`);
                if (tabLink.length) {
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    tabLink.addClass('active');
                    $(hash).addClass('show active');
                }
            }
        }
        
        openTabFromHash();
        $(window).on('hashchange', openTabFromHash);
        // ===== END HANDLE HASH =====

        // Auto-open modal dari dashboard
        const urlParams = new URLSearchParams(window.location.search);
        const openTaskId = urlParams.get('open_task');
        if (openTaskId) {
            setTimeout(function() {
                openTaskDetail(openTaskId);
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 500);
        }

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileNameDiv = document.getElementById('fileName');
        const selectedFileSpan = document.getElementById('selectedFile');

        // Toggle submission type
        $('input[name="submission_type"]').change(function() {
            if ($(this).val() === 'file') {
                $('#fileSection').show();
                $('#linkSection').hide();
            } else {
                $('#fileSection').hide();
                $('#linkSection').show();
            }
        });

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

        // Filter tasks
        $('.filter-btn').click(function() {
            $('.filter-btn').removeClass('active btn-primary btn-success btn-warning btn-danger')
                .addClass('btn-outline-primary btn-outline-success btn-outline-warning btn-outline-danger');
            
            const status = $(this).data('status');
            
            if (status === 'all') {
                $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
                $('.task-item').show();
            } else {
                if (status === 'selesai') {
                    $(this).removeClass('btn-outline-success').addClass('btn-success active');
                } else if (status === 'pending') {
                    $(this).removeClass('btn-outline-warning').addClass('btn-warning active');
                } else if (status === 'terlambat') {
                    $(this).removeClass('btn-outline-danger').addClass('btn-danger active');
                }
                
                $('.task-item').hide();
                $(`.task-item[data-status="${status}"]`).show();
            }
        });
    });
</script>
@endpush

@endsection