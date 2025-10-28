<?php

use App\Http\Controllers\Public\AdminController;
use App\Http\Controllers\Public\CalendarController;
use App\Http\Controllers\Public\ClicksignController;
use App\Http\Controllers\Public\CredentialController;
use App\Http\Controllers\Public\DashboardController;
use App\Http\Controllers\Public\ProfileController;
use App\Http\Controllers\Public\SelectedTenantController;
use App\Http\Controllers\Public\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::prefix('perfil')
        ->name('profile.')
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::delete('/', 'destroy')->name('destroy');
        });

    Route::post('/selecionar-cidade', [SelectedTenantController::class, 'change'])->name('tenant.change');
    Route::get('/configuracoes', [SelectedTenantController::class, 'settings'])->name('tenant.settings');
    Route::get('/calendario', [CalendarController::class, 'index'])->name('calendar.index');

    Route::prefix('credenciais')
        ->name('credentials.')
        ->controller(CredentialController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/cadastrar', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/editar', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy')->middleware('is_root');
        });

    Route::prefix('tickets')
        ->name('tickets.')
        ->controller(TicketController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/cadastrar', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{code}', 'view')->name('view');
            Route::put('/{code}', 'update')->name('update');
            Route::put('/{code}/messages', 'storeMessage')->name('messages.store');
        });

    Route::prefix('administradores')
        ->name('admin.')
        ->controller(AdminController::class)
        ->middleware('is_root')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/cadastrar', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/editar', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    Route::prefix('clicksign')
        ->name('clicksign.')
        ->controller(ClicksignController::class)
        ->middleware('is_root')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::delete('/', 'destroy')->name('destroy');
        });
});
