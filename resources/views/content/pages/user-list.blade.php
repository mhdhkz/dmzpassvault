@extends('layouts/layoutMaster')

@section('title', 'List User')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  <script>
    window.USER_ROLE = "{{ auth()->user()->role }}";
    window.USER_ID = "{{ auth()->id() }}";
  </script>
  @vite(['resources/assets/js/user-list.js', 'resources/assets/js/modal-edit-user.js'])
@endsection

@section('content')
  <div class="row g-4 mb-4" id="userStats">
    <!-- Total User -->
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div class="content-left">
        <span class="text-heading">Total User</span>
        <h4 class="mb-0 mt-1" id="total-user">0</h4>
      </div>
      <div class="avatar bg-label-primary rounded-circle d-flex justify-content-center align-items-center"
        style="width: 48px; height: 48px;">
        <i class="icon-base bx bx-group icon-lg"></i>
      </div>
      </div>
    </div>
    </div>

    <!-- Sistem -->
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div class="content-left">
        <span class="text-heading">Sistem</span>
        <h4 class="mb-0 mt-1" id="total-sistem">0</h4>
      </div>
      <div class="avatar bg-label-warning rounded-circle d-flex justify-content-center align-items-center"
        style="width: 48px; height: 48px;">
        <i class="icon-base bx bx-cog icon-lg"></i>
      </div>
      </div>
    </div>
    </div>

    <!-- Admin -->
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div class="content-left">
        <span class="text-heading">Admin</span>
        <h4 class="mb-0 mt-1" id="total-admin">0</h4>
      </div>
      <div class="avatar bg-label-danger rounded-circle d-flex justify-content-center align-items-center"
        style="width: 48px; height: 48px;">
        <i class="icon-base bx bx-user-pin icon-lg"></i>
      </div>
      </div>
    </div>
    </div>

    <!-- Regular User -->
    <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
      <div class="content-left">
        <span class="text-heading">Regular User</span>
        <h4 class="mb-0 mt-1" id="total-regular">0</h4>
      </div>
      <div class="avatar bg-label-success rounded-circle d-flex justify-content-center align-items-center"
        style="width: 48px; height: 48px;">
        <i class="icon-base bx bx-user icon-lg"></i>
      </div>
      </div>
    </div>
    </div>
  </div>


  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">List User</h3>
    </div>

    <!-- Collapsible filter -->
    <div class="card-header">
    <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#userFilters"
      aria-expanded="true">
      <span class="icon-base bx bx-filter icon-sm me-2"></span>Filter <i class="bx bx-chevron-down"></i>
    </button>
    </div>

    <div class="collapse" id="userFilters">
    <div class="card-body">
      <div class="row g-4">
      <div class="col-md-4 user_role"></div>
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
    <table class="datatables-userlist table border-top">
      <thead class="table-light">
      <tr>
        <th class="control dt-orderable-none dtr-hidden" rowspan="1" colspan="1" style="width: 0px; display: none;"
        aria-label=""></th>

        <th data-dt-column="1" class="dt-orderable-none" tabindex="-1" aria-label="Checkbox: Not sortable"></th>

        <th data-dt-column="2" class="dt-orderable-none" tabindex="-1" aria-label="No: Not sortable">No</th>

        <th data-dt-column="3" class="sorting" tabindex="0" aria-label="Name: Activate to sort">
        <span class="dt-column-title" role="button">Name</span>
        </th>

        <th data-dt-column="4" class="sorting" tabindex="0" aria-label="Email: Activate to sort">
        <span class="dt-column-title" role="button">Email</span>
        </th>

        <th data-dt-column="5" class="sorting" tabindex="0" aria-label="Role: Activate to sort">
        <span class="dt-column-title" role="button">Role</span>
        </th>
        <th data-dt-column="8" class="dt-orderable-none" tabindex="-1" aria-label="Actions: Not sortable">
        <span class="dt-column-title">Actions</span>
        </th>
      </tr>
      </thead>
    </table>
    </div>
  </div>
  @include('_partials/_modals/modal-edit-user')
@endsection