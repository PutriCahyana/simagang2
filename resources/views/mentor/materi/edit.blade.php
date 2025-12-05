@extends('layout/app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit"></i>
    {{ $judul }}
</h1>

<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('mentor.materiUpdate', $materi->materi_id) }}" method="POST">
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

            <div class="form-group mt-4">
                <button type="button" class="btn btn-secondary" onclick="previewContent()">
                    <i class="fas fa-eye mr-2"></i>
                    Preview
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Update Materi
                </button>
                <a href="{{ route('mentor.materi') }}" class="btn btn-secondary">
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
</style>
@endpush