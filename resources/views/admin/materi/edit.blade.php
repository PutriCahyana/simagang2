@extends('layout/app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit"></i>
    {{ $judul }}
</h1>

<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('materiUpdate', $materi->materi_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="judul">Judul Materi <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" 
                       id="judul" name="judul" value="{{ old('judul', $materi->judul) }}" 
                       placeholder="Masukkan judul materi">
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="room_id">Kategori/Room <span class="text-danger">*</span></label>
                <select class="form-control @error('room_id') is-invalid @enderror" 
                        id="room_id" name="room_id">
                    <option value="">-- Pilih Room --</option>
                    @foreach($room as $r)
                        <option value="{{ $r->room_id }}" 
                            {{ old('room_id', $materi->room_id) == $r->room_id ? 'selected' : '' }}>
                            {{ $r->nama_room }}
                        </option>
                    @endforeach
                </select>
                @error('room_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                          id="deskripsi" name="deskripsi" rows="3" 
                          placeholder="Masukkan deskripsi singkat (opsional)">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="konten">Konten Materi <span class="text-danger">*</span></label>
                <textarea class="form-control @error('konten') is-invalid @enderror" 
                          id="editor" name="konten" 
                          placeholder="Masukkan konten materi">{{ old('konten', $materi->konten) }}</textarea>
                @error('konten')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="file">File Lampiran</label>
                
                @if($materi->file_path)
                <div class="current-file mb-3">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="{{ $materi->getFileIcon() }} fa-2x mr-3"></i>
                        <div class="flex-grow-1">
                            <strong>File Saat Ini:</strong><br>
                            <span class="text-muted">{{ basename($materi->file_path) }}</span>
                            @if($materi->getFileType())
                                <span class="badge badge-secondary ml-2">{{ $materi->getFileType() }}</span>
                            @endif
                            @if($materi->getFileSizeFormatted())
                                <span class="text-muted ml-2">({{ $materi->getFileSizeFormatted() }})</span>
                            @endif
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remove_file" name="remove_file" value="1">
                            <label class="custom-control-label text-danger" for="remove_file">
                                <i class="fas fa-trash"></i> Hapus File
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                <div class="custom-file">
                    <input type="file" class="custom-file-input @error('file') is-invalid @enderror" 
                           id="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.zip,.rar">
                    <label class="custom-file-label" for="file">Pilih file...</label>
                    @error('file')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Format yang didukung: PDF, Word, Excel, PowerPoint, Gambar (JPG, PNG, GIF), Video (MP4, AVI, MOV), Archive (ZIP, RAR). 
                    Maksimal 10MB.
                </small>
            </div>

            <div class="form-group mt-4">
                <button type="button" class="btn btn-secondary" onclick="previewContent()">
                    <i class="fas fa-eye mr-2"></i>
                    Preview
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Update Materi
                </button>
                <a href="{{ route('materi') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Konten</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let editorInstance;
    
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'link', 'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo', '|',
                    'alignment', 'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                    'code', 'codeBlock'
                ],
                shouldNotGroupWhenFull: true
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            },
            table: {
                contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
            }
        })
        .then(editor => {
            editorInstance = editor;
        })
        .catch(error => {
            console.error(error);
        });

    function previewContent() {
        const content = editorInstance.getData();
        document.getElementById('previewContent').innerHTML = content;
        $('#previewModal').modal('show');
    }

    // Update file label when file is selected
    document.querySelector('#file').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
        const label = e.target.nextElementSibling;
        label.textContent = fileName;
    });

    // Disable file input when remove checkbox is checked
    document.querySelector('#remove_file')?.addEventListener('change', function(e) {
        const fileInput = document.querySelector('#file');
        if (e.target.checked) {
            fileInput.disabled = true;
            fileInput.nextElementSibling.textContent = 'File akan dihapus';
        } else {
            fileInput.disabled = false;
            fileInput.nextElementSibling.textContent = 'Pilih file...';
        }
    });
</script>
@endpush

@push('styles')
<style>
    .ck-editor__editable {
        min-height: 400px;
    }
    
    #previewContent {
        padding: 20px;
        line-height: 1.6;
    }
    
    #previewContent h1, 
    #previewContent h2, 
    #previewContent h3, 
    #previewContent h4 {
        margin-top: 1em;
        margin-bottom: 0.5em;
    }
    
    #previewContent p {
        margin-bottom: 1em;
    }
    
    #previewContent ul, 
    #previewContent ol {
        margin-left: 20px;
        margin-bottom: 1em;
    }
    
    #previewContent table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 1em;
    }
    
    #previewContent table td, 
    #previewContent table th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    .current-file .alert {
        margin-bottom: 0;
    }

    .custom-file-label::after {
        content: "Browse";
    }
</style>
@endpush