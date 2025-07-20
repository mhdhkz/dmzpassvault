<!-- Edit User Modal -->
<div class="modal fade" id="editUserList" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2 fw-bold">Edit Informasi User</h4>
          <p class="mb-0">Perubahan akan langsung diterapkan ke sistem.</p>
        </div>

        <form class="row g-4" id="editUserListForm" onsubmit="return false">
          <input type="hidden" id="editUserId" name="id">

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserName">Nama</label>
            <input type="text" id="editUserName" name="name" class="form-control" placeholder="Nama lengkap" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserEmail">Email</label>
            <input type="email" id="editUserEmail" name="email" class="form-control" placeholder="email@example.com" />
          </div>

          <div class="col-12">
            <label class="form-label" for="editUserRole">Role</label>
            <select id="editUserRole" name="role" class="form-select select2" style="width: 100%">
              <option value="">Pilih Role</option>
              <option value="admin">Admin</option>
              <option value="user">User</option>
              {{-- Tambahkan role lain jika ada --}}
            </select>
          </div>

          <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Batalkan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /Edit User Modal -->
