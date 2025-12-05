@extends('layout/app')

@section('konten')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit"></i>
    {{ $judul }}
</h1>

<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('materiUpdate', $materi->materi_id) }}" method="POST">
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
                          id="konten" name="konten" rows="10" 
                          placeholder="Masukkan konten materi">{{ old('konten', $materi->konten) }}</textarea>
                @error('konten')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mt-4">
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

@endsection