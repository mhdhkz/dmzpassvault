@extends('layouts/layoutMaster')

@section('title', 'Form Role Position')

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
  <script>
    window.USER_ROLE = "{{ auth()->user()->role }}";
    window.CURRENT_USER_ID = "{{ auth()->id() }}"; // untuk seleksi request miliknya sendiri
  </script>
  @vite(['resources/assets/js/roles-form.js'])
@endsection

@section('content')
  <div class="card mb-6">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">
      {{ isset($position) ? 'Edit Role Position' : 'Form Penambahan Role Position' }}
    </h3>
    </div>

    <form class="card-body mt-8 mb-4 role-form" method="POST"
    action="{{ isset($position) ? route('admin-role-update', $position->id) : route('admin-role-store') }}">
    @csrf
    @if(isset($position)) @method('PUT') @endif

    <div class="row g-4">
      <div class="col-md-6">
      <label class="form-label" for="name">Nama Role / Position*</label>
      <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $position->name ?? '') }}"
        required />
      </div>

      <div class="col-md-6">
      <label class="form-label" for="platform_ids">Platform yang Diizinkan*</label>
      <select id="platform_ids" name="platform_ids[]" class="form-select select2" multiple required>
        @foreach($platforms as $platform)
      <option value="{{ $platform->id }}" {{ isset($position) && in_array($platform->id, $position->platforms->pluck('id')->toArray()) ? 'selected' : '' }}>
      {{ $platform->name }}
      </option>
      @endforeach
      </select>
      </div>
    </div>

    <div class="mt-4">
      <hr class="my-4 border-gray-600" />
      <small class="text-danger fst-italic">(*) Wajib diisi</small>
    </div>

    <div class="pt-4">
      <button type="submit" class="btn btn-primary me-4" id="btn-submit-role">
      {{ isset($position) ? 'Simpan Perubahan' : 'Tambah Role' }}
      </button>
      <a href="{{ route('admin-role-form') }}" class="btn btn-label-secondary">Kembali</a>
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

  <hr class="my-5" />
  <h5>Daftar Role Position</h5>
  <div class="table-responsive">
    <table class="table table-bordered table-hover" id="position-table">
    <thead class="table-light">
      <tr>
      <th>ID</th>
      <th>Nama Role</th>
      <th>Platform yang Diizinkan</th>
      <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($positions as $pos)
      <tr>
      <td>{{ $pos->id }}</td>
      <td>{{ $pos->name }}</td>
      <td>
      @foreach($pos->platforms as $pf)
      <span class="badge bg-primary me-1">{{ $pf->name }}</span>
      @endforeach
      </td>
      <td>
      <button type="button" class="btn btn-sm btn-warning btn-edit-role" data-id="{{ $pos->id }}"
      data-name="{{ $pos->name }}" data-platforms="{{ $pos->platforms->pluck('id')->join(',') }}">
      Edit
      </button>

      <form action="{{ route('admin-role-delete', $pos->id) }}" method="POST" class="d-inline delete-form">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
      </form>
      </td>
      </tr>
    @endforeach
    </tbody>
    </table>
  </div>

  <!-- Modal Edit Role -->
  <div class="modal fade" id="modal-edit-role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
    <form method="POST" class="modal-content" id="form-edit-role">
      @csrf
      @method('PUT')
      <div class="modal-header bg-warning">
      <h5 class="modal-title text-white pb-3">Edit Role Position</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <input type="hidden" name="edit_id" id="edit_id" />

      <div class="mb-3">
        <label for="edit_name" class="form-label">Nama Role</label>
        <input type="text" class="form-control" name="name" id="edit_name" required />
      </div>

      <div class="mb-3">
        <label for="edit_platform_ids" class="form-label">Platform Diizinkan</label>
        <select id="edit_platform_ids" name="platform_ids[]" class="form-select select2" multiple required>
        @foreach ($platforms as $pf)
      <option value="{{ $pf->id }}">{{ $pf->name }}</option>
      @endforeach
        </select>
      </div>
      </div>
      <div class="modal-footer">
      <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
      <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
    </div>
  </div>

  @if (session('success'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
      toast: true,
      icon: 'success',
      title: @json(session('success')),
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      customClass: { popup: 'colored-toast' }
    });
    });
    </script>
  @endif
@endsection