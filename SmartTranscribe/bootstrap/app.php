<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.token' => \App\Http\Middleware\AuthToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 401 - Não autenticado
        $exceptions->renderable(function (AuthenticationException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        });

        // 403 - Sem permissão
        $exceptions->renderable(function (AuthorizationException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['message' => 'Você não tem permissão para acessar este recurso.'], 403);
        });

        // 404 - Não encontrado
        $exceptions->renderable(function (NotFoundHttpException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['message' => 'Rota não encontrada.'], 404);
        });

        $exceptions->renderable(function (NotFoundResourceException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['message' => $exception->getMessage() ?? 'Recurso não encontrado.'], 404);
        });

        // 422 - Erro de validação
        $exceptions->renderable(function (ValidationException $exception) {
            Log::error($exception->getMessage());
            return response()->json(['message' => 'Dados inválidos.', 'errors' => $exception->errors()], 422);
        });

        // 500 - Erro interno do servidor
        $exceptions->renderable(function (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        });
    })
    ->create();
