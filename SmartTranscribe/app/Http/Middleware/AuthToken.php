<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token de autenticação ausente ou malformado.'], 401);
        }

        $token = substr($authorizationHeader, 7);

        $client = Client::where('token', $token)->first();

        if (!$client) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        $request->attributes->set('client', $client);

        return $next($request);
    }
}
