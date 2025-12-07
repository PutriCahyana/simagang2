@extends('layout.app')

@section('konten')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="mb-1">👋 Selamat Datang, {{ $user->nama }}!</h4>
            <p class="text-muted mb-2">
                @if($room)
                    {{ $room->nama_room }} · Week {{ $currentWeek }} of {{ $totalWeeks }}
                    @if($room->mentor && $room->mentor->user)
                        · Mentor: {{ $room->mentor->user->nama }}
                    @endif
                @else
                    Belum tergabung di room manapun
                @endif
            </p>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="text-muted">Progress Magang: {{ $progress }}%</small>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="fs-1 mb-2">📝</div>
                    <h5 class="card-title">Logbook</h5>
                    <h3 class="mb-0">{{ $totalLogbooks }}/{{ $workDays }}</h3>
                    <small class="text-muted">Hari Terisi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="fs-1 mb-2">✅</div>
                    <h5 class="card-title">Tasks</h5>
                    <h3 class="mb-0 text-warning">{{ $pendingTasks }}</h3>
                    <small class="text-muted">Pending · {{ $completedTasks }} Done</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="fs-1 mb-2">📅</div>
                    <h5 class="card-title">Kehadiran</h5>
                    <h3 class="mb-0 text-success">{{ $attendancePercentage }}%</h3>
                    <small class="text-muted">{{ $attendanceDays }}/{{ $workDays }} Hari</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="fs-1 mb-2">⭐</div>
                    <h5 class="card-title">Evaluasi</h5>
                    <h3 class="mb-0 text-primary">{{ $averageScore }}/100</h3>
                    <small class="text-muted">Rata-rata Nilai</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
            <!-- Daily Logbook Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📋 Logbook Hari Ini - {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</h5>
                </div>
                <div class="card-body">
                    @if($todayLogbook)
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <span class="me-2">✅</span>
                            <div>
                                <strong>Status: Sudah Diisi</strong>
                                <p class="mb-0 small">
                                    @if($todayLogbook->is_approved)
                                        Logbook telah disetujui oleh mentor
                                    @else
                                        Menunggu persetujuan dari mentor
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('peserta.logbook.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye"></i> Lihat Logbook Hari Ini
                        </a>
                    @else
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <span class="me-2">❌</span>
                            <div>
                                <strong>Status: Belum Diisi</strong>
                                <p class="mb-0 small">Jangan lupa mengisi logbook aktivitas kamu hari ini!</p>
                            </div>
                        </div>
                        <a href="{{ route('peserta.logbook.create') }}" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-pencil-square"></i> Isi Logbook Sekarang →
                        </a>
                    @endif
                </div>
            </div>

            <!-- Tasks Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">✅ Tugas Dari Mentor</h5>
                    <span class="badge bg-primary">{{ $pendingTasks }} Active</span>
                </div>
                <div class="card-body">
                    @if($tasks->count() > 0)
                        @foreach($tasks as $task)
                            @php
                                $isOverdue = $task->deadline < now();
                                $isToday = $task->deadline->isToday();
                                $isTomorrow = $task->deadline->isTomorrow();
                                
                                if ($isOverdue) {
                                    $borderColor = 'danger';
                                    $badgeColor = 'danger';
                                    $badgeText = 'OVERDUE';
                                } elseif ($isToday) {
                                    $borderColor = 'danger';
                                    $badgeColor = 'danger';
                                    $badgeText = 'DEADLINE HARI INI';
                                } elseif ($isTomorrow) {
                                    $borderColor = 'warning';
                                    $badgeColor = 'warning';
                                    $badgeText = 'BESOK';
                                } else {
                                    $borderColor = 'warning';
                                    $badgeColor = 'warning';
                                    $badgeText = 'IN PROGRESS';
                                }
                            @endphp
                            
                            <div class="border-start border-{{ $borderColor }} border-4 ps-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-{{ $badgeColor }} text-white mb-1 rounded-pill" style="font-weight: 600; padding: 0.4em 0.8em;">{{ $badgeText }}</span>
                                        <h6 class="mb-1">{{ $task->judul }}</h6>
                                        <small class="text-muted">Deadline: {{ $task->deadline->isoFormat('dddd, D MMM YYYY · HH:mm') }}</small>
                                    </div>
                                </div>
                                @if($task->deskripsi)
                                    <p class="small mb-2 text-muted">{{ Str::limit($task->deskripsi, 100) }}</p>
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('peserta.room.show', $task->room_id) }}?open_task={{ $task->task_id }}" class="btn btn-sm btn-primary">Lihat & Submit</a>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('peserta.logbook.index') }}" class="btn btn-outline-secondary">Lihat Semua Tugas →</a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="fs-1 mb-2">🎉</div>
                            <p class="text-muted mb-0">Tidak ada tugas yang perlu dikerjakan saat ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Recent Logbook Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📚 Logbook Terakhir</h5>
                </div>
                <div class="card-body">
                    @if($recentLogbooks->count() > 0)
                        @foreach($recentLogbooks as $logbook)
                            <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="{{ $logbook->is_approved ? 'text-success' : 'text-warning' }}">
                                        {{ $logbook->is_approved ? '✅' : '⏳' }} 
                                        {{ $logbook->date->isoFormat('ddd, D MMM') }}
                                    </strong>
                                    <span class="badge bg-{{ $logbook->is_approved ? 'success' : 'warning' }} text-white rounded-pill" style="font-weight: 600; padding: 0.4em 0.8em;">
                                        {{ $logbook->is_approved ? 'Approved' : 'Review' }}
                                    </span>
                                </div>
                                <p class="small text-muted mb-0">"{{ Str::limit($logbook->aktivitas, 80) }}"</p>
                            </div>
                        @endforeach
                        
                        <div class="text-center">
                            <a href="{{ route('peserta.logbook.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua →</a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0 small">Belum ada logbook</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Announcements Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📢 Pengumuman {{ $room ? $room->nama_room : '' }}</h5>
                </div>
                <div class="card-body">
                    @if($announcements->count() > 0)
                        @foreach($announcements as $announcement)
                            <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex align-items-start">
                                    <span class="me-2">📌</span>
                                    <div>
                                        <strong class="d-block">{{ $announcement->description }}</strong>
                                        <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0 small">Belum ada pengumuman</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection