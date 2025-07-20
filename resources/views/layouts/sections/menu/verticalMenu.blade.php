@php
  use Illuminate\Support\Facades\Route;
  $configData = Helper::appClasses();
  $userRole = auth()->user()->role; // pastikan sudah diset di middleware/auth
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme"
  @foreach ($configData['menuAttributes'] as $attribute => $value)
    {{ $attribute }}="{{ $value }}"
  @endforeach>

  @if (!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">@include('_partials.macros')</span>
        <span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('variables.templateName') }} PV</span>
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="icon-base bx bx-chevron-left"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData[0]['menu'] as $menu)
      @php
        // Filter berdasarkan role
        if (isset($menu['roles']) && !in_array($userRole, $menu['roles'])) {
          continue;
        }
      @endphp

      {{-- Menu Header --}}
      @if (isset($menu['menuHeader']))
        <li class="menu-header small">
          <span class="menu-header-text">{{ __($menu['menuHeader']) }}</span>
        </li>
      @else
        {{-- Active class logic --}}
        @php
          $activeClass = null;
          $currentRouteName = Route::currentRouteName();

          if ($currentRouteName === $menu['slug']) {
            $activeClass = 'active';
          } elseif (isset($menu['submenu'])) {
            if (is_array($menu['slug'])) {
              foreach ($menu['slug'] as $slug) {
                if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                  $activeClass = 'active open';
                }
              }
            } else {
              if (str_contains($currentRouteName, $menu['slug']) && strpos($currentRouteName, $menu['slug']) === 0) {
                $activeClass = 'active open';
              }
            }
          }
        @endphp

        <li class="menu-item {{ $activeClass }}">
          <a href="{{ $menu['slug'] === 'logout' ? 'javascript:void(0);' : (isset($menu['url']) ? url($menu['url']) : 'javascript:void(0);') }}"
            class="{{ isset($menu['submenu']) ? 'menu-link menu-toggle' : 'menu-link' }}"
            {{ $menu['slug'] === 'logout' ? 'id=logout-link-menu-vert' : '' }}
            @if (isset($menu['target']) && !empty($menu['target'])) target="_blank" @endif
          >
            @isset($menu['icon'])
              <i class="{{ $menu['icon'] }}"></i>
            @endisset
            <div>{{ isset($menu['name']) ? __($menu['name']) : '' }}</div>
            @isset($menu['badge'])
              <div class="badge bg-{{ $menu['badge'][0] }} rounded-pill ms-auto">{{ $menu['badge'][1] }}</div>
            @endisset
          </a>

          {{-- submenu --}}
          @isset($menu['submenu'])
            @include('layouts.sections.menu.submenu', ['menu' => $menu['submenu'], 'userRole' => $userRole])
          @endisset
        </li>
      @endif
    @endforeach
  </ul>
</aside>
