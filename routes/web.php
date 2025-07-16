<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalendarController;
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

    Route::get('/administradores', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/administradores/cadastrar', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/administradores', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/administradores/{id}/editar', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/administradores/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/administradores/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    
    Route::get('/calendario', [CalendarController::class, 'index'])->name('calendar.index');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/tenancy.php';
