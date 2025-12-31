<!-- Copy bagian modal ini ke file blade kamu -->
<div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog" aria-labelledby="addRoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="addRoomLabel">Create Room</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="{{ route('mentor.roomStore') }}" method="POST" id="roomForm">
                    @csrf
                    
                    <div class="form-group mb-4">
                        <label for="nama_room">Nama Room <span class="text-danger">*</span></label>
                        <input type="text" name="nama_room" id="nama_room" class="form-control" placeholder="Masukkan nama room..." value="GENERAL" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" placeholder="Masukkan deskripsi room..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="roomForm" class="btn btn-primary">Simpan Room</button>
            </div>
        </div>
    </div>
</div>