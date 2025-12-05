@extends('layout/app')

@section('konten')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">
        <i class="fas fa-book"></i>
        {{ $judul }}
    </h1>
    <a href="{{ route('materi') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-info-circle mr-2"></i>
            Informasi Materi
        </h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Judul Materi:</strong>
            </div>
            <div class="col-md-9">
                {{ $materi->judul }}
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Kategori:</strong>
            </div>
            <div class="col-md-9">
                <span class="badge badge-info">
                    {{ $materi->room ? $materi->room->nama_room : '-' }}
                </span>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Deskripsi:</strong>
            </div>
            <div class="col-md-9">
                {{ $materi->deskripsi ?? '-' }}
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Dibuat Pada:</strong>
            </div>
            <div class="col-md-9">
                {{ $materi->created_at->format('d F Y, H:i') }} WIB
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Terakhir Diupdate:</strong>
            </div>
            <div class="col-md-9">
                {{ $materi->updated_at->format('d F Y, H:i') }} WIB
            </div>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3 bg-success text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-file-alt mr-2"></i>
            Konten Materi
        </h6>
    </div>
    <div class="card-body">
        <div class="materi-content">
            {!! $materi->konten !!}
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('materiEdit', $materi->materi_id) }}" class="btn btn-warning">
        <i class="fas fa-edit mr-2"></i>
        Edit Materi
    </a>
    <form action="{{ route('materiDestroy', $materi->materi_id) }}" method="POST" style="display: inline-block;" 
          onsubmit="return confirm('Yakin ingin hapus materi ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash mr-2"></i>
            Hapus Materi
        </button>
    </form>
</div>

@endsection