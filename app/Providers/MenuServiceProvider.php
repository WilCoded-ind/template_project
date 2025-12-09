<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Menu;

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
    }
}
