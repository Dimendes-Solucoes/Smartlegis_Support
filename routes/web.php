<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClicksignController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SelectedTenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/selecionar-tenant', [SelectedTenantController::class, 'change'])->name('tenant.change');
    Route::get('/configuracoes', [SelectedTenantController::class, 'settings'])->name('tenant.settings');

    Route::get('/calendario', [CalendarController::class, 'index'])->name('calendar.index');
});

Route::middleware(['auth', 'is_root'])->group(function () {
    Route::get('/administradores', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/administradores/cadastrar', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/administradores', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/administradores/{id}/editar', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/administradores/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/administradores/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');

    Route::get('/clicksign', [ClicksignController::class, 'index'])->name('clicksign.index');
    Route::delete('/clicksign', [ClicksignController::class, 'destroy'])->name('clicksign.destroy');
});

Route::prefix('credentials')->name('credentials.')
        ->controller(CredentialController::class)
        ->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/tenancy.php';
