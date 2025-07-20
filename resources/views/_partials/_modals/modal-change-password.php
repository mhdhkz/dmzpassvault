<!-- Change Password Modal -->
<div class="modal fade" id="changePassword" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-simple modal-password-user">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2 fw-bold">Ganti Password</h4>
          <p class="mb-0">Password baru akan diterapkan langsung ke akun ini.</p>
        </div>

        <form id="changePasswordForm" class="row g-3">
          <input type="hidden" id="changePasswordUserId" name="id">

          <div class="col-12 text-center">
            <label class="form-label">User</label><br>
            <span class="fw-semibold" id="changePasswordUsername">-</span>
          </div>

          <div class="col-12">
            <label class="form-label" for="newPassword">Password Baru</label>
            <input type="password" class="form-control" id="newPassword" name="password" required />
          </div>

          <div class="col-12">
            <label class="form-label" for="confirmPassword">Konfirmasi Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required />
          </div>

          <div class="col-12 text-center mt-2">
            <button type="submit" class="btn btn-warning">Ganti Password</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /Change Password Modal -->
