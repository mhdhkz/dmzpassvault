@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Str;
@endphp

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
  <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
    <a href="{{ url('/') }}" class="app-brand-link">
    <span class="app-brand-logo demo">
      @include('_partials.macros', ["height" => 20])
    </span>
    <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }} PV</span>
    </a>
  </div>
  <!-- Display menu close icon only for horizontal-menu with navbar-full -->
  @if (isset($menuHorizontal))
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
    <i class="icon-base bx bx-chevron-left d-flex align-items-center justify-content-center"></i>
    </a>
  @endif
  </div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
  <div
    class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
    <i class="icon-base bx bx-menu icon-md"></i>
    </a>
  </div>
@endif

@if (!isset($menuHorizontal))
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper mb-0">
    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
      <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
    </a>
    </div>
  </div>
  <!-- /Search -->
@endif

<ul class="navbar-nav flex-row align-items-center ms-md-auto">
  @if (isset($menuHorizontal))
    <!-- Search -->
    <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
      <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
    </a>
    </li>
    <!-- /Search -->
  @endif


  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    @if ($configData['hasCustomizer'] == true)
    <!-- Style Switcher -->
    <div class="navbar-nav align-items-center">
      <li class="nav-item dropdown me-2 me-xl-0">
      <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
        <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="nav-theme-text">
        <li>
        <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
          aria-pressed="false">
          <span><i class="icon-base bx bx-sun icon-md me-3" data-icon="sun"></i>Light</span>
        </button>
        </li>
        <li>
        <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
          aria-pressed="true">
          <span><i class="icon-base bx bx-moon icon-md me-3" data-icon="moon"></i>Dark</span>
        </button>
        </li>
        <li>
        <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
          aria-pressed="false">
          <span><i class="icon-base bx bx-desktop icon-md me-3" data-icon="desktop"></i>System</span>
        </button>
        </li>
      </ul>
      </li>
    </div>
    <!-- / Style Switcher-->
  @endif
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            @php
        $user = Auth::user();
        $name = $user?->name ?? 'Guest User';
        $initials = collect(explode(' ', $name))->map(fn($n) => strtoupper($n[0]))->join('');
      @endphp

            @if ($user && $user->profile_photo_path)
        <img src="{{ $user->profile_photo_url }}" alt="{{ $name }}" class="w-px-40 h-auto rounded-circle" />
      @else
        <span
          class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center text-uppercase fw-semibold"
          style="width: 40px; height: 40px;">
          {{ Str::limit($initials, 2, '') }}
        </span>
      @endif

          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item"
              href="{{ Route::has('profile.show') ? route('profile.show') : 'javascript:void(0);' }}">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    @php
            $user = Auth::user();
            $name = $user?->name ?? 'Guest User';
            $initials = collect(explode(' ', $name))->map(fn($n) => strtoupper($n[0]))->join('');
            @endphp

                    @if ($user && $user->profile_photo_path)
            <img src="{{ $user->profile_photo_url }}" alt="{{ $name }}" class="w-px-40 h-auto rounded-circle" />
          @else
            <span
              class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center text-uppercase fw-semibold"
              style="width: 40px; height: 40px;">
              {{ Str::limit($initials, 2, '') }}
            </span>
          @endif

                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-0">
                    @if (Auth::check())
            {{ Auth::user()->name }}
          @else
            Test
          @endif
                  </h6>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider my-1"></div>
          </li>
          @if (Auth::check() && Laravel\Jetstream\Jetstream::hasApiFeatures())
        <li>
        <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
          <i class="icon-base bx bx-key icon-md me-3"></i><span>API Tokens</span>
        </a>
        </li>
      @endif
          @if (Auth::User() && Laravel\Jetstream\Jetstream::hasTeamFeatures())
          <li>
          <h6 class="dropdown-header">Manage Team</h6>
          </li>
          <li>
          <div class="dropdown-divider my-1"></div>
          </li>
          <li>
          <a class="dropdown-item"
            href="{{ Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
            <i class="icon-base bx bx-cog icon-md me-3"></i><span>Team Settings</span>
          </a>
          </li>
          @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
        <li>
        <a class="dropdown-item" href="{{ route('teams.create') }}">
          <i class="icon-base bx bx-user icon-md me-3"></i><span>Create New Team</span>
        </a>
        </li>
        @endcan
          @if (Auth::user()->allTeams()->count() > 1)
        <li>
        <div class="dropdown-divider my-1"></div>
        </li>
        <li>
        <h6 class="dropdown-header">Switch Teams</h6>
        </li>
        <li>
        <div class="dropdown-divider my-1"></div>
        </li>
        @endif
          @if (Auth::user())
          @foreach (Auth::user()->allTeams() as $team)
        {{-- Below commented code read by artisan command while installing jetstream. !! Do not remove if you want to
        use jetstream. --}}

        <x-switchable-team :team="$team" />
        @endforeach
        @endif
      @endif
          @if (Auth::check())
        <li>
        <a class="dropdown-item" href="{{ route('logout') }}"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Logout</span>
        </a>
        </li>
        <form method="POST" id="logout-form" action="{{ route('logout') }}">
        @csrf
        </form>
      @else
        <li>
        <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}">
          <i class="icon-base bx bx-log-in icon-md me-3"></i><span>Login</span>
        </a>
        </li>
      @endif
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>