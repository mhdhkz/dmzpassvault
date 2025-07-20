<!-- Edit Identity Modal -->
<div class="modal fade" id="editIdentity" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="mb-2 fw-bold">Edit Informasi Identity</h4>
          <p>Perubahan akan dicatat dalam log sistem.</p>
        </div>
        <form class="row g-4" id="editIdentityForm" onsubmit="return false">
          <input type="hidden" id="editIdentityId" name="id">

          <div class="col-12 col-md-6">
            <label class="form-label" for="editHostname">Hostname</label>
            <input type="text" id="editHostname" name="hostname" class="form-control" placeholder="cth: server-01" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editIpAddress">IP Address</label>
            <input type="text" id="editIpAddress" name="ip_addr_srv" class="form-control"
              placeholder="cth: 192.168.1.1" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUsername">Username</label>
            <input type="text" id="editUsername" name="username" class="form-control" placeholder="cth: admin" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editFunctionality">Functionality</label>
            <input type="text" id="editFunctionality" name="functionality" class="form-control"
              placeholder="cth: Web Server App A" />
          </div>


          <div class="col-12 col-md-6">
            <label class="form-label" for="editPlatform">Platform</label>
            <select id="editPlatform" name="platform_id" class="select2 form-select">
              <option value="">Pilih Platform</option>
              @foreach($platforms as $platform)
          <option value="{{ $platform->id }}">{{ $platform->name }}</option>
        @endforeach
            </select>
          </div>

          <div class="col-12">
            <label class="form-label" for="editDescription">Deskripsi</label>
            <textarea class="form-control" id="editDescription" name="description" rows="3" maxlength="500"
              placeholder="Tuliskan deskripsi tambahan (opsional)"></textarea>
            <small id="charCount" class="form-text text-muted d-block text-end">500 karakter tersisa</small>
          </div>

          <div class="col-12 text-center mt-3">
            <button type="button" class="btn btn-primary" id="btnUpdateIdentity">Simpan Perubahan</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Batalkan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Edit Identity Modal -->