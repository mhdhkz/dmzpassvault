@extends('layouts/layoutMaster')

@section('title', 'Dekripsi')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
  @vite('resources/assets/js/vault-decrypt.js')
@endsection

@section('content')

  <!-- Users List Table -->
  <div class="card">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">Dekripsi Vault</h3>
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

        <th data-dt-column="3" class="sorting text-center" tabindex="0" aria-label="Hostname: Activate to sort">
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