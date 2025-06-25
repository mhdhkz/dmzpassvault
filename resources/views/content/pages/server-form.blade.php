@extends('layouts/layoutMaster')

@section('title', ' Form Server')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite(['resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/select2/select2.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/server-form.js'])
@endsection

@section('content')
  <!-- Multi Column with Form Separator -->
  <div class="card mb-6">
    <div class="card-header border-bottom custom-header-bg">
    <h3 class="card-title mb-0 text-center text-white">Form Pendaftaran Server</h3>
    </div>
    <form class="card-body mt-8 mb-4" method="POST" action="{{ route('identities.store') }}">
    @csrf
    <h6>1. Alamat Server</h6>
    <div class="row g-4">
      <div class="col-md-6">
      <label class="form-label" for="multicol-hostname">Hostname</label>
      <input type="text" id="multicol-hostname" name="hostname" required class="form-control"
        value="{{ old('hostname') }}" placeholder="ex: dmzappp01" />
      </div>
      <div class="col-md-6">
      <label class="form-label" for="multicol-ipaddr">IP Address</label>
      <input type="text" id="multicol-ipaddr" name="ip_addr_srv" required class="form-control"
        value="{{ old('ip_addr_srv') }}" placeholder="ex: 10.0.0.1" />
      </div>
    </div>
    <hr class="my-6 mx-n6" />
    <h6>2. Detail Server</h6>
    <div class="row g-4">
      <div class="col-md-6">
      <label class="form-label" for="multicol-username">Username</label>
      <input type="text" id="multicol-username" name="username" required class="form-control"
        value="{{ old('username') }}" placeholder="ex: root" />
      </div>
      <div class="col-md-6">
      <label class="form-label" for="multicol-functionality">Functionality</label>
      <input type="text" id="multicol-functionality" name="functionality" required class="form-control"
        value="{{ old('functionality') }}" placeholder="ex: Web Server App A" />
      </div>
      <div class="col-md-6">
      <label class="form-label" for="multicol-platform">Platform</label>
      <select id="multicol-platform" class="select2 form-select" name="platform_id" required>
        <option value="">Pilih salah satu</option>
        @foreach ($platforms as $platform)
      <option value="{{ $platform->id }}" {{ old('platform_id') == $platform->id ? 'selected' : '' }}>
      {{ $platform->name }}
      </option>
      @endforeach
      </select>
      </div>
      <div class="col-md-6">
      <label class="form-label" for="multicol-description">Description</label>
      <textarea id="multicol-description" name="description" class="form-control" placeholder="Maksimal 500 karakter"
        maxlength="500">{{ old('description') }}</textarea>
      <small id="charCount" class="form-text text-muted">500 karakter tersisa</small>
      </div>
    </div>
    <div class="pt-6">
      <button type="submit" class="btn btn-primary me-4">Daftar Server</button>
      <button type="reset" class="btn btn-label-secondary">Batalkan</button>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mt-4">
      <strong>Terjadi kesalahan:</strong>
      <ul class="mb-0">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
      </ul>
    </div>
    @endif
    </form>
  </div>
  </div>
  </div>
@endsection
