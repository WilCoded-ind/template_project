<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BaruController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management Routes
    Route::middleware('permission:user.view')->group(function () {
        Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::resource('users', UserController::class);
    });

    // Role Management Routes
    Route::middleware('permission:role.view')->group(function () {
        Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export');
        Route::resource('roles', RoleController::class);
    });

    // Menu Management Routes
    Route::middleware('permission:menu.view')->group(function () {
        Route::resource('menus', MenuController::class);
    });

    // Permission Management Routes
    Route::middleware(['auth', 'permission:permission.view'])->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Folder Management Routes
    Route::middleware('permission:barus.view')->group(function () {
        Route::resource('barus', BaruController::class);
    });
});

require __DIR__ . '/auth.php';
