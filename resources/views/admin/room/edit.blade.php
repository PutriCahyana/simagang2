<!-- Modal Edit Room -->
<div class="modal fade" id="editRoomModal" tabindex="-1" role="dialog" aria-labelledby="editRoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editRoomLabel">Edit Room</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="" method="POST" id="editRoomForm">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" id="edit_room_id">
                    
                    <div class="form-group mb-4">
                        <label for="edit_nama_room">Nama Room <span class="text-danger">*</span></label>
                        <input type="text" name="nama_room" id="edit_nama_room" class="form-control" placeholder="Masukkan nama room..." required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="edit_deskripsi">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="4" placeholder="Masukkan deskripsi room..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" form="editRoomForm" class="btn btn-primary">Update Room</button>
            </div>
        </div>
    </div>
</div>