@extends('layouts/layoutMaster')

@section('title', 'List Permohonan Vault')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/spinkit/spinkit.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js'
  ])
@endsection

@section('page-script')
  @vite(['resources/assets/js/vault-list.js', 'resources/assets/js/modal-edit-vault.js'])
@endsection

@section('content')
  <!-- Vault Request List Table -->
  <div class="card">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">List Permohonan Vault</h3>
    </div>
    <!-- DataTable -->
    <!-- FILTER -->
    <div class="px-3 pt-3">
    <div class="row mb-3">
      <div class="col-md-3">
      <label for="filter-status" class="form-label">Status</label>
      <select id="filter-status" class="form-select">
        <option value="">Semua</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
        <option value="expired">Expired</option>
      </select>
      </div>
      <div class="col-md-3">
      <label for="filter-user" class="form-label">User Pemohon</label>
      <input type="text" id="filter-user" class="form-control" placeholder="Nama user">
      </div>
      <div class="col-md-4">
      <label for="filter-date-range" class="form-label">Tanggal Request</label>
      <input type="text" id="filter-date-range" class="form-control" autocomplete="off"
        placeholder="YYYY-MM-DD - YYYY-MM-DD">
      </div>
    </div>
    <div class="col-md-2 d-flex align-items-end">
      <button id="btn-clear-filter" class="btn btn-warning w-70 ml-2">
      <i class="bx bx-eraser me-1"></i> Hapus Filter
      </button>
    </div>
    </div>
    <!-- ENDFILTER -->
    <!-- DataTable -->
    <div class="card-datatable">
    <table class="datatables-users table border-top">
      <thead class="table-light">
      <tr>
        <th class="control dt-orderable-none dtr-hidden" aria-label=""></th>
        <th data-dt-column="1" class="dt-orderable-none" aria-label="Checkbox: Not sortable"></th>
        <th data-dt-column="2" class="dt-orderable-none">No</th>
        <th data-dt-column="3"><span class="dt-column-title">ID Pengajuan</span></th>
        <th data-dt-column="4"><span class="dt-column-title">User Pemohon</span></th>
        <th data-dt-column="5"><span class="dt-column-title">Waktu Request</span></th>
        <th data-dt-column="6"><span class="dt-column-title">Durasi</span></th>
        <th data-dt-column="7"><span class="dt-column-title">Status</span></th>
        <th data-dt-column="8" class="dt-orderable-none text-center"><span
          class="dt-column-title text-center">Actions</span></th>
      </tr>
      </thead>
    </table>
    </div>
  </div>

  @include('_partials._modals.modal-edit-vault')
@endsection