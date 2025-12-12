<?php

namespace App\Exceptions;

use BadMethodCallException;
use Error;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            if (!$request->wantsJson()) {
                return redirect()->route('login');
            }
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if (!$request->wantsJson()) {
                return redirect()->route('dashboard')
                    ->with('error', $e->getMessage() ?: 'Erro ao realizar requisição');
            }
        });

        $this->renderable(function (BadMethodCallException $e, Request $request) {
            if (!$request->wantsJson()) {
                return redirect()->route('dashboard')
                    ->with('error', $e->getMessage() ?: 'Erro ao realizar requisição');
            }
        });

        $this->renderable(function (Error $e, Request $request) {
            if (!$request->wantsJson()) {
                return redirect()->route('dashboard')
                    ->with('error', $e->getMessage() ?: 'Erro ao realizar requisição');
            }
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if (!$request->wantsJson()) {
                return back()->with('error', $e->getMessage() ?: 'Erro ao realizar requisição');
            }
        });
    }
}
