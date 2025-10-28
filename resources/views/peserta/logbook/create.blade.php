@extends('layout.app')

@section('konten')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tambah Logbook</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('peserta.logbook.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="room_id" class="form-label">Room <span class="text-danger">*</span></label>
                            <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Room --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->room_id }}" {{ old('room_id') == $room->room_id ? 'selected' : '' }}>
                                        {{ $room->nama_room }}
                                    </option>
                                @endforeach
                            </select> 
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" 
                                   name="date" 
                                   id="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date') }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tidak boleh mengisi logbook di hari Sabtu/Minggu</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Kerja</label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Jam kerja akan otomatis terisi:<br>
                                <strong>Senin - Kamis:</strong> 07:30 - 16:30<br>
                                <strong>Jumat:</strong> 07:30 - 17:00
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="aktivitas" class="form-label">Aktivitas <span class="text-danger">*</span></label>
                            <textarea name="aktivitas" 
                                      id="aktivitas" 
                                      rows="5" 
                                      class="form-control @error('aktivitas') is-invalid @enderror" 
                                      placeholder="Tuliskan aktivitas yang dilakukan hari ini..."
                                      required>{{ old('aktivitas') }}</textarea>
                            @error('aktivitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <select name="keterangan" id="keterangan" class="form-select @error('keterangan') is-invalid @enderror" required>
                                <option value="offline_kantor" {{ old('keterangan') == 'offline_kantor' ? 'selected' : '' }}>Offline Kantor</option>
                                <option value="online" {{ old('keterangan') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="sakit" {{ old('keterangan') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="izin" {{ old('keterangan') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="alpha" {{ old('keterangan') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                            </select>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('peserta.logbook.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Logbook
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection