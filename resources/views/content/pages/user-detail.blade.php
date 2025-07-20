@extends('layouts/layoutMaster')

@section('title', 'Profil User')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/spinkit/spinkit.scss'
  ])
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js'
  ])
@endsection

@section('page-script')
  <script>
    window.USER_ROLE = "{{ auth()->user()->role }}";
    window.CURRENT_USER_ID = "{{ auth()->id() }}";
  </script>
  @vite([
    'resources/assets/js/modal-edit-user-detail.js',
    'resources/assets/js/modal-change-password.js',
    'resources/assets/js/user-detail.js',
    'resources/assets/js/user-view-table.js'
  ])
@endsection

@section('content')
  <div class="row">
    <!-- User Sidebar -->
    <div class="col-xl-6 col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
      <div class="user-avatar-section">
        <div class="d-flex align-items-center flex-column mb-3">
        <div class="avatar" style="width: 70px; height: 70px;">
          <span
          class="avatar-initial rounded-circle bg-label-primary text-heading d-inline-flex justify-content-center align-items-center"
          style="width: 100%; height: 100%; font-size: 24px;">
          @php
        $nameParts = explode(' ', trim($user->name));
        $initials = '';
        if (count($nameParts) === 1) {
        $initials = strtoupper(substr($nameParts[0], 0, 1));
        } else {
        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
        }
      @endphp
          {{ $initials }}
          </span>
        </div>
        <h5 class="mt-2 mb-1 text-center">{{ $user->name }}</h5>
        <div class="text-center">
          @php
      $roleColor = match ($user->role) {
        'admin' => 'danger',
        'system' => 'secondary',
        default => 'info',
      };
      @endphp
          <span class="badge bg-label-{{ $roleColor }} text-capitalize">{{ $user->role }}</span>
        </div>

        </div>
      </div>

      <h5 class="pb-4 border-bottom mb-4">Infomasi Detail</h5>
      <div class="info-container">
        <ul class="list-unstyled mb-6">
        <li class="mb-2">
          <span class="h6">Name:</span>
          <span id="text-name">{{ $user->name }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Email:</span>
          <span id="text-email">{{ $user->email }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Role:</span>
          <span id="text-role">{{ $user->role }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Birth Date:</span>
          <span>{{ $user->birth_date ? $user->birth_date->format('d M Y') : '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Nationality:</span>
          <span>{{ $user->nationality ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Employee ID:</span>
          <span>{{ $user->employee_id ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Job Title:</span>
          <span>{{ $user->job_title ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Position:</span>
          <span>{{ $user->position?->name ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Work Mode:</span>
          <span>{{ $user->work_mode ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Work Location:</span>
          <span>{{ $user->work_location ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Created At:</span>
          <span>{{ $user->created_at?->format('d M Y H:i') ?? '-' }}</span>
        </li>
        <li class="mb-2">
          <span class="h6">Updated At:</span>
          <span id="text-updated-at">{{ $user->updated_at?->format('d M Y H:i') ?? '-' }}</span>
        </li>
        </ul>
        @php
      $isAdmin = auth()->user()->role === 'admin';
      $isSelf = auth()->id() === $user->id;
    @endphp

        <div class="d-flex flex-wrap justify-content-center gap-2">

        @php
      $isAdmin = auth()->user()->role === 'admin';
      $isSelf = auth()->id() === $user->id;
      @endphp

        <div class="d-flex flex-wrap justify-content-center gap-2">
          @if ($isAdmin || $isSelf)
        <a href="javascript:;" class="btn btn-primary btn-edit-user-detail" data-bs-toggle="modal"
        data-bs-target="#editUserDetailModal" data-id="{{ $user->id }}" data-name="{{ $user->name }}"
        data-email="{{ $user->email }}" data-role="{{ $user->role }}"
        data-birth_date="{{ $user->birth_date?->format('Y-m-d') }}" data-nationality="{{ $user->nationality }}"
        data-employee_id="{{ $user->employee_id }}" data-job_title="{{ $user->job_title }}"
        data-position_id="{{ $user->position_id }}" data-work_mode="{{ $user->work_mode }}"
        data-work_location="{{ $user->work_location }}">
        <i class="bx bx-edit-alt me-1"></i> Edit Info
        </a>

        <a href="javascript:;" class="btn btn-warning btn-password-user" data-bs-toggle="modal"
        data-bs-target="#changePassword" data-id="{{ $user->id }}" data-name="{{ $user->name }}">
        <i class="bx bx-key me-1"></i> Ganti Password
        </a>
      @endif

          @if ($isAdmin && !$isSelf)
        <a href="javascript:;" class="btn btn-danger delete-user" data-id="{{ $user->id }}">
        <i class="bx bx-trash me-1"></i> Hapus
        </a>
      @endif
        </div>


        </div>
      </div>
      </div>
    </div>
    </div>
    <!--/ User Sidebar -->

    <!-- Timeline -->
    <div class="col-xl-6 col-lg-6 mb-4">
    <div class="card h-100">
      <h5 class="card-header">Timeline Aktivitas User</h5>
      <div class="card-body pt-1">
      <ul class="timeline mb-0">
        @forelse ($timelineLogs as $log)
        @php
      $styles = match ($log->event_type) {
      'created' => ['color' => 'primary', 'icon' => 'bx-plus-circle'],
      'updated' => ['color' => 'info', 'icon' => 'bx-edit'],
      'login' => ['color' => 'success', 'icon' => 'bx-log-in'],
      'logout' => ['color' => 'danger', 'icon' => 'bx-log-out'],
      default => ['color' => 'dark', 'icon' => 'bx-info-circle'],
      };
      @endphp
        <li class="timeline-item timeline-item-transparent">
        <span class="timeline-point timeline-point-{{ $styles['color'] }}"></span>
        <div class="timeline-event">
        <div class="timeline-header mb-2">
        <h6 class="mb-0 text-capitalize">
          <i class="bx {{ $styles['icon'] }} me-1"></i>
          {{ $log->event_type }}
        </h6>
        <small class="text-body-secondary">{{ $log->event_time->diffForHumans() }}</small>
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
    <!-- /Timeline -->

    <!-- Activity Table -->
    <div class="col-12">
    <div class="card">
      <h5 class="card-header pb-0 text-md-start text-center">Riwayat Aktivitas User</h5>
      <div class="card-datatable table-responsive mb-4">
      <table class="table table-hover table-bordered datatable-activity w-100" data-user-id="{{ $user->id }}">
        <thead>
        <tr>
          <th>No</th>
          <th>Activity</th>
          <th>Time</th>
          <th>User</th>
          <th>IP Address</th>
        </tr>
        </thead>
      </table>
      </div>
    </div>
    </div>
    <!-- /Activity Table -->

    <!-- Modal -->
    @include('_partials/_modals/modal-edit-user-detail')
    @include('_partials/_modals/modal-change-password')
    <!-- /Modal -->
  @endsection