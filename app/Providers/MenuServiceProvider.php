<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    //
  }

  public function boot(): void
  {
    View::composer('*', function ($view) {
      $user = auth()->user();
      $userId = $user?->id ?? '';
      $userRole = auth()->user()?->role ?? 'guest';

      $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
      $verticalMenuJson = str_replace('__CURRENT_USER_ID__', $userId, $verticalMenuJson);
      $verticalMenuData = json_decode($verticalMenuJson, true); // decode as array

      $filteredMenu = $this->filterMenuByRole($verticalMenuData['menu'], $userRole);
      $verticalMenuData['menu'] = array_values($filteredMenu); // reset index

      $view->with('menuData', [$verticalMenuData]);
    });
  }

  private function filterMenuByRole(array $menu, string $role): array
  {
    return array_filter(array_map(function ($item) use ($role) {
      if (isset($item['roles']) && !in_array($role, $item['roles'])) {
        return null;
      }

      if (isset($item['submenu'])) {
        $item['submenu'] = array_values($this->filterMenuByRole($item['submenu'], $role));

        // Jangan tampilkan jika semua submenu disembunyikan
        if (empty($item['submenu'])) {
          return null;
        }
      }

      return $item;
    }, $menu));
  }
}
