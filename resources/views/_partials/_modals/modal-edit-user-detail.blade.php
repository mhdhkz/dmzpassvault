<!-- Edit User Detail Modal -->
<div class="modal fade" id="editUserDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-edit-user">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h4 class="mb-2 fw-bold">Edit Informasi User</h4>
          <p class="mb-0">Perubahan akan langsung diterapkan ke sistem.</p>
        </div>

        <form class="row g-4" id="editUserDetailForm">
          <input type="hidden" id="editUserDetailId" name="id">

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserDetailName">Nama</label>
            <input type="text" id="editUserDetailName" name="name" class="form-control" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserDetailEmail">Email</label>
            <input type="email" id="editUserDetailEmail" name="email" class="form-control" />
          </div>

          @php
      $currentUser = auth()->user();
    @endphp

          @if ($currentUser->role === 'admin')
        <!-- Admin bisa ubah role -->
        <div class="col-12">
        <label class="form-label" for="editUserDetailRole">Role</label>
        <select id="editUserDetailRole" name="role" class="form-select select2">
          <option value="">Pilih Role</option>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
        </div>
      @else
        <!-- Non-admin: sembunyikan input tapi tetap kirim role -->
        <input type="hidden" name="role" id="editUserDetailRole" value="{{ $user->role }}">
      @endif


          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserDetailBirthDate">Birth Date</label>
            <input type="date" id="editUserDetailBirthDate" name="birth_date" class="form-control" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserDetailNationality">Nationality</label>
            <input type="text" id="editUserDetailNationality" name="nationality" class="form-control" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserDetailEmployeeId">Employee ID</label>
            <input type="text" id="editUserDetailEmployeeId" name="employee_id" class="form-control" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserDetailJobTitle">Job Title</label>
            <input type="text" id="editUserDetailJobTitle" name="job_title" class="form-control" />
          </div>

          @if ($currentUser->role === 'admin')
        <div class="col-12 col-md-6">
        <label class="form-label" for="editUserDetailPosition">Position</label>
        <select id="editUserDetailPosition" name="position_id" class="form-select select2">
          <option value="">Pilih Posisi</option>
          @foreach($positions as $position)
        <option value="{{ $position->id }}">{{ $position->name }}</option>
      @endforeach
        </select>
        </div>
      @else
        <input type="hidden" name="position_id" id="editUserDetailPosition" value="{{ $user->position_id }}">
      @endif


          <div class="col-12 col-md-6">
            <label class="form-label" for="editUserDetailWorkMode">Work Mode</label>
            <select id="editUserDetailWorkMode" name="work_mode" class="form-select select2">
              <option value="">Pilih Mode Kerja</option>
              <option value="Onsite">Onsite</option>
              <option value="Remote">Remote</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label" for="editUserDetailWorkLocation">Work Location</label>
            <input type="text" id="editUserDetailWorkLocation" name="work_location" class="form-control" />
          </div>

          <div class="col-12 text-center mt-3">
            <button type="button" class="btn btn-primary" id="btnUpdateUserDetail">Simpan Perubahan</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Batalkan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /Edit User Detail Modal -->