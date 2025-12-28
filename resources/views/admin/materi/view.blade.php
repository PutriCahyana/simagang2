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

        @if($materi->file_path)
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>File Lampiran:</strong>
            </div>
            <div class="col-md-9">
                <div class="file-attachment-box">
                    <i class="{{ $materi->getFileIcon() }} fa-2x mr-3"></i>
                    <div class="file-info">
                        <div class="file-name">{{ basename($materi->file_path) }}</div>
                        <div class="file-meta">
                            <span class="badge badge-secondary">{{ $materi->getFileType() }}</span>
                            @if($materi->getFileSizeFormatted())
                                <span class="text-muted ml-2">{{ $materi->getFileSizeFormatted() }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('materiDownload', $materi->materi_id) }}" 
                       class="btn btn-sm btn-success ml-auto">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
        @endif
        
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

@push('styles')
<style>
    /* File Attachment Styling */
    .file-attachment-box {
        display: flex;
        align-items: center;
        padding: 15px;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .file-attachment-box:hover {
        background-color: #eaecf4;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .file-info {
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 5px;
        word-break: break-word;
    }

    .file-meta {
        font-size: 0.85rem;
    }

    /* Styling untuk konten materi yang dibuat dengan CKEditor */
    .materi-content {
        line-height: 1.8;
        font-size: 16px;
        color: #333;
    }
    
    /* Heading Styles */
    .materi-content h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-top: 1.5em;
        margin-bottom: 0.75em;
        color: #2c3e50;
        border-bottom: 3px solid #4e73df;
        padding-bottom: 10px;
    }
    
    .materi-content h2 {
        font-size: 2rem;
        font-weight: bold;
        margin-top: 1.5em;
        margin-bottom: 0.75em;
        color: #34495e;
        border-bottom: 2px solid #858796;
        padding-bottom: 8px;
    }
    
    .materi-content h3 {
        font-size: 1.75rem;
        font-weight: 600;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        color: #34495e;
    }
    
    .materi-content h4 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 1.25em;
        margin-bottom: 0.5em;
        color: #5a6c7d;
    }
    
    /* Paragraph */
    .materi-content p {
        margin-bottom: 1.25em;
        text-align: justify;
    }
    
    /* Lists */
    .materi-content ul,
    .materi-content ol {
        margin-left: 30px;
        margin-bottom: 1.5em;
        padding-left: 10px;
    }
    
    .materi-content ul li,
    .materi-content ol li {
        margin-bottom: 0.5em;
        line-height: 1.8;
    }
    
    .materi-content ul {
        list-style-type: disc;
    }
    
    .materi-content ol {
        list-style-type: decimal;
    }
    
    /* Links */
    .materi-content a {
        color: #4e73df;
        text-decoration: underline;
    }
    
    .materi-content a:hover {
        color: #2e59d9;
        text-decoration: none;
    }
    
    /* Blockquote */
    .materi-content blockquote {
        border-left: 4px solid #4e73df;
        background-color: #f8f9fc;
        padding: 15px 20px;
        margin: 1.5em 0;
        font-style: italic;
        color: #5a5c69;
    }
    
    /* Table */
    .materi-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5em 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .materi-content table thead {
        background-color: #4e73df;
        color: white;
    }
    
    .materi-content table th,
    .materi-content table td {
        border: 1px solid #e3e6f0;
        padding: 12px 15px;
        text-align: left;
    }
    
    .materi-content table tbody tr:nth-child(even) {
        background-color: #f8f9fc;
    }
    
    .materi-content table tbody tr:hover {
        background-color: #eaecf4;
    }
    
    /* Code Inline */
    .materi-content code {
        background-color: #f4f4f4;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 2px 6px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: #c7254e;
    }
    
    /* Code Block */
    .materi-content pre {
        background-color: #2d2d2d;
        color: #f8f8f2;
        padding: 20px;
        border-radius: 6px;
        overflow-x: auto;
        margin: 1.5em 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .materi-content pre code {
        background-color: transparent;
        border: none;
        color: #f8f8f2;
        padding: 0;
    }
    
    /* Strong & Emphasis */
    .materi-content strong {
        font-weight: bold;
        color: #2c3e50;
    }
    
    .materi-content em {
        font-style: italic;
    }
    
    /* Images (jika ada) */
    .materi-content img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 1.5em auto;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    /* Horizontal Rule */
    .materi-content hr {
        border: none;
        border-top: 2px solid #e3e6f0;
        margin: 2em 0;
    }
</style>
@endpush