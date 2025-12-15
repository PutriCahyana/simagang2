<!-- Modal Create Pengumuman -->
<div class="modal fade" id="modalTambahPengumuman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-megaphone-fill me-2"></i>Tambah Pengumuman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formTambahPengumuman">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Pengumuman</label>
                        <input type="text" class="form-control" name="judul" placeholder="Masukkan judul..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Pengumuman</label>
                        <textarea class="form-control" name="isi" rows="4" placeholder="Tulis pengumuman..." maxlength="500" required></textarea>
                        <small class="text-muted">Maksimal 500 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Durasi Tampil</label>
                        <select class="form-select" name="durasi_tampil" required>
                            <option value="24">24 Jam</option>
                            <option value="72">3 Hari</option>
                            <option value="168" selected>7 Hari</option>
                            <option value="720">30 Hari</option>
                        </select>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_penting" value="1" id="isPenting">
                        <label class="form-check-label fw-semibold text-danger" for="isPenting">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Tandai sebagai PENTING
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-warning w-100 fw-bold">
                        <i class="bi bi-send-fill me-2"></i>Publikasikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>