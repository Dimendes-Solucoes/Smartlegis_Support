<?php

use App\Http\Controllers\Tenancy\BigDiscussionController;
use App\Http\Controllers\Tenancy\CommissionController;
use App\Http\Controllers\Tenancy\CouncilorController;
use App\Http\Controllers\Tenancy\DiscussionController;
use App\Http\Controllers\Tenancy\DocumentCategoryController;
use App\Http\Controllers\Tenancy\DocumentController;
use App\Http\Controllers\Tenancy\QuestionOrderController;
use App\Http\Controllers\Tenancy\QuorumController;
use App\Http\Controllers\Tenancy\SessionController;
use App\Http\Controllers\Tenancy\TimerController;
use App\Http\Controllers\Tenancy\TribuneController;
use App\Http\Controllers\Tenancy\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.connection'])
    ->group(function () {
        Route::prefix('usuarios')
            ->name('users.')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/trocar-prefeito', 'replaceMayor')->name('replace_mayor');
                Route::post('/salvar-prefeito', 'saveMayor')->name('save_mayor');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::prefix('vereadores')
            ->name('councilors.')
            ->controller(CouncilorController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::patch('/{id}/trocar-status', 'changeStatus')->name('change_status');
                Route::get('/{id}/mandatos', 'termsIndex')->name('terms.index');
                Route::post('/{id}/mandatos', 'termsStore')->name('terms.store');
                Route::put('/mandatos/{termId}', 'termsUpdate')->name('terms.update');
                Route::delete('/mandatos/{termId}', 'termsDestroy')->name('terms.destroy');
                    });

        Route::prefix('comissoes')
            ->name('commissions.')
            ->controller(CommissionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::put('/{id}/users', 'updateUsers')->name('update_users');
            });

        Route::prefix('categorias-de-documentos')
            ->name('document-categories.')
            ->controller(DocumentCategoryController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::put('resetar_ordem', 'resetOrder')->name('reset_order');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::patch('/{id}/status', 'changeStatus')->name('change_status');
            });

        Route::prefix('documentos')
            ->name('documents.')
            ->controller(DocumentController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });

        Route::prefix('tempos')
            ->name('timers.')
            ->controller(TimerController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
            });

        Route::prefix('sessoes')
            ->name('sessions.')
            ->controller(SessionController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/cadastrar', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{id}/editar', 'edit')->name('edit');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
                Route::post('/{id}/reset', 'reset')->name('reset');
                Route::post('/{id}/duplicate', 'duplicate')->name('duplicate');

                Route::get('/{id}/documentos', 'documents')->name('documents');
                Route::put('/{id}/documentos', 'updateDocuments')->name('update_documents');
                Route::put('/{id}/resetar-documentos', 'resetDocuments')->name('reset_documents');
                Route::delete('/{id}/documentos/{document_id}/expendient', 'removeDocumentFromExpedient')->name('documents.destroy_expendient');
                Route::delete('/{id}/documentos/{document_id}/order', 'removeDocumentFromOrder')->name('documents.destroy_order');
                Route::get('/{id}/documentos/{document_id}/votos', 'documentVotes')->name('documents.votes');
                Route::put('/{id}/documentos/{document_id}/votos', 'updateDocumentVotes')->name('documents.update_votes');

                Route::get('/{id}/quorums', 'quorums')->name('quorums');
                Route::delete('/{id}/quorums/limpar', 'clearQuorums')->name('quorums.clear');

                Route::get('/{id}/tribunas', 'tribunes')->name('tribunes');
                Route::delete('/{id}/tribunas/limpar', 'clearTribunes')->name('tribunes.clear');

                Route::get('/{id}/discussoes', 'listDiscussions')->name('list_discussions');
                Route::delete('/{id}/discussoes/limpar', 'clearDiscussions')->name('discussions.clear');
                Route::get('/{id}/discussoes/{discussion_id}', 'discussions')->name('discussions');

                Route::get('/{id}/explanacoes-pessoais', 'bigDiscussions')->name('big_discussions');
                Route::delete('/{id}/explanacoes-pessoais/limpar', 'clearBigDiscussions')->name('big_discussions.clear');

                Route::get('/{id}/questoes-de-ordem', 'questionOrders')->name('questions_orders');
                Route::delete('/{id}/questoes-de-ordem/limpar', 'clearQuestionOrders')->name('questions_orders.clear');
            });

        Route::prefix('tribunas')
            ->name('tribunes.')
            ->controller(TribuneController::class)
            ->group(function () {
                Route::put('/{id}/reordenar-usuarios', 'reorderUsers')->name('users.reorder');
                Route::delete('/{id}/inscricoes', 'removeUser')->name('users.destroy');
            });

        Route::delete('quorums/{id}/inscricoes', [QuorumController::class, 'removeUser'])->name('quorums.users.destroy');
        Route::delete('explanacoes-pessoais/{id}/inscricoes', [BigDiscussionController::class, 'removeUser'])->name('big-discussions.users.destroy');
        Route::delete('discussoes/{id}/inscricoes', [DiscussionController::class, 'removeUser'])->name('discussions.users.destroy');
        Route::delete('questoes-de-ordem/{id}/inscricoes', [QuestionOrderController::class, 'removeUser'])->name('question-orders.users.destroy');
    });
