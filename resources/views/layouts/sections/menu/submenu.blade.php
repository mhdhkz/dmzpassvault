@php
  use Illuminate\Support\Facades\Route;
@endphp

<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)

    @php
    // Filter role
    if (isset($submenu['roles']) && !in_array(auth()->user()->role, $submenu['roles'])) {
    continue;
    }

    $activeClass = null;
    $active = $configData["layout"] === 'vertical' ? 'active open' : 'active';
    $currentRouteName = Route::currentRouteName();

    if ($currentRouteName === $submenu['slug']) {
    $activeClass = 'active';
    } elseif (isset($submenu['submenu'])) {
    if (is_array($submenu['slug'])) {
    foreach ($submenu['slug'] as $slug) {
      if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
      $activeClass = $active;
      }
    }
    } else {
    if (str_contains($currentRouteName, $submenu['slug']) && strpos($currentRouteName, $submenu['slug']) === 0) {
      $activeClass = $active;
    }
    }
    }
    @endphp

    <li class="menu-item {{ $activeClass }}">
    <a href="{{ isset($submenu['url']) ? url($submenu['url']) : 'javascript:void(0)' }}"
      class="{{ isset($submenu['submenu']) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu['target']) && !empty($submenu['target'])) target="_blank" @endif>

      @if (isset($submenu['icon']))
      <i class="{{ $submenu['icon'] }}"></i>
    @endif
      <div>{{ isset($submenu['name']) ? __($submenu['name']) : '' }}</div>

      @isset($submenu['badge'])
      <div class="badge bg-{{ $submenu['badge'][0] }} rounded-pill ms-auto">{{ $submenu['badge'][1] }}</div>
    @endisset
    </a>

    {{-- Recursive submenu --}}
    @if (isset($submenu['submenu']))
    @include('layouts.sections.menu.submenu', ['menu' => $submenu['submenu']])
    @endif
    </li>
    @endforeach
  @endif
</ul>