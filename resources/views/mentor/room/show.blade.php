@extends('layout.app')

@section('konten')
<div class="container-fluid px-4 py-4">
    <!-- Header Room -->
    <div class="card shadow-sm border-0 mb-4 bg-gradient-primary">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="icon-box me-3">
                    <i class="bi bi-collection-fill"></i>
                </div>
                <div>
                    <h2 class="text-white mb-1 fw-bold">{{ $room->nama_room }}</h2>
                    <p class="text-white-50 mb-0">{{ $room->deskripsi }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-lg-8">
            <!-- Form Tambah Task -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-plus-circle text-primary me-2"></i>
                        Tambah Task Baru
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="taskForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Judul Task</label>
                            <input type="text" class="form-control form-control-lg" id="taskJudul" name="judul" placeholder="Masukkan judul task..." required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Deskripsi</label>
                            <textarea class="form-control" id="taskDeskripsi" name="deskripsi" rows="4" placeholder="Jelaskan detail task..." required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Deadline</label>
                            <input type="datetime-local" class="form-control form-control-lg" id="taskDeadline" name="deadline" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">File Lampiran (Opsional)</label>
                            <input type="file" class="form-control" id="taskFile" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar">
                            <small class="text-muted">Format: PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR. Maks 10MB</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Task
                        </button>
                    </form>
                </div>
            </div>

            <!-- List Task Aktif -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success-soft border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="bi bi-list-task me-2"></i>
                        Daftar Task Aktif
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="taskListActive">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Task Kadaluarsa -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger-soft border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-danger">
                        <i class="bi bi-clock-history me-2"></i>
                        Task Kadaluarsa
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="taskListExpired">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-lg-4">
            <!-- Materi Terkait -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info-soft border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-info">
                        <i class="bi bi-book-half me-2"></i>
                        Materi Terkait
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if($room->materis->count() > 0)
                        @foreach($room->materis as $materi)
                        <a href="{{ route('mentor.materiView', $materi->materi_id) }}" class="text-decoration-none">
                            <div class="materi-item p-3 mb-2 rounded-3 bg-light">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-info me-3">
                                        <i class="bi bi-file-earmark-text text-white"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold text-dark">{{ $materi->judul }}</h6>
                                        <small class="text-muted">{{ $materi->getFileType() ?? 'Dokumen' }}</small>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada materi</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Peserta -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success-soft border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold text-success d-flex align-items-center">
                        <i class="bi bi-people-fill me-2"></i>
                        <span>Peserta Room</span>
                        <span class="badge bg-success text-white ms-2" id="participantCount">0</span>
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div id="participantList">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 small">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengumuman -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning-soft border-0 pt-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-warning">
                            <i class="bi bi-megaphone-fill me-2"></i>
                            Pengumuman
                        </h5>
                        <button class="btn btn-sm btn-warning" onclick="$('#modalTambahPengumuman').modal('show')">
                            <i class="bi bi-plus-circle me-1"></i>Tambah
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div id="pengumumanList">
                        <div class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 small">Memuat data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Task -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="taskDetailJudul"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-file-text text-primary me-2"></i>Deskripsi
                    </h6>
                    <p class="text-muted mb-0" id="taskDetailDeskripsi"></p>
                </div>
                
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-calendar-check text-primary me-2"></i>Deadline
                    </h6>
                    <p class="text-muted mb-0" id="taskDetailDeadline"></p>
                </div>
                
                <div class="mb-4" id="taskDetailFileContainer" style="display: none;">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-paperclip text-primary me-2"></i>File Lampiran
                    </h6>
                    <a id="taskDetailFile" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i>Download File
                    </a>
                </div>
                
                <div>
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-send-check text-primary me-2"></i>Submissions
                        <span class="badge bg-primary ms-1" id="submissionCount">0</span>
                    </h6>
                    <div id="submissionList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Task -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Edit Task</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editTaskForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editTaskId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Task</label>
                        <input type="text" class="form-control" id="editTaskJudul" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea class="form-control" id="editTaskDeskripsi" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deadline</label>
                        <input type="datetime-local" class="form-control" id="editTaskDeadline" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Lampiran Saat Ini</label>
                        <div id="currentFileDisplay"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ganti File (Opsional)</label>
                        <input type="file" class="form-control" id="editTaskFile" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Beri Nilai - TAMBAHAN BARU -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-star-fill me-2"></i>Beri Nilai
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Info Peserta -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="submission-avatar" id="gradeStudentAvatar" style="width: 48px; height: 48px;">A</div>
                        <div>
                            <h6 class="mb-0 fw-bold" id="gradeStudentName">Nama Peserta</h6>
                            <small class="text-muted" id="gradeSubmittedAt">Submitted at</small>
                        </div>
                    </div>
                </div>

                <form id="gradeForm">
                    @csrf
                    <input type="hidden" id="gradeSubmissionId">
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold" id="gradeLabelNilai">
                            <i class="bi bi-trophy text-warning me-2"></i>Nilai (0-100)
                        </label>
                        <input type="number" 
                            class="form-control form-control-lg text-center" 
                            id="gradeNilai" 
                            name="nilai"
                            min="0" 
                            max="100" 
                            required
                            placeholder="0"
                            style="font-size: 24px; font-weight: bold;">
                        <div class="invalid-feedback" id="gradeNilaiFeedback">
                            Nilai harus antara 0-100
                        </div>
                    </div>
                                        
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-chat-square-quote text-info me-2"></i>Feedback (Opsional)
                        </label>
                        <textarea class="form-control" 
                                  id="gradeFeedback" 
                                  name="feedback"
                                  rows="4" 
                                  placeholder="Berikan feedback untuk peserta..."
                                  maxlength="1000"></textarea>
                        <small class="text-muted">Maksimal 1000 karakter</small>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-check-circle me-2"></i>Simpan Nilai
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Include Modals --}}
@include('mentor.room.modals.createPengumuman')
@include('mentor.room.modals.editPengumuman')

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    /* Soft Colors */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    }
    
    .bg-success-soft {
        background-color: #d1fae5;
    }
    
    .bg-danger-soft {
        background-color: #fee2e2;
    }
    
    .bg-info-soft {
        background-color: #dbeafe;
    }

    .submission-item {
    background: #f9fafb;
    transition: all 0.2s ease;
    }

    .submission-item:hover {
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
        
    /* Card Enhancement */
    .card {
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    /* Icon Box */
    .icon-box {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }
    
    /* Icon Circle */
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    /* Form Control */
    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    /* Button */
    .btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border: none;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }
    
    /* Materi Item */
    .materi-item {
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .materi-item:hover {
        background-color: white !important;
        border-color: #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    /* Task Card */
    .task-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        border-left: 4px solid #6366f1;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    
    .task-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(4px);
    }
    
    .task-card.expired {
        border-left-color: #ef4444;
        opacity: 0.9;
    }
    
    .task-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .task-description {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 12px;
    }
    
    .task-meta {
        display: flex;
        gap: 16px;
        margin-bottom: 12px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #6b7280;
    }
    
    .task-meta i {
        color: #6366f1;
    }
    
    /* Participant Item */
    .participant-item {
        padding: 12px;
        background: #f9fafb;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .participant-item:hover {
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateX(4px);
    }
    
    .participant-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }
    
    /* Submission Item */
    .submission-item {
        padding: 12px;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }
    
    .empty-state i {
        font-size: 48px;
        opacity: 0.3;
        margin-bottom: 12px;
    }
    
    /* Modal */
    .modal-content {
        border-radius: 16px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .icon-box {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }
    }
    /* Submission Card Styles */
.submission-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    transition: all 0.2s ease;
}

.submission-card:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    transform: translateY(-2px);
}

.submission-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    gap: 12px;
}

.submission-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    flex-shrink: 0;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
}

.status-success {
    background-color: #d1fae5;
    color: #065f46;
}

.status-warning {
    background-color: #fef3c7;
    color: #92400e;
}

.status-info {
    background-color: #dbeafe;
    color: #1e40af;
}

.submission-info {
    background: #f9fafb;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 8px;
    font-size: 14px;
}

.info-item:last-child {
    margin-bottom: 0;
}

.info-label {
    font-weight: 600;
    color: #374151;
    margin-right: 6px;
}

.info-value {
    color: #6b7280;
    flex: 1;
}

.submission-action {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.submission-action .btn {
    flex: 1;
    min-width: 120px;
}

.bg-warning-soft {
    background-color: #fef3c7;
}

.pengumuman-item {
    padding: 12px;
    background: #f9fafb;
    border-radius: 10px;
    margin-bottom: 10px;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.pengumuman-item.penting {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.pengumuman-item:hover {
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.pengumuman-item.expired {
    opacity: 0.6;
}

/* Responsive untuk submission */
@media (max-width: 576px) {
    .submission-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .status-badge {
        align-self: flex-start;
    }
    
    .submission-avatar {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .submission-action {
        flex-direction: column;
    }
    
    .submission-action .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>

$(document).ready(function() {
    const roomId = {{ $room->room_id }};
    
    // Load data awal
    loadParticipants();
    loadTasks();
    loadPengumuman();

    // ============ PENGUMUMAN MANAGEMENT ============
    function loadPengumuman() {
        $.ajax({
            url: `/mentor/room/${roomId}/pengumuman`,
            method: 'GET',
            success: function(pengumumanList) {
                if (pengumumanList.length === 0) {
                    $('#pengumumanList').html(`
                        <div class="text-center py-4">
                            <i class="bi bi-megaphone display-4 text-muted opacity-25"></i>
                            <p class="text-muted mt-2 mb-0 small">Belum ada pengumuman</p>
                        </div>
                    `);
                    return;
                }
                
                let html = '';
                pengumumanList.slice(0, 3).forEach(function(item) {
                    const pentingClass = item.is_penting ? 'penting' : '';
                    const expiredClass = !item.is_aktif ? 'expired' : '';
                    const pentingBadge = item.is_penting ? '<span class="badge bg-danger text-white ms-1" style="font-size: 9px;">PENTING</span>' : '';
                    
                    html += `
                        <div class="pengumuman-item ${pentingClass} ${expiredClass}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px;">
                                    ${item.judul}${pentingBadge}
                                </h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-pengumuman" href="#" data-id="${item.pengumuman_id}">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger delete-pengumuman" href="#" data-id="${item.pengumuman_id}">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p class="text-muted mb-1 small" style="font-size: 12px;">${item.isi.substring(0, 80)}${item.isi.length > 80 ? '...' : ''}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-clock me-1"></i>${item.created_at}
                                </small>
                                ${!item.is_aktif ? '<span class="badge bg-secondary" style="font-size: 9px;">Kadaluarsa</span>' : `<span class="badge bg-success" style="font-size: 9px;">${item.durasi_text}</span>`}
                            </div>
                        </div>
                    `;
                });
                
                if (pengumumanList.length > 3) {
                    html += `<p class="text-center text-muted small mb-0">+${pengumumanList.length - 3} pengumuman lainnya</p>`;
                }
                
                $('#pengumumanList').html(html);
            },
            error: function() {
                $('#pengumumanList').html(`
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                        <p class="text-muted mt-2 mb-0 small">Gagal memuat pengumuman</p>
                    </div>
                `);
            }
        });
    }

    // Submit create pengumuman
    $('#formTambahPengumuman').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        $.ajax({
            url: `/mentor/room/${roomId}/pengumuman`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
            },
            success: function(response) {
                $('#modalTambahPengumuman').modal('hide');
                $('#formTambahPengumuman')[0].reset();
                showToast('success', response.message);
                loadPengumuman();
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan';
                
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                showToast('error', errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Edit pengumuman
    $(document).on('click', '.edit-pengumuman', function(e) {
        e.preventDefault();
        const pengumumanId = $(this).data('id');
        
        $.ajax({
            url: `/mentor/room/${roomId}/pengumuman`,
            method: 'GET',
            success: function(pengumumanList) {
                const pengumuman = pengumumanList.find(p => p.pengumuman_id === pengumumanId);
                
                if (pengumuman) {
                    $('#editPengumumanId').val(pengumuman.pengumuman_id);
                    $('#editJudul').val(pengumuman.judul);
                    $('#editIsi').val(pengumuman.isi);
                    $('#editDurasi').val(pengumuman.durasi_tampil);
                    $('#editIsPenting').prop('checked', pengumuman.is_penting);
                    
                    $('#editPengumumanModal').modal('show');
                }
            }
        });
    });

    // Submit edit pengumuman
    $('#editPengumumanForm').on('submit', function(e) {
        e.preventDefault();
        
        const pengumumanId = $('#editPengumumanId').val();
        const formData = new FormData();
        
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('judul', $('#editJudul').val());
        formData.append('isi', $('#editIsi').val());
        formData.append('durasi_tampil', $('#editDurasi').val());
        formData.append('is_penting', $('#editIsPenting').is(':checked') ? 1 : 0);
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        $.ajax({
            url: `/mentor/room/${roomId}/pengumuman/${pengumumanId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
            },
            success: function(response) {
                $('#editPengumumanModal').modal('hide');
                showToast('success', response.message);
                loadPengumuman();
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                showToast('error', errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Delete pengumuman
    $(document).on('click', '.delete-pengumuman', function(e) {
        e.preventDefault();
        const pengumumanId = $(this).data('id');
        
        if (!confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')) {
            return;
        }
        
        $.ajax({
            url: `/mentor/room/${roomId}/pengumuman/${pengumumanId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                showToast('success', response.message);
                loadPengumuman();
            },
            error: function() {
                showToast('error', 'Gagal menghapus pengumuman');
            }
        });
    });

    // ============ TASK MANAGEMENT ============
    
    // Submit form task baru
    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        $.ajax({
            url: `/mentor/room/${roomId}/tasks`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
            },
            success: function(response) {
                $('#taskForm')[0].reset();
                showToast('success', response.message);
                loadTasks();
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showToast('error', errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Submit form edit task
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        const taskId = $('#editTaskId').val();
        const formData = new FormData();
        
        formData.append('_method', 'PUT');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('judul', $('#editTaskJudul').val());
        formData.append('deskripsi', $('#editTaskDeskripsi').val());
        formData.append('deadline', $('#editTaskDeadline').val());
        
        const fileInput = document.getElementById('editTaskFile');
        if (fileInput.files.length > 0) {
            formData.append('file', fileInput.files[0]);
        }
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        $.ajax({
            url: `/mentor/room/${roomId}/tasks/${taskId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
            },
            success: function(response) {
                $('#editTaskModal').modal('hide');
                showToast('success', response.message);
                loadTasks();
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan';
                if (xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                showToast('error', errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Load participants
    function loadParticipants() {
        $.ajax({
            url: `/mentor/room/${roomId}/participants`,
            method: 'GET',
            success: function(participants) {
                $('#participantCount').text(participants.length);
                
                if (participants.length === 0) {
                    $('#participantList').html(`
                        <div class="empty-state">
                            <i class="bi bi-people d-block"></i>
                            <p class="mb-0 small">Belum ada peserta</p>
                        </div>
                    `);
                    return;
                }
                
                let html = '';
                participants.forEach(function(participant) {
                    const detailUrl = `/mentor/room/${roomId}/participant/${participant.id}`;
                    html += `
                        <a href="${detailUrl}" class="text-decoration-none">
                            <div class="participant-item">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="participant-avatar">${participant.nama.charAt(0).toUpperCase()}</div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold text-dark">${participant.nama}</h6>
                                        <small class="text-muted">@${participant.username}</small><br>
                                        <small class="text-muted">Bergabung: ${participant.joined_at}</small>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </a>
                    `;
                });
                
                $('#participantList').html(html);
            },
            error: function() {
                $('#participantList').html(`
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle d-block"></i>
                        <p class="mb-0 small">Gagal memuat peserta</p>
                    </div>
                `);
            }
        });
    }
    
    // Load tasks
    function loadTasks() {
        $.ajax({
            url: `/mentor/room/${roomId}/tasks`,
            method: 'GET',
            success: function(response) {
                // Render active tasks
                if (response.active.length === 0) {
                    $('#taskListActive').html(`
                        <div class="empty-state">
                            <i class="bi bi-inbox d-block"></i>
                            <p class="mb-0">Belum ada task aktif</p>
                        </div>
                    `);
                } else {
                    let html = '';
                    response.active.forEach(function(task) {
                        html += renderTaskCard(task, false);
                    });
                    $('#taskListActive').html(html);
                }
                
                // Render expired tasks
                if (response.expired.length === 0) {
                    $('#taskListExpired').html(`
                        <div class="empty-state">
                            <i class="bi bi-inbox d-block"></i>
                            <p class="mb-0">Tidak ada task kadaluarsa</p>
                        </div>
                    `);
                } else {
                    let html = '';
                    response.expired.forEach(function(task) {
                        html += renderTaskCard(task, true);
                    });
                    $('#taskListExpired').html(html);
                }
            },
            error: function() {
                $('#taskListActive').html(`
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle d-block"></i>
                        <p class="mb-0">Gagal memuat task</p>
                    </div>
                `);
                $('#taskListExpired').html(`
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle d-block"></i>
                        <p class="mb-0">Gagal memuat task</p>
                    </div>
                `);
            }
        });
    }
    
    // Render task card
    function renderTaskCard(task, isExpired) {
        const badgeClass = isExpired ? 'bg-danger' : 'bg-success';
        const cardClass = isExpired ? 'expired' : '';
        
        // Kalau task expired, button edit dihilangkan
        const editButton = isExpired ? '' : `
            <button class="btn btn-sm btn-outline-warning edit-task" data-task-id="${task.id}">
                <i class="bi bi-pencil me-1"></i>Edit
            </button>
        `;
        
        return `
            <div class="task-card ${cardClass}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="task-title mb-0">${task.judul}</h5>
                    <span class="badge ${badgeClass}">${task.total_submissions} submissions</span>
                </div>
                <p class="task-description">${task.deskripsi.substring(0, 120)}${task.deskripsi.length > 120 ? '...' : ''}</p>
                <div class="task-meta">
                    <span><i class="bi bi-calendar-event me-1"></i>${task.deadline}</span>
                    ${task.file_name ? `<span><i class="bi bi-paperclip me-1"></i>${task.file_name}</span>` : ''}
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-primary view-task" data-task-id="${task.id}">
                        <i class="bi bi-eye me-1"></i>Detail
                    </button>
                    ${editButton}
                    <button class="btn btn-sm btn-outline-danger delete-task" data-task-id="${task.id}">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        `;
    }
    
    // View task detail - DENGAN TOMBOL BERI NILAI
    $(document).on('click', '.view-task', function() {
        const taskId = $(this).data('task-id');
        
        // Simpan task ID untuk reload nanti
        $('#taskDetailModal').data('task-id', taskId);
        
        $.ajax({
            url: `/mentor/room/${roomId}/tasks`,
            method: 'GET',
            success: function(response) {
                const allTasks = [...response.active, ...response.expired];
                const task = allTasks.find(t => t.id === taskId);
                
                if (task) {
                    $('#taskDetailJudul').text(task.judul);
                    $('#taskDetailDeskripsi').text(task.deskripsi);
                    $('#taskDetailDeadline').text(task.deadline);
                    $('#submissionCount').text(task.total_submissions);
                    
                    // Show file if exists
                    if (task.file_path) {
                        $('#taskDetailFile').attr('href', `/storage/${task.file_path}`);
                        $('#taskDetailFileContainer').show();
                    } else {
                        $('#taskDetailFileContainer').hide();
                    }
                    
                    // Render submissions dengan tombol nilai
                    if (task.submissions.length === 0) {
                        $('#submissionList').html(`
                            <div class="text-center py-4">
                                <i class="bi bi-inbox display-5 text-muted opacity-25"></i>
                                <p class="text-muted mt-3 mb-0">Belum ada yang mengumpulkan</p>
                            </div>
                        `);
                    } else {
                        let submissionHtml = '';
                        task.submissions.forEach(function(submission) {
                            const statusConfig = {
                                'graded': { color: 'success', icon: 'check-circle-fill', text: 'Dinilai' },
                                'late': { color: 'warning', icon: 'clock-fill', text: 'Terlambat' },
                                'pending': { color: 'info', icon: 'hourglass-split', text: 'Pending' }
                            };
                            
                            const status = statusConfig[submission.status] || statusConfig['pending'];
                            
                            // Tombol download atau link
                            let actionButtons = '';
                            if (submission.file_path) {
                                actionButtons += `
                                    <a href="/mentor/room/${roomId}/submission/${submission.submission_id}/download" 
                                       class="btn btn-sm btn-outline-primary" 
                                       target="_blank">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                `;
                            } else if (submission.link) {
                                actionButtons += `
                                    <a href="${submission.link}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       target="_blank" 
                                       rel="noopener">
                                        <i class="bi bi-link-45deg me-1"></i>Buka Link
                                    </a>
                                `;
                            }
                            
                            // Tombol beri nilai - BAGIAN PENTING!
                            const gradeButtonText = submission.nilai ? 
                                `<i class="bi bi-pencil me-1"></i>Edit Nilai (${submission.nilai})` : 
                                `<i class="bi bi-star me-1"></i>Beri Nilai`;
                            
                            actionButtons += `
                                <button class="btn btn-sm btn-success btn-grade-submission"
                                        data-submission-id="${submission.submission_id}"
                                        data-student-name="${submission.user_nama}"
                                        data-submitted-at="${submission.submitted_at}"
                                        data-current-nilai="${submission.nilai || ''}"
                                        data-current-feedback="${submission.feedback || ''}"
                                        data-status="${submission.status}"> 
                                    ${gradeButtonText}
                                </button>
                            `;
                            
                            // Info sections
                            let infoSections = '';
                            
                            if (submission.catatan) {
                                infoSections += `
                                    <div class="info-item">
                                        <i class="bi bi-chat-left-text text-primary me-2"></i>
                                        <span class="info-label">Catatan:</span>
                                        <span class="info-value">${submission.catatan}</span>
                                    </div>
                                `;
                            }
                            
                            if (submission.nilai) {
                                infoSections += `
                                    <div class="info-item">
                                        <i class="bi bi-star-fill text-warning me-2"></i>
                                        <span class="info-label">Nilai:</span>
                                        <span class="info-value fw-bold text-success">${submission.nilai}/100</span>
                                    </div>
                                `;
                            }
                            
                            if (submission.feedback) {
                                infoSections += `
                                    <div class="info-item">
                                        <i class="bi bi-chat-square-quote text-info me-2"></i>
                                        <span class="info-label">Feedback:</span>
                                        <span class="info-value">${submission.feedback}</span>
                                    </div>
                                `;
                            }
                            
                            submissionHtml += `
                                <div class="submission-card">
                                    <div class="submission-header">
                                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                                            <div class="submission-avatar">
                                                ${submission.user_nama.charAt(0).toUpperCase()}
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark">${submission.user_nama}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>${submission.submitted_at}
                                                </small>
                                            </div>
                                        </div>
                                        <span class="status-badge status-${status.color}">
                                            <i class="bi bi-${status.icon} me-1"></i>${status.text}
                                        </span>
                                    </div>
                                    
                                    ${infoSections ? `<div class="submission-info">${infoSections}</div>` : ''}
                                    
                                    <div class="submission-action">
                                        ${actionButtons}
                                    </div>
                                </div>
                            `;
                        });
                        $('#submissionList').html(submissionHtml);
                    }
                    
                    $('#taskDetailModal').modal('show');
                }
            }
        });
    });

    // Edit task
    $(document).on('click', '.edit-task', function() {
        const taskId = $(this).data('task-id');
        
        $.ajax({
            url: `/mentor/room/${roomId}/tasks`,
            method: 'GET',
            success: function(response) {
                const allTasks = [...response.active, ...response.expired];
                const task = allTasks.find(t => t.id === taskId);
                
                if (task) {
                    $('#editTaskId').val(task.id);
                    $('#editTaskJudul').val(task.judul);
                    $('#editTaskDeskripsi').val(task.deskripsi);
                    
                    // Format deadline for datetime-local input
                    const deadlineDate = new Date(task.deadline_raw);
                    const formattedDeadline = deadlineDate.toISOString().slice(0, 16);
                    $('#editTaskDeadline').val(formattedDeadline);
                    
                    // Display current file
                    if (task.file_path) {
                        $('#currentFileDisplay').html(`
                            <div class="alert alert-info py-2 mb-0">
                                <i class="bi bi-paperclip me-1"></i> ${task.file_name}
                                <a href="/storage/${task.file_path}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        `);
                    } else {
                        $('#currentFileDisplay').html('<p class="text-muted mb-0 small">Tidak ada file</p>');
                    }
                    
                    $('#editTaskModal').modal('show');
                }
            }
        });
    });
    
    // Delete task
    $(document).on('click', '.delete-task', function() {
        const taskId = $(this).data('task-id');
        
        if (!confirm('Apakah Anda yakin ingin menghapus task ini?')) {
            return;
        }
        
        $.ajax({
            url: `/mentor/room/${roomId}/tasks/${taskId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                showToast('success', response.message);
                loadTasks();
            },
            error: function() {
                showToast('error', 'Gagal menghapus task');
            }
        });
    });

    // ============ GRADING SYSTEM ============
    
    // Event handler untuk tombol "Beri Nilai" - UPDATE BAGIAN INI
$(document).on('click', '.btn-grade-submission', function() {
    const submissionId = $(this).data('submission-id');
    const studentName = $(this).data('student-name');
    const submittedAt = $(this).data('submitted-at');
    const currentNilai = $(this).data('current-nilai') || '';
    const currentFeedback = $(this).data('current-feedback') || '';
    const status = $(this).data('status');
    
    // Set data ke modal
    $('#gradeSubmissionId').val(submissionId);
    $('#gradeStudentName').text(studentName);
    $('#gradeSubmittedAt').text('Dikumpulkan: ' + submittedAt);
    $('#gradeStudentAvatar').text(studentName.charAt(0).toUpperCase());
    
    // LOGIC UNTUK STATUS LATE
    const isLate = status === 'late';
    const maxNilai = isLate ? 85 : 100;
    
    // UPDATE LABEL DAN ATRIBUT
    $('#gradeLabelNilai').html(`
        <i class="bi bi-trophy text-warning me-2"></i>Nilai (0-${maxNilai})
    `);
    
    // Update atribut max pada input
    $('#gradeNilai').attr('max', maxNilai);
    
    // Update invalid feedback
    $('#gradeNilaiFeedback').text(`Nilai harus antara 0-${maxNilai}`);
    
    // ===== WARNING JIKA TERLAMBAT =====
    if (isLate) {
        if ($('#lateWarning').length === 0) {
            $('#gradeNilai').closest('.mb-4').prepend(`
                <div class="alert alert-warning mb-3" id="lateWarning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Submission Terlambat!</strong>
                    Nilai maksimal adalah <strong>85</strong>.
                </div>
            `);
        }
    } else {
        $('#lateWarning').remove();
    }

    // Feedback
    $('#gradeFeedback').val(currentFeedback);

    // Reset validation
    $('#gradeForm').removeClass('was-validated');
    $('#gradeNilai').removeClass('is-invalid');

    // Show modal
    $('#gradeModal').modal('show');
});

// REAL-TIME VALIDATION SAAT INPUT
$('#gradeNilai').on('input', function() {
    const nilai = parseInt($(this).val());
    const maxNilai = parseInt($(this).attr('max'));
    const submitBtn = $('#gradeForm button[type="submit"]');
    
    if (nilai > maxNilai) {
        $(this).addClass('is-invalid');
        submitBtn.prop('disabled', true);
        
        // Update feedback message
        $('#gradeNilaiFeedback').text(`Nilai maksimal untuk submission ini adalah ${maxNilai}`).addClass('d-block');
    } else if (nilai < 0 || isNaN(nilai)) {
        $(this).addClass('is-invalid');
        submitBtn.prop('disabled', true);
        
        $('#gradeNilaiFeedback').text(`Nilai harus antara 0 dan ${maxNilai}`).addClass('d-block');
    } else {
        $(this).removeClass('is-invalid');
        $('#gradeNilaiFeedback').removeClass('d-block');
        submitBtn.prop('disabled', false);
    }
});

// Reset form saat modal ditutup
$('#gradeModal').on('hidden.bs.modal', function() {
    $('#gradeForm')[0].reset();
    $('#gradeForm').removeClass('was-validated');
    $('#gradeNilai').removeClass('is-invalid');
    $('#lateWarning').remove();
    $('#gradeNilaiFeedback').removeClass('d-block');
    $('#gradeForm button[type="submit"]').prop('disabled', false);
    
    // RESET LABEL KE DEFAULT
    $('#gradeLabelNilai').html(`
        <i class="bi bi-trophy text-warning me-2"></i>Nilai (0-100)
    `);
    $('#gradeNilai').attr('max', 100);
    $('#gradeNilaiFeedback').text('Nilai harus antara 0-100');
});

    // Submit form penilaian
    $('#gradeForm').on('submit', function(e) {
        e.preventDefault();
        
        const submissionId = $('#gradeSubmissionId').val();
        const nilai = parseInt($('#gradeNilai').val());
        
        // Validasi nilai
        if (nilai < 0 || nilai > 100 || isNaN(nilai)) {
            $('#gradeNilai').addClass('is-invalid');
            return;
        }
        
        $('#gradeNilai').removeClass('is-invalid');
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        $.ajax({
            url: `/mentor/room/${roomId}/submission/${submissionId}/grade`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'
                );
            },
            success: function(response) {
                $('#gradeModal').modal('hide');
                showToast('success', response.message);
                
                // Reload tasks untuk update UI
                loadTasks();
                
                // Jika modal detail task sedang terbuka, reload juga
                if ($('#taskDetailModal').hasClass('show')) {
                    const activeTaskId = $('#taskDetailModal').data('task-id');
                    if (activeTaskId) {
                        setTimeout(() => {
                            $(`.view-task[data-task-id="${activeTaskId}"]`).trigger('click');
                        }, 300);
                    }
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan';
                
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                showToast('error', errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Reset form saat modal ditutup
    $('#gradeModal').on('hidden.bs.modal', function() {
        $('#gradeForm')[0].reset();
        $('#gradeForm').removeClass('was-validated');
        $('#gradeNilai').removeClass('is-invalid');
    });
    
    // ============ TOAST NOTIFICATION ============
    function showToast(type, message) {
        const bgColor = type === 'success' ? '#10b981' : '#ef4444';
        const icon = type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
        
        const toast = $(`
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show" role="alert">
                    <div class="toast-header" style="background-color: ${bgColor}; color: white; border: none;">
                        <i class="bi bi-${icon} me-2"></i>
                        <strong class="me-auto">${type === 'success' ? 'Berhasil' : 'Error'}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(function() {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
});

</script>
@endpush