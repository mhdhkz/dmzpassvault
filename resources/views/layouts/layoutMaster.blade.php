@isset($pageConfigs)
  {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@php
  $configData = Helper::appClasses();
@endphp

@isset($configData['layout'])
  @include(
    $configData['layout'] === 'horizontal'
    ? 'layouts.horizontalLayout'
    : ($configData['layout'] === 'blank'
    ? 'layouts.blankLayout'
    : ($configData['layout'] === 'front'
    ? 'layouts.layoutFront'
    : 'layouts.contentNavbarLayout'
    )
    )
    )
@endisset

<form id="logout-form-menu-vert" method="POST" action="{{ route('logout') }}" style="display: none;">
  @csrf
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const logoutLink = document.getElementById('logout-link-menu-vert');
    if (logoutLink) {
      logoutLink.addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('logout-form-menu-vert').submit();
      });
    }
  });
</script>
