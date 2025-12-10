<?php

namespace App\Providers;

use App\Models\Menu;
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
        View::composer('layouts.navigation', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $userPermissions = $user->getPermissions()->pluck('id')->toArray();

                $menus = Menu::with(['children' => function ($query) use ($userPermissions) {
                    $query->active()
                        ->whereIn('permission_id', $userPermissions)
                        ->orWhereNull('permission_id')
                        ->orderBy('order');
                }])
                    ->active()
                    ->parent()
                    ->where(function ($query) use ($userPermissions) {
                        $query->whereIn('permission_id', $userPermissions)
                            ->orWhereNull('permission_id');
                    })
                    ->orderBy('order')
                    ->get();

                $view->with('menus', $menus);
            } else {
                $view->with('menus', collect());
            }
        });

        // Composer untuk AdminLTE sidebar
        View::composer('layouts.adminlte.sidebar', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $userPermissions = $user->getPermissions()->pluck('id')->toArray();

                $menus = Menu::with(['children' => function ($query) use ($userPermissions) {
                    $query->active()
                        ->whereIn('permission_id', $userPermissions)
                        ->orWhereNull('permission_id')
                        ->orderBy('order');
                }])
                    ->active()
                    ->parent()
                    ->where(function ($query) use ($userPermissions) {
                        $query->whereIn('permission_id', $userPermissions)
                            ->orWhereNull('permission_id');
                    })
                    ->orderBy('order')
                    ->get()
                    ->map(function ($menu) {
                        $menu->active = request()->routeIs($menu->route ?? '') ||
                                       (isset($menu->url) && url(request()->path()) === url($menu->url));

                        if ($menu->children) {
                            foreach ($menu->children as $child) {
                                $child->active = request()->routeIs($child->route ?? '') ||
                                                (isset($child->url) && url(request()->path()) === url($child->url));
                            }
                        }

                        return $menu;
                    });

                $view->with('menus', $menus);
            } else {
                $view->with('menus', collect());
            }
        });
    }
}
