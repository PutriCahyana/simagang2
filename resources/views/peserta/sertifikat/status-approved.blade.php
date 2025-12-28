<div class="text-center py-4">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
    </div>
    
    <h5 class="mb-2">Sertifikat Sudah Siap!</h5>
    <p class="text-muted mb-4">
        Nomor: <strong>{{ $certificate->nomor_surat }}</strong><br>
        <small>Approved {{ \Carbon\Carbon::parse($certificate->approved_at)->format('d M Y H:i') }}</small>
    </p>
    
    <div class="d-flex gap-2 justify-content-center">
        <a href="{{ route('peserta.sertifikat.preview') }}" class="btn btn-primary">
            <i class="bi bi-eye"></i> Lihat Sertifikat
        </a>
        @if($certificate->pdf_data)
        <a href="{{ asset('storage/' . $certificate->pdf_data) }}" 
           download="Sertifikat-{{ $certificate->nomor_surat }}.pdf"
           class="btn btn-success">
            <i class="bi bi-download"></i> Download PDF
        </a>
        @endif
    </div>
</div>