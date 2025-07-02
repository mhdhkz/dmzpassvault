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
    'resources/assets/js/vault-detail.js'
  ])
@endsection

@section('content')
  <div class="card mb-4">
    <div class="card-body">
    <h5 class="pb-4 border-bottom mb-4">Detail Permintaan Vault</h5>

    <div class="row">
      <!-- Kolom Kiri -->
      <div class="col-md-6 border-end">
      <ul class="list-unstyled mb-0">
        <li class="mb-3"><span class="fw-semibold">Request ID:</span> {{ $vault->request_id }}</li>
        <li class="mb-3"><span class="fw-semibold">Purpose:</span> <span
          id="text-purpose">{{ $vault->purpose }}</span></li>
        <li class="mb-3">
        <span class="fw-semibold">Usage Time:</span><br>
        @if ($vault->start_at && $vault->end_at)
      <span>{{ $vault->start_at->format('d M Y H:i') }} s/d {{ $vault->end_at->format('d M Y H:i') }}</span>
      @else
      <span class="text-muted">-</span>
      @endif
        </li>
        <li class="mb-3">
        <span class="fw-semibold">Duration:</span>
        <span id="text-duration">{{ $vault->duration_friendly ?? '-' }}</span>
        </li>
        <li class="mb-3">
        <span class="fw-semibold">Status:</span>
        <span
          class="badge bg-label-{{ $vault->status === 'approved' ? 'success' : ($vault->status === 'rejected' ? 'danger' : 'warning') }}">
          {{ strtoupper($vault->status) }}
        </span>
        </li>
        <li class="mb-3"><span class="fw-semibold">Requestor:</span> {{ $vault->user->name ?? '-' }}</li>
        <li class="mb-3"><span class="fw-semibold">Created at:</span> {{ $vault->created_at->format('d M Y H:i') }}
        </li>
        <li class="mb-3"><span class="fw-semibold">Updated at:</span>
        {{ $vault->updated_at ? $vault->updated_at->format('d M Y H:i') : '-' }}
        </li>
      </ul>
      </div>

      <!-- Kolom Kanan -->
      <div class="col-md-6">
      <ul class="list-unstyled mb-0">
        <li class="mb-3"><span class="fw-semibold">Approved by:</span> {{ $vault->approvedBy->name ?? '-' }}</li>
        <li class="mb-3"><span class="fw-semibold">Approved at:</span>
        {{ $vault->approved_at ? $vault->approved_at->format('d M Y H:i') : '-' }}</li>
        <li class="mb-3"><span class="fw-semibold">Revealer:</span> {{ $vault->revealedBy->name ?? '-' }}</li>
        <li class="mb-3"><span class="fw-semibold">Revealed at:</span>
        {{ $vault->revealed_at?->format('d M Y H:i') ?? '-' }}</li>
        <li class="mb-3"><span class="fw-semibold">Revealer IP:</span> {{ $vault->reveal_ip ?? '-' }}</li>
        <li class="mb-3"><span class="fw-semibold">Revoked at:</span>
        {{ $vault->revoked_at?->format('d M Y H:i') ?? '-' }}</li>

        <li class="mb-3"><span class="fw-semibold">Requested Identity:</span>
        <ul class="list-group list-group-flush mt-2">
          @forelse ($vault->identities as $identity)
        <li class="list-group-item d-flex justify-content-between align-items-start px-0">
        <div>
        <strong>{{ $identity->hostname }}</strong><br>
        <small>{{ $identity->username . '@' . ($identity->ip_addr_srv ?? 'no-ip') }}</small>
        </div>
        <span class="badge bg-primary">{{ $identity->platform->name ?? 'N/A' }}</span>
        </li>
      @empty
        <li class="list-group-item text-muted">There are no identity requested.</li>
      @endforelse
        </ul>
        </li>
      </ul>
      </div>
    </div>

    <hr class="my-4 border-gray-600" />

    <div class="d-flex justify-content-center mt-3 flex-wrap gap-2">
      <a href="javascript:;" class="btn btn-success btn-approve-request" data-id="{{ $vault->id }}">Approve</a>
      <a href="javascript:;" class="btn btn-danger btn-reject-request" data-id="{{ $vault->id }}">Reject</a>
      <a href="javascript:;" class="btn btn-primary btn-edit-request" data-id="{{ $vault->id }}">Edit</a>
      <a href="javascript:;" class="btn btn-warning btn-delete-request" data-id="{{ $vault->id }}">Hapus</a>
    </div>

    </div>
  </div>

  @include('_partials/_modals/modal-edit-vault', ['platforms' => \App\Models\Platform::all()])

  <script>
    window.platformList = @json($platforms->pluck('name', 'id'));
  </script>
@endsection
