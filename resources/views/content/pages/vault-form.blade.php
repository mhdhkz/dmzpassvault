@extends('layouts/layoutMaster')

@section('title', 'Form Permohonan Vault')

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
  @vite(['resources/assets/js/vault-form.js'])
@endsection

@section('content')
  <div class="card mb-6">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">Form Permohonan Akses Vault</h3>
    </div>
    <form class="card-body mt-8 mb-4 vault-form" method="POST" action="{{ route('vault.store') }}">

    @csrf

    <h6>1. Informasi Pengajuan</h6>
    <div class="row g-4">

      <div class="mb-3">
      <label class="form-label" for="preview-request-id">Request ID</label>
      <div id="request-id-preview" class="form-control bg-light">
        <span class="text-muted">Memuat...</span>
      </div>
      </div>

      <div class="col-md-12">
      <label class="form-label" for="purpose">Tujuan / Keperluan*</label>
      <textarea name="purpose" class="form-control" id="purpose" required
        maxlength="1200">{{ old('purpose') }}</textarea>
      </div>
      <div class="col-md-6">
      <label class="form-label mt-4" for="duration_range">Durasi Pemakaian*</label>
      <input type="text" name="duration_range" id="duration_range" class="form-control"
        placeholder="YYYY-MM-DD HH:mm - YYYY-MM-DD HH:mm" required />
      <small class="fst-italic">maksimal 5 hari</small>
      </div>
    </div>

    <hr class="my-6 mx-n6" />

    <h6>2. Pilih Server yang Ingin Diakses</h6>
    <div class="row g-4">
      <div class="col-md-12">
      <label class="form-label" for="identity_ids">Daftar Server*</label>
      <select id="identity_ids" class="select2 form-select" name="identity_ids[]" multiple required>
        @foreach ($identities as $identity)
      <option value="{{ $identity->id }}">
      {{ $identity->hostname }} ({{ $identity->username }} @ {{ $identity->ip_addr_srv ?? 'no-ip' }})
      </option>
      @endforeach
      </select>
      </div>
    </div>

    <div class="mt-2">
      <hr class="my-4 border-gray-600" />
      <small class="text-danger fst-italic">(*) Wajib diisi</small>
    </div>

    <div class="pt-6">
      <button type="submit" class="btn btn-primary me-4" id="confirm-color">Kirim Pengajuan</button>
      <button type="reset" class="btn btn-label-secondary">Batalkan</button>
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
