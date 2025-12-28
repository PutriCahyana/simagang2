@extends('layout.app')

@section('konten')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Sertifikat Saya</h2>
                    <p class="text-muted">{{ $certificate->nomor_surat }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('peserta.logbook.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ asset('storage/' . $certificate->pdf_data) }}" 
                       download="Sertifikat-{{ $certificate->nomor_surat }}.pdf"
                       class="btn btn-success">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Sertifikat Approved!</strong> Sertifikat sudah disetujui dan siap diunduh.
            </div>

            <!-- PDF Viewer -->
            <div class="card shadow-lg">
                <div class="card-body p-0">
                    <iframe 
                        src="{{ asset('storage/' . $certificate->pdf_data) }}" 
                        style="width: 100%; height: 80vh; border: none;">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection