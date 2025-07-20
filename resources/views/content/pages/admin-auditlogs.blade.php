@extends('layouts/layoutMaster')

@section('title', 'Audit Logs')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/spinkit/spinkit.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
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
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
  ])
@endsection

@section('page-script')
  @vite('resources/assets/js/audit-logs.js')
@endsection

@section('content')

  <div class="card">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">Audit Log</h3>
    </div>

    <!-- Collapsible Filter -->
    <div class="card-header">
    <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#auditFilters"
      aria-expanded="true">
      <span class="icon-base bx bx-filter icon-sm me-2"></span>Filter <i class="bx bx-chevron-down"></i>
    </button>
    </div>

    <div class="collapse" id="auditFilters">
    <div class="card-body">
      <div class="row g-4">
      <div class="col-md-4">
        <label for="filterEventType" class="form-label">Event Type</label>
        <select id="filterEventType" class="form-select select2">
        <option value="">Semua</option>
        <option value="created">Created</option>
        <option value="updated">Updated</option>
        <option value="deleted">Deleted</option>
        <option value="accessed">Accessed</option>
        <!-- Tambahkan jenis lainnya -->
        </select>
      </div>
      <div class="col-md-4">
        <label for="filterUserName" class="form-label">User Pemicu</label>
        <input type="text" id="filterUserName" class="form-control" placeholder="Nama User">
      </div>
      <div class="col-md-4">
        <label for="filterActorIp" class="form-label">IP Aktor</label>
        <input type="text" id="filterActorIp" class="form-control" placeholder="192.168.x.x">
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

    <div class="card-datatable">
    <table class="datatables-audit table border-top">
      <thead class="table-light">
      <tr>
        <th class="control dt-orderable-none dtr-hidden" rowspan="1" colspan="1" style="width: 0px; display: none;">
        </th>
        <th></th>
        <th>No</th>
        <th class="text-center">Hostname</th>
        <th>IP Address</th>
        <th>Event Type</th>
        <th>Event Time</th>
        <th>Triggered By</th>
        <th>Actor IP</th>
      </tr>
      </thead>
    </table>
    </div>
  </div>
@endsection
