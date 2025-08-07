<?php

use App\Http\Controllers\Tenancy\CommissionController;
use App\Http\Controllers\Tenancy\CouncilorController;
use App\Http\Controllers\Tenancy\UserController;
use App\Http\Controllers\Tenancy\DocumentCategoryController;
use App\Http\Controllers\Tenancy\TimerController;
use App\Http\Controllers\Tenancy\SessionController;
use App\Http\Controllers\Tenancy\TribuneController;
use App\Http\Controllers\Tenancy\BigDiscussionController;
use App\Http\Controllers\Tenancy\DiscussionController;
use App\Http\Controllers\Tenancy\QuestionOrderController;
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
                Route::delete('/{id}', 'destroy')->name('users.destroy');
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

        Route::prefix('comissoes')
            ->controller(CommissionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('commissions.index');
                Route::get('/cadastrar', 'create')->name('commissions.create');
                Route::post('/', 'store')->name('commissions.store');
                Route::get('/{id}/editar', 'edit')->name('commissions.edit');
                Route::put('/{id}', 'update')->name('commissions.update');
                Route::put('/{id}/users', 'updateUsers')->name('commissions.update_users');
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

        Route::prefix('sessoes')
            ->controller(SessionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('sessions.index');
                Route::get('/{id}/editar', 'edit')->name('sessions.edit');
                Route::put('/{id}', 'update')->name('sessions.update');
                Route::get('/{id}/reordenar', 'editOrder')->name('sessions.edit_order');
                Route::put('/{id}/reordenar', 'updateOrder')->name('sessions.update_order');
                Route::delete('/{id}', 'destroy')->name('sessions.destroy');
            });

        Route::prefix('tribunas')
        ->controller(TribuneController::class)
        ->group(function () {
            Route::get('/', 'index')->name('tribunes.index');
            Route::get('/{id}/editar', 'edit')->name('tribunes.edit');
            Route::delete('/{id}/inscricoes', 'removeUser')->name('tribunes.users.destroy');
            Route::delete('/{id}', 'destroy')->name('tribunes.destroy');
        });

        Route::prefix('explanacoes-pessoais')
        ->controller(BigDiscussionController::class)
        ->group(function () {
            Route::get('/', 'index')->name('big-discussions.index');
            Route::get('/{id}/editar', 'edit')->name('big-discussions.edit');
            Route::delete('/{id}/inscricoes/', 'removeUser')->name('big-discussions.users.destroy');
            Route::delete('/{id}', 'destroy')->name('big-discussions.destroy');
        });

        Route::prefix('discussoes')
        ->controller(DiscussionController::class)
        ->group(function () {
            Route::get('/', 'index')->name('discussions.index');
            Route::get('/{id}/editar', 'edit')->name('discussions.edit');
            Route::delete('/{id}/inscricoes/', 'removeUser')->name('discussions.users.destroy');
            Route::delete('/{id}', 'destroy')->name('discussions.destroy');
        });

        Route::prefix('questoes-de-ordem')
        ->controller(QuestionOrderController::class)
        ->group(function () {
            Route::get('/', 'index')->name('question-orders.index');
            Route::get('/{id}/editar', 'edit')->name('question-orders.edit');
            Route::delete('/{id}/inscricoes', 'removeUser')->name('question-orders.users.destroy');
            Route::delete('/{id}','destroy')->name('question-orders.destroy');
        });
    });
