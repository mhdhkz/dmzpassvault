@extends('layouts/layoutMaster')

@section('title', 'List Identity')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/identity-list.js')
@endsection

@section('content')
  <div class="row g-6 mb-6">
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div class="content-left">
        <span class="text-heading">Session</span>
        <div class="d-flex align-items-center my-1">
          <h4 class="mb-0 me-2">21,459</h4>
          <p class="text-success mb-0">(+29%)</p>
        </div>
        <small class="mb-0">Total Users</small>
        </div>
        <div class="avatar">
        <span class="avatar-initial rounded bg-label-primary">
          <i class="icon-base bx bx-group icon-lg"></i>
        </span>
        </div>
      </div>
      </div>
    </div>
    </div>
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div class="content-left">
        <span class="text-heading">Paid Users</span>
        <div class="d-flex align-items-center my-1">
          <h4 class="mb-0 me-2">4,567</h4>
          <p class="text-success mb-0">(+18%)</p>
        </div>
        <small class="mb-0">Last week analytics </small>
        </div>
        <div class="avatar">
        <span class="avatar-initial rounded bg-label-danger">
          <i class="icon-base bx bx-user-plus icon-lg"></i>
        </span>
        </div>
      </div>
      </div>
    </div>
    </div>
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div class="content-left">
        <span class="text-heading">Active Users</span>
        <div class="d-flex align-items-center my-1">
          <h4 class="mb-0 me-2">19,860</h4>
          <p class="text-danger mb-0">(-14%)</p>
        </div>
        <small class="mb-0">Last week analytics</small>
        </div>
        <div class="avatar">
        <span class="avatar-initial rounded bg-label-success">
          <i class="icon-base bx bx-user-check icon-lg"></i>
        </span>
        </div>
      </div>
      </div>
    </div>
    </div>
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
      <div class="d-flex align-items-start justify-content-between">
        <div class="content-left">
        <span class="text-heading">Pending Users</span>
        <div class="d-flex align-items-center my-1">
          <h4 class="mb-0 me-2">237</h4>
          <p class="text-success mb-0">(+42%)</p>
        </div>
        <small class="mb-0">Last week analytics</small>
        </div>
        <div class="avatar">
        <span class="avatar-initial rounded bg-label-warning">
          <i class="icon-base bx bx-user-voice icon-lg"></i>
        </span>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>
  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">List Identity</h3>
    </div>

    <!-- Collapsible filter -->
    <div class="card-header">
    <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#serverFilters"
      aria-expanded="true">
      <span class="icon-base bx bx-filter icon-sm me-2"></span>Filter <i class="bx bx-chevron-down"></i>
    </button>
    </div>

    <div class="collapse" id="serverFilters">
    <div class="card-body">
      <div class="row g-4">
      <div class="col-md-4 server_username"></div>
      <div class="col-md-4 server_functionality"></div>
      <div class="col-md-4 server_platform"></div>
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
    <table class="datatables-users table border-top">
      <thead class="table-light">
      <tr>
        <th class="control dt-orderable-none dtr-hidden" rowspan="1" colspan="1" style="width: 0px; display: none;"
        aria-label=""></th>

        <th data-dt-column="1" class="dt-orderable-none" tabindex="-1" aria-label="Checkbox: Not sortable"></th>

        <th data-dt-column="2" class="dt-orderable-none" tabindex="-1" aria-label="No: Not sortable">No</th>

        <th data-dt-column="3" class="sorting" tabindex="0" aria-label="Hostname: Activate to sort">
        <span class="dt-column-title" role="button">Hostname</span>
        </th>

        <th data-dt-column="4" class="sorting" tabindex="0" aria-label="IP Address: Activate to sort">
        <span class="dt-column-title" role="button">IP Address</span>
        </th>

        <th data-dt-column="5" class="sorting" tabindex="0" aria-label="Username: Activate to sort">
        <span class="dt-column-title" role="button">Username</span>
        </th>

        <th data-dt-column="6" class="sorting" tabindex="0" aria-label="Functionality: Activate to sort">
        <span class="dt-column-title" role="button">Functionality</span>
        </th>

        <th data-dt-column="7" class="sorting" tabindex="0" aria-label="Platform: Activate to sort">
        <span class="dt-column-title" role="button">Platform</span>
        </th>
        <th data-dt-column="8" class="dt-orderable-none" tabindex="-1" aria-label="Actions: Not sortable">
        <span class="dt-column-title">Actions</span>
        </th>
      </tr>
      </thead>
    </table>
    </div>
  </div>
  @include('_partials/_modals/modal-edit-identity', ['platforms' => \App\Models\Platform::all()])
  <script>
    window.platformList = @json($platforms->pluck('name', 'id'));
  </script>
@endsection