@extends('layouts/layoutMaster')

@section('title', 'Form Penambahan User')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/user-form.js'])
@endsection

@section('content')
  <div class="card mb-6">
    <div class="card-header border-bottom custom-header-bg">
      <h3 class="card-title mb-0 text-center text-white">Form Penambahan User Baru</h3>
    </div>

    <form class="card-body mt-8 mb-4 user-form" method="POST" action="{{ route('admin-user-store') }}">
      @csrf

      <div class="row g-4">
        <div class="col-md-6">
          <label class="form-label" for="name">Nama Lengkap*</label>
          <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="email">Email*</label>
          <input type="email" class="form-control" id="email" name="email" required value="{{ old('email') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="role">Role*</label>
          <select class="form-select" id="role" name="role" required>
            <option value="">Pilih Role</option>
            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label" for="birth_date">Tanggal Lahir</label>
          <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="employee_id">Employee ID</label>
          <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="nationality">Kewarganegaraan</label>
          <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="job_title">Jabatan</label>
          <input type="text" class="form-control" id="job_title" name="job_title" value="{{ old('job_title') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="position">Posisi</label>
          <input type="text" class="form-control" id="position" name="position" value="{{ old('position') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="work_mode">Mode Kerja</label>
          <select class="form-select" id="work_mode" name="work_mode">
            <option value="">Pilih Mode</option>
            <option value="Onsite" {{ old('work_mode') === 'Onsite' ? 'selected' : '' }}>Onsite</option>
            <option value="Remote" {{ old('work_mode') === 'Remote' ? 'selected' : '' }}>Remote</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label" for="work_location">Lokasi Kerja</label>
          <input type="text" class="form-control" id="work_location" name="work_location" value="{{ old('work_location') }}" />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="password">Password*</label>
          <input type="password" class="form-control" id="password" name="password" required />
        </div>

        <div class="col-md-6">
          <label class="form-label" for="password_confirmation">Konfirmasi Password*</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required />
        </div>
      </div>

      <div class="mt-4">
        <hr class="my-4 border-gray-600" />
        <small class="text-danger fst-italic">(*) Wajib diisi</small>
      </div>

      <div class="pt-4">
        <button type="submit" class="btn btn-primary me-4" id="btn-submit-user">Tambah User</button>
        <a href="{{ route('admin-user-list') }}" class="btn btn-label-secondary">Kembali</a>
      </div>

      @if ($errors->any())
      <div class="alert alert-danger mt-4">
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
    </form>
  </div>

  @if (session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'colored-toast' },
        didOpen: toast => {
          toast.addEventListener('mouseenter', Swal.stopTimer);
          toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
      });
      Toast.fire({ icon: 'success', title: @json(session('success')) });
    });
  </script>
  @endif
@endsection
