@extends('layout/app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-plus"></i>
    {{ $judul }}
</h1>
    <div class="card">
        <div class="card-header d-flex flex-wrap b">
            <a href="{{ route('mentor.materi') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left mr-2"></i>
                Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('mentor.materiStore') }}" method="post">
                @csrf
            <div class="row mb-3">
                <div class="col-xl-4">
                    <label class="form-label"> <span class="text-danger">*</span> Judul Materi</label> 
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}"> 
                    @error('judul')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror
                </div>

                <div class="col-xl-4">
                    <label class="form-label"> <span class="text-danger">*</span> Kategori</label> 
                    <select name="room_id" class="form-control @error('room_id') is-invalid @enderror">
                        <option selected disabled>-- Pilih Room --</option>
                        @foreach($room as $item)
                            <option value="{{ $item->room_id }}" {{ old('room_id') == $item->room_id ? 'selected' : '' }}>
                                {{ $item->nama_room }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror
                </div>

                <div class="col-xl-4">
                    <label class="form-label">Deskripsi</label> 
                    <input type="text" name="deskripsi" class="form-control" value="{{ old('deskripsi') }}"> 
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">Konten</label>
                    <textarea name="konten" id="editor" class="form-control @error('konten') is-invalid @enderror">{{ old('konten') }}</textarea>
                    @error('konten')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-sm btn-secondary me-2 mr-2" onclick="previewContent()">
                    <i class="fas fa-eye"></i>
                    Preview
                </button>

                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Publish
                </button>
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