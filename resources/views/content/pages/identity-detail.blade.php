@extends('layouts/layoutMaster')

@section('title', 'Detail Identity')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/modal-edit-identity.js', 'resources/assets/js/identity-detail.js', 'resources/assets/js/identity-view-table.js'])
@endsection

@section('content')
  <div class="row">
    <!-- User Sidebar -->
    <div class="col-xl-6 col-lg-6 mb-4">
    <!-- Identity Details -->
    <div class="card h-100">
      <div class="card-body">
      <div class="user-avatar-section">
        <div class=" d-flex align-items-center flex-column">
        @php
      $parts = preg_split('/[\s\-]+/', $identity->hostname);
      $initials = '';
      foreach ($parts as $p) {
        if (isset($p[0]))
        $initials .= strtoupper($p[0]);
        if (strlen($initials) === 2)
        break;
      }
      @endphp
        <div class="d-flex align-items-center flex-column mt-n4 mb-3">
          <div class="avatar" style="width: 70px; height: 70px;">
          <span
            class="mt-1 avatar-initial rounded-circle bg-label-primary text-heading d-inline-flex justify-content-center align-items-center"
            style="width: 100%; height: 100%; font-size: 24px;">
            {{ $initials }}
          </span>
          </div>
          <h5 class="mt-2 mb-0 text-center">{{ $identity->hostname }}</h5>
        </div>
        </div>
      </div>
      <h5 class="pb-4 border-bottom mb-4">Details</h5>
      <div class="info-container">
        <ul class="list-unstyled mb-6">
        <li class="mb-2">
          <span class="h6">Hostname:</span>
          <span id="text-hostname">{{ $identity->hostname }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">IP Address:</span>
          <span id="text-ip">{{ $identity->ip_addr_srv }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Username:</span>
          <span id="text-username">{{ $identity->username }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Functionality:</span>
          <span id="text-functionality">{{ $identity->functionality }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Platform:</span>
          <span id="text-platform">{{ $identity->platform->name ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Created By:</span>
          <span>{{ $identity->createdBy->name ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Created At:</span>
          <span>{{ $identity->created_at ? $identity->created_at->format('d M Y H:i') : '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Updated By:</span>
          <span id="text-updated-by">{{ $identity->updatedBy->name ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Updated At:</span>
          <span
          id="text-updated-at">{{ $identity->updated_at ? $identity->updated_at->format('d M Y H:i') : '-' }}</span>
        </li>
        </ul>
        <hr class="my-4 border-gray-600" />
        <div class="d-flex justify-content-center">
        <a href="javascript:;" class="btn btn-primary me-4 btn-edit-identity" data-bs-target="#editIdentity"
          data-id="{{ $identity->id }}" data-hostname="{{ $identity->hostname }}"
          data-ip_addr_srv="{{ $identity->ip_addr_srv }}" data-platform_id="{{ $identity->platform_id }}"
          data-username="{{ $identity->username }}" data-functionality="{{ $identity->functionality }}"
          data-description="{{ $identity->description }}" data-bs-toggle="modal">Edit</a>
        <a href="javascript:;" class="btn btn-danger delete-identity" data-id="{{ $identity->id }}">
          Hapus
        </a>
        </div>
      </div>
      </div>
    </div>
    <!-- /Identity Details -->
    </div>
    <!--/ User Sidebar -->

    <!-- Activity Timeline -->
    <div class="col-xl-6 col-lg-6 mb-4">
    <div class="card h-100">
      <h5 class="card-header">Timeline Akses Identity</h5>
      <div class="card-body pt-1">
      <ul class="timeline mb-0">
        @forelse ($timelineLogs as $log)
        @php
      $styles = match ($log->event_type) {
      'created' => ['color' => 'primary', 'icon' => 'bx-plus-circle'],
      'updated' => ['color' => 'info', 'icon' => 'bx-edit'],
      'rotated' => ['color' => 'warning', 'icon' => 'bx-refresh'],
      'requested' => ['color' => 'secondary', 'icon' => 'bx-key'],
      'accessed' => ['color' => 'success', 'icon' => 'bx-show'],
      default => ['color' => 'dark', 'icon' => 'bx-info-circle'],
      };
      @endphp

        <li class="timeline-item timeline-item-transparent">
        <span class="timeline-point timeline-point-{{ $styles['color'] }}">
        </span>
        <div class="timeline-event">
        <div class="timeline-header mb-2">
        <h6 class="mb-0 text-capitalize">
          <i class="bx {{ $styles['icon'] }} me-1"></i>
          {{ $log->event_type }}
        </h6>
        <small class="text-body-secondary">
          {{ $log->event_time->diffForHumans() }}
        </small>
        </div>
        <p class="mb-1">
        @if ($log->triggered_by === 'system')
        <span class="badge bg-label-warning"><i class="bx bx-chip me-1"></i> Sistem</span>
      @else
        <span class="badge bg-label-primary"><i class="bx bx-user me-1"></i>
        {{ $log->user->name ?? 'Unknown' }}</span>
      @endif
        </p>
        @if ($log->note)
        <p class="mb-1"><small class="text-muted fst-italic">{{ $log->note }}</small></p>
      @endif
        @if ($log->actor_ip_addr)
        <small class="text-muted">IP: {{ $log->actor_ip_addr }}</small>
      @endif
        </div>
        </li>


      @empty
      <li class="timeline-item">
      <div class="timeline-event">
        <div class="timeline-header">
        <h6 class="mb-0">Belum ada aktivitas</h6>
        </div>
      </div>
      </li>
      @endforelse
      </ul>
      </div>
    </div>
    </div>
    <!-- /Activity Timeline -->

    <!-- Project table -->
    <div class="col-12">
    <div class="card">
      <h5 class="card-header pb-0 text-md-start text-center">Daftar Aktivitas Akses Identity</h5>
      <div class="card-datatable table-responsive mb-4">
      <table class="table table-hover table-bordered datatable-activity w-100" data-identity-id="{{ $identity->id }}">
        <thead>
        <tr>
          <th>No</th>
          <th>Tipe Aksi</th>
          <th>Waktu</th>
          <th>User</th>
          <th>IP Address</th>
        </tr>
        </thead>
      </table>
      </div>
    </div>
    </div>
    <!-- /Project table -->

    <!-- Modal -->
    @include('_partials/_modals/modal-edit-identity', ['platforms' => \App\Models\Platform::all()])
    <!-- /Modal -->
    <script>
    window.platformList = @json($platforms->pluck('name', 'id'));
    </script>
  @endsection
