<?php

use App\Http\Controllers\Tenancy\CouncilorController;
use App\Http\Controllers\Tenancy\UserController;
use App\Http\Controllers\Tenancy\DocumentCategoryController;
use App\Http\Controllers\Tenancy\TimerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.connection'])
    ->group(function () {
        Route::prefix('usuarios')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/', 'index')->name('users.index');
                Route::get('/cadastrar', 'create')->name('users.create');
                Route::post('/', 'store')->name('users.store');
                Route::get('/trocar-prefeito', 'replaceMayor')->name('users.replace_mayor');
                Route::post('/salvar-prefeito', 'saveMayor')->name('users.save_mayor');
                Route::get('/{id}/editar', 'edit')->name('users.edit');
                Route::put('/{id}', 'update')->name('users.update');
            });

        Route::prefix('vereadores')
            ->controller(CouncilorController::class)
            ->group(function () {
                Route::get('/', 'index')->name('councilors.index');
                Route::get('/cadastrar', 'create')->name('councilors.create');
                Route::post('/', 'store')->name('councilors.store');
                Route::get('/{id}/editar', 'edit')->name('councilors.edit');
                Route::put('/{id}', 'update')->name('councilors.update');
                Route::patch('/{id}/trocar-status', 'changeStatus')->name('councilors.change_status');
            });

        Route::prefix('categorias-de-documentos')
            ->controller(DocumentCategoryController::class)
            ->group(function () {
                Route::get('/', 'index')->name('document-categories.index');
                Route::get('/cadastrar', 'create')->name('document-categories.create');
                Route::post('/', 'store')->name('document-categories.store');
                Route::get('/{id}/editar', 'edit')->name('document-categories.edit');
                Route::put('/{id}', 'update')->name('document-categories.update');
                Route::delete('/{id}', 'destroy')->name('document-categories.destroy');
                Route::patch('/{id}/status', 'changeStatus')->name('document-categories.change_status');
            });

        Route::prefix('tempos')
            ->controller(TimerController::class)
            ->group(function () {
                Route::get('/', 'index')->name('timers.index');
                Route::get('/{id}/editar', 'edit')->name('timers.edit');
                Route::put('/{id}', 'update')->name('timers.update');
            });
    });
