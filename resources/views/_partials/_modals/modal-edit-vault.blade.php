@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/vault-form.js',
    'resources/assets/js/modal-edit-vault.js'
  ])
@endsection

<!-- Edit Vault Request Modal -->
<div class="modal fade" id="editRequestModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

        <div class="text-center mb-4">
          <h4 class="mb-2 fw-bold">Edit Permintaan Vault</h4>
          <p>Silakan ubah informasi pengajuan vault.</p>
        </div>

        <form class="row g-4" id="editRequestForm" onsubmit="return false">
          <input type="hidden" id="editRequestId" name="id">

          <div class="col-12">
            <label class="form-label" for="editRequestIdentifier">Request ID</label>
            <input type="text" id="editRequestIdentifier" class="form-control" disabled />
          </div>

          <div class="col-12">
            <label class="form-label" for="editPurpose">Purpose</label>
            <input type="text" id="editPurpose" name="purpose" class="form-control" />
          </div>

          <div class="col-12">
            <label class="form-label" for="editDuration">Durasi (menit)</label>
            <input type="text" id="editDurationRange" name="duration_range" class="form-control"
              placeholder="YYYY-MM-DD HH:mm - YYYY-MM-DD HH:mm" autocomplete="off" />
            <small class="fst-italic">maksimal 5 hari</small>
          </div>

          <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary" id="btnUpdateRequest">Simpan Perubahan</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
<!-- / Edit Vault Request Modal -->
