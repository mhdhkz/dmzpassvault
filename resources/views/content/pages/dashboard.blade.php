@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/spinkit/spinkit.scss'
  ])
@endsection

@section('page-style')
  @vite('resources/assets/vendor/scss/pages/app-logistics-dashboard.scss')
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
  ])
@endsection

@section('page-script')
  @vite('resources/assets/js/app-vault-dashboard.js')
@endsection

@section('content')
  <div class="row g-4">
    {{-- Statistik Cards --}}
    @php
    $cards = [
    ['title' => 'Total Identity', 'desc' => 'Server Linux dan DB', 'icon' => 'bx-server', 'class' => 'primary', 'value' => $totalIdentities],
    ['title' => 'Total Request Vault', 'desc' => 'Permintaan akses aktif', 'icon' => 'bx-key', 'class' => 'warning', 'value' => $totalRequests],
    ['title' => 'Total User', 'desc' => 'Pengguna terdaftar', 'icon' => 'bx-user', 'class' => 'info', 'value' => $totalUsers],
    ['title' => 'Job Rotasi', 'desc' => 'Dijalankan otomatis oleh sistem', 'icon' => 'bx-refresh', 'class' => 'danger', 'value' => $totalJobs, 'sub' => "Sukses: $jobSuccess, Gagal: $jobFailed"]
    ];
    @endphp
    @foreach ($cards as $card)
    <div class="col-lg-3 col-sm-6">
    <div class="card card-border-shadow-{{ $card['class'] }} h-100">
      <div class="card-body">
      <div class="d-flex align-items-center mb-2">
      <div class="avatar me-4">
      <span class="avatar-initial rounded bg-label-{{ $card['class'] }}">
        <i class="bx {{ $card['icon'] }} icon-lg"></i>
      </span>
      </div>
      <div>
      <h4 class="mb-0">{{ $card['value'] }}</h4>
      @isset($card['sub']) <small class="text-muted">{{ $card['sub'] }}</small> @endisset
      </div>
      </div>
      <p class="mb-2">{{ $card['title'] }}</p>
      <p class="mb-0 text-muted">{{ $card['desc'] }}</p>
      </div>
    </div>
    </div>
    @endforeach

    {{-- Pie Chart + Timeline --}}
    <div class="col-xxl-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h5 class="card-title mb-0">Status Permintaan Vault</h5>
        <small class="text-muted">Pie Chart Status</small>
      </div>
      <div class="d-flex gap-2">
        <select class="form-select chart-filter-month w-auto" id="pieChartMonthSelect" data-year="pieChartYearSelect">
        @for ($m = 1; $m <= 12; $m++)
      <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
      </option>
      @endfor
        </select>
        <select class="form-select chart-filter-year w-auto" id="pieChartYearSelect" data-month="pieChartMonthSelect">
        @for ($y = now()->year; $y >= now()->year - 3; $y--)
      <option value="{{ $y }}">{{ $y }}</option>
      @endfor
        </select>
      </div>
      </div>
      <div class="card-body position-relative">
      <div id="requestStatusPieChart" class="w-100 position-relative">
        <div id="requestChartSpinner" class="position-absolute top-50 start-50 translate-middle zindex-2 d-none">
        <div class="sk-fold">
          <div class="sk-fold-cube"></div>
          <div class="sk-fold-cube"></div>
          <div class="sk-fold-cube"></div>
          <div class="sk-fold-cube"></div>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>

    {{-- Timeline Recent Activity --}}
    <div class="col-xxl-6">
    <div class="card h-100">
      <div class="card-header">
      <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
      <small class="text-muted">Dari log audit sistem</small>
      </div>
      <div class="card-body">
      <ul class="timeline">
        @foreach($recentActivities as $log)
      <li class="timeline-item">
      <span class="timeline-point {{ $log->event_type === 'rotated' ? 'bg-warning' : 'bg-primary' }}"></span>
      <div class="timeline-event">
        <div class="d-flex justify-content-between">
        <h6 class="mb-1">{{ ucfirst($log->event_type) }}</h6>
        <small>{{ $log->event_time->diffForHumans() }}</small>
        </div>
        <p class="mb-0">{{ $log->note }}</p>
        <small class="text-muted">User: {{ $log->user->name ?? 'System' }} | IP: {{ $log->actor_ip_addr }}</small>
      </div>
      </li>
      @endforeach
      </ul>
      </div>
    </div>
    </div>

    {{-- Bar Chart Vault Activity (tanpa filter) --}}
    <div class="col-12">
    <div class="card h-100">
      <div class="card-header">
      <h5 class="card-title mb-0">Statistik Aktivitas Vault</h5>
      <small class="text-muted">Data berdasarkan log audit</small>
      </div>
      <div class="card-body">
      <div id="vaultActivityChart" class="w-100 position-relative">
        <div id="vaultChartSpinner" class="position-absolute top-50 start-50 translate-middle zindex-2 d-none">
        <div class="sk-fold">
          <div class="sk-fold-cube"></div>
          <div class="sk-fold-cube"></div>
          <div class="sk-fold-cube"></div>
          <div class="sk-fold-cube"></div>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>

  <script>
    window.auditStats = @json($chartData);
    window.requestStatusData = @json($requestStatusData);
  </script>
@endsection