<!-- Info User -->
<div class="card bg-light mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                 style="width: 50px; height: 50px; font-size: 1.2rem;">
                {{ strtoupper(substr($user->nama, 0, 1)) }}
            </div>
            <div class="ms-3">
                <h6 class="mb-0">{{ $user->nama }}</h6>
                <small class="text-muted">{{ $user->peserta->nim ?? '-' }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Timeline -->
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
            <p class="text-muted mb-0 small">
                @if($certificate)
                    <i class="bi bi-check-circle me-1"></i>
                    Dibuat {{ \Carbon\Carbon::parse($certificate->created_at)->diffForHumans() }}
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
            <p class="text-muted mb-0 small">
                @if($certificate && $certificate->status === 'approved')
                    <i class="bi bi-check-circle me-1"></i>
                    Draft telah direview
                @elseif($certificate && $certificate->status === 'draft')
                    <i class="bi bi-clock me-1"></i>
                    Sedang dalam review oleh admin
                @else
                    Menunggu tahap sebelumnya
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
            <p class="text-muted mb-0 small">
                @if($certificate && $certificate->status === 'approved')
                    <i class="bi bi-check-circle me-1"></i>
                    Approved {{ \Carbon\Carbon::parse($certificate->approved_at)->diffForHumans() }}
                @else
                    Menunggu approval dari admin
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Alert Info -->
@if(!$certificate)
<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle me-2"></i>
    <small>Sertifikat akan dibuat setelah periode magang selesai.</small>
</div>
@elseif($certificate->status === 'draft')
<div class="alert alert-warning mt-3">
    <i class="bi bi-clock me-2"></i>
    <small>Sertifikat sedang direview. Mohon tunggu approval dari admin.</small>
</div>
@endif