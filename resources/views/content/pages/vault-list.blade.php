@extends('layouts/layoutMaster')

@section('title', 'List Pengajuan Vault')

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
    <h3 class="card-title mb-0 text-center text-white">List Pengajuan Vault</h3>
    </div>

    <!-- Filter -->
    <div class="card-header">
    <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#vaultFilters"
      aria-expanded="true">
      <span class="icon-base bx bx-filter icon-sm me-2"></span>Filter <i class="bx bx-chevron-down"></i>
    </button>
    </div>

    <div class="collapse" id="vaultFilters">
    <div class="card-body">
      <div class="row g-4">
      <div class="col-md-4">
        <label for="filter-status" class="form-label">Status</label>
        <select id="filter-status" class="form-select select2" data-placeholder="Pilih Status">
        <option value="">Semua Status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
        <option value="expired">Expired</option>
        </select>
      </div>
      </div>
      <div>
      <button type="button" id="clearFilterBtn" class="btn btn-warning mt-4">
        <span class="icon-base bx bx-reset icon-sm me-2"></span>Hapus Filter
      </button>
      </div>
    </div>
    </div>

    <hr class="my-4 border-gray-600" />

    <!-- DataTable -->
    <div class="card-datatable">
    <table class="datatables-users table border-top">
      <thead class="table-light">
      <tr>
        <th class="control dt-orderable-none dtr-hidden" aria-label=""></th>
        <th data-dt-column="1" class="dt-orderable-none" aria-label="Checkbox: Not sortable"></th>
        <th data-dt-column="2" class="dt-orderable-none">No</th>
        <th data-dt-column="3"><span class="dt-column-title">ID Pengajuan</span></th>
        <th data-dt-column="4"><span class="dt-column-title">Server</span></th>
        <th data-dt-column="5"><span class="dt-column-title">Waktu Request</span></th>
        <th data-dt-column="6"><span class="dt-column-title">Durasi</span></th>
        <th data-dt-column="7"><span class="dt-column-title">Status</span></th>
        <th data-dt-column="8" class="dt-orderable-none"><span class="dt-column-title">Actions</span></th>
      </tr>
      </thead>
    </table>
    </div>
  </div>
  @include('_partials._modals.modal-edit-vault')
@endsection
