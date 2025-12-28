@extends('layout.app')

@section('konten')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Status Sertifikat</h2>
                    <p class="text-muted">Pantau proses pembuatan sertifikat Anda</p>
                </div>
                <a href="{{ route('peserta.logbook.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                 style="width: 60px; height: 60px; font-size: 1.5rem;">
                                {{ strtoupper(substr($user->nama, 0, 1)) }}
                            </div>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <h5 class="mb-1">{{ $user->nama }}</h5>
                            <p class="text-muted mb-0">{{ $user->peserta->nim ?? '-' }} - {{ $user->peserta->institut ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Status -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">Progress Sertifikat</h5>
                    
                    <div class="timeline-container">
                        <!-- Step 1: Generate -->
                        <div class="timeline-step {{ $certificate ? 'completed' : 'pending' }}">
                            <div class="timeline-icon">
                                @if($certificate)
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-muted" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Sertifikat Dibuat</h6>
                                <p class="text-muted mb-0">
                                    @if($certificate)
                                        <i class="bi bi-check-circle me-1"></i>
                                        Dibuat pada {{ \Carbon\Carbon::parse($certificate->created_at)->format('d M Y H:i') }}
                                    @else
                                        Menunggu admin untuk membuat sertifikat
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="timeline-line {{ $certificate ? 'active' : '' }}"></div>

                        <!-- Step 2: Draft -->
                        <div class="timeline-step {{ $certificate && $certificate->status === 'draft' ? 'active' : ($certificate && $certificate->status === 'approved' ? 'completed' : 'pending') }}">
                            <div class="timeline-icon">
                                @if($certificate && $certificate->status === 'approved')
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @elseif($certificate && $certificate->status === 'draft')
                                    <i class="bi bi-clock-fill text-warning" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-muted" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Review Draft</h6>
                                <p class="text-muted mb-0">
                                    @if($certificate && $certificate->status === 'approved')
                                        <i class="bi bi-check-circle me-1"></i>
                                        Draft telah direview
                                    @elseif($certificate && $certificate->status === 'draft')
                                        <i class="bi bi-clock me-1"></i>
                                        Sertifikat sedang dalam review oleh admin
                                    @else
                                        Menunggu tahap sebelumnya selesai
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="timeline-line {{ $certificate && $certificate->status === 'approved' ? 'active' : '' }}"></div>

                        <!-- Step 3: Approved -->
                        <div class="timeline-step {{ $certificate && $certificate->status === 'approved' ? 'completed' : 'pending' }}">
                            <div class="timeline-icon">
                                @if($certificate && $certificate->status === 'approved')
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-circle text-muted" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Sertifikat Approved</h6>
                                <p class="text-muted mb-0">
                                    @if($certificate && $certificate->status === 'approved')
                                        <i class="bi bi-check-circle me-1"></i>
                                        Approved pada {{ \Carbon\Carbon::parse($certificate->approved_at)->format('d M Y H:i') }}
                                        <br>
                                        <a href="{{ route('peserta.sertifikat.preview') }}" class="btn btn-success btn-sm mt-2">
                                            <i class="bi bi-download"></i> Lihat & Download Sertifikat
                                        </a>
                                    @else
                                        Menunggu approval dari admin
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Alert -->
            @if(!$certificate)
            <div class="alert alert-info mt-4" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Informasi:</strong> Sertifikat Anda akan dibuat oleh admin setelah Anda menyelesaikan periode magang. Silakan hubungi admin jika ada pertanyaan.
            </div>
            @elseif($certificate->status === 'draft')
            <div class="alert alert-warning mt-4" role="alert">
                <i class="bi bi-clock me-2"></i>
                <strong>Dalam Proses:</strong> Sertifikat Anda sedang dalam tahap review. Mohon menunggu approval dari admin.
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline-container {
    position: relative;
    padding: 20px 0;
}

.timeline-step {
    position: relative;
    display: flex;
    align-items: flex-start;
    margin-bottom: 40px;
}

.timeline-step:last-child {
    margin-bottom: 0;
}

.timeline-icon {
    flex-shrink: 0;
    width: 60px;
    display: flex;
    justify-content: center;
    z-index: 2;
}

.timeline-content {
    flex-grow: 1;
    padding-left: 20px;
    padding-top: 5px;
}

.timeline-line {
    position: absolute;
    left: 30px;
    top: 50px;
    width: 2px;
    height: 60px;
    background-color: #e0e0e0;
    z-index: 1;
}

.timeline-line.active {
    background-color: #28a745;
}

.timeline-step.completed .timeline-content h6 {
    color: #28a745;
}

.timeline-step.active .timeline-content h6 {
    color: #ffc107;
}

.timeline-step.pending .timeline-content h6 {
    color: #6c757d;
}
</style>
@endsection