@extends('layouts/layoutMaster')

@section('title', 'Detail Vault')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
    'resources/assets/vendor/libs/spinkit/spinkit.scss',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
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
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
    'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
  ])
@endsection

@section('page-script')
  @vite([
    'resources/assets/js/modal-edit-vault.js',
    'resources/assets/js/modal-edit-identity-vault.js',
    'resources/assets/js/vault-detail.js'
  ])
@endsection

@section('content')
  <div class="row">
    <!-- Identity Details -->
    <div class="col-xl-6 col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
      <div class="user-avatar-section">
        <div class="d-flex align-items-center flex-column">
        @php
      $hostnameText = $identity?->hostname ?? '';
      $parts = preg_split('/[\s\-]+/', $hostnameText);
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
          <h5 class="mt-2 mb-0 text-center {{ !$hostnameText ? 'text-danger' : '' }}" style="font-size: 1.125rem;">
          {{ $hostnameText ?: 'Identity has been deleted' }}
          </h5>
        </div>
        </div>
      </div>
      <h5 class="pb-4 border-bottom mb-4">Details</h5>
      <div class="info-container">
        <ul class="list-unstyled mb-6">
        <li class="mb-2"><span class="h6">Hostname:</span> <span
          id="text-hostname">{{ $identity?->hostname ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">IP Address:</span> <span
          id="text-ip">{{ $identity?->ip_addr_srv ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Username:</span> <span
          id="text-username">{{ $identity?->username ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Functionality:</span> <span
          id="text-functionality">{{ $identity?->functionality ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Platform:</span> <span
          id="text-platform">{{ $identity?->platform?->name ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Created By:</span> <span>{{ $identity?->createdBy?->name ?? '-' }}</span>
        </li>
        <li class="mb-2"><span class="h6">Created At:</span>
          <span>{{ $identity?->created_at?->format('d M Y H:i') ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Updated By:</span> <span
          id="text-updated-by">{{ $identity?->updatedBy?->name ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Updated At:</span> <span
          id="text-updated-at">{{ $identity?->updated_at?->format('d M Y H:i') ?? '-' }}</span></li>
        </ul>
        <hr class="my-4 border-gray-600" />
        <div class="d-flex justify-content-center">
        <a href="javascript:;" class="btn btn-primary me-4 btn-edit-identity" data-bs-target="#editIdentity"
          data-id="{{ $identity?->id }}" data-hostname="{{ $identity?->hostname }}"
          data-ip_addr_srv="{{ $identity?->ip_addr_srv }}" data-platform_id="{{ $identity?->platform_id }}"
          data-username="{{ $identity?->username }}" data-functionality="{{ $identity?->functionality }}"
          data-description="{{ $identity?->description }}" data-bs-toggle="modal">Edit</a>
        <a href="javascript:;" class="btn btn-danger delete-identity" data-id="{{ $identity?->id }}">Hapus</a>
        </div>
      </div>
      </div>
    </div>
    </div>

    <!-- Vault Request Details -->
    <div class="col-xl-6 col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
      <h5 class="pb-4 border-bottom mb-4">Detail Permintaan Vault</h5>
      <ul class="list-unstyled">
        <li class="mb-2"><span class="h6">Request ID:</span> <span>{{ $vault->request_id }}</span></li>
        <li class="mb-2"><span class="h6">Purpose:</span> <span id="text-purpose">{{ $vault->purpose }}</span></li>
        <li class="mb-2"><span class="h6">Durations:</span> <span id="text-duration">{{ $vault->duration_minutes }}
          menit</span></li>
        <li class="mb-2"><span class="h6">Status:</span>
        <span
          class="badge bg-label-{{ $vault->status === 'approved' ? 'success' : ($vault->status === 'rejected' ? 'danger' : 'warning') }}">
          {{ strtoupper($vault->status) }}
        </span>
        </li>
        <li class="mb-2"><span class="h6">Created by:</span> <span>{{ $vault->user->name ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Created at:</span>
        <span>{{ $vault->created_at->format('d M Y H:i') }}</span></li>
        <li class="mb-2"><span class="h6">Approved by:</span> <span>{{ $vault->approvedBy->name ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Approved at:</span>
        <span>{{ $vault->approved_at ? \Carbon\Carbon::parse($vault->approved_at)->format('d M Y H:i') : '-' }}</span>
        </li>
        <li class="mb-2"><span class="h6">Revealed by:</span> <span>{{ $vault->revealedBy->name ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Revealed at:</span>
        <span>{{ $vault->revealed_at?->format('d M Y H:i') ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Revealer IP:</span> <span>{{ $vault->reveal_ip ?? '-' }}</span></li>
        <li class="mb-2"><span class="h6">Canceled at:</span>
        <span>{{ $vault->revoked_at?->format('d M Y H:i') ?? '-' }}</span></li>
      </ul>
      <hr class="my-4 border-gray-600" />
      <div class="d-flex justify-content-center mt-3">
        <a href="javascript:;" class="btn btn-primary me-2 btn-edit-request" data-id="{{ $vault->id }}"
        data-request_id="{{ $vault->request_id }}" data-purpose="{{ $vault->purpose }}"
        data-duration="{{ $vault->duration_minutes }}" data-status="{{ $vault->status }}"
        data-approved_by="{{ $vault->approved_by }}" data-revealed_by="{{ $vault->revealed_by }}"
        data-reveal_ip="{{ $vault->reveal_ip }}" data-approved_at="{{ $vault->approved_at }}"
        data-revealed_at="{{ $vault->revealed_at }}" data-revoked_at="{{ $vault->revoked_at }}"
        data-bs-toggle="modal" data-bs-target="#editRequestModal">Edit</a>
        <a href="javascript:;" class="btn btn-danger btn-delete-request" data-id="{{ $vault->id }}">Hapus</a>
      </div>
      </div>
    </div>
    </div>
  </div>
  @include('_partials/_modals/modal-edit-identity', ['platforms' => \App\Models\Platform::all()])
  @include('_partials/_modals/modal-edit-vault', ['platforms' => \App\Models\Platform::all()])
  <script>
    window.platformList = @json($platforms->pluck('name', 'id'));
  </script>
@endsection
