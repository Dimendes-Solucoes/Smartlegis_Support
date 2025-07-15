<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $service
    ) {}

    /** @group AdminAuth */
    public function login(AuthLoginRequest $request)
    {
        return response()->json([
            'token' => $this->service->login($request->validated())
        ]);
    }

    /** @group AdminAuth */
    public function profile()
    {
        return response()->json([
            'auth' => $this->service->profile()
        ]);
    }
}
