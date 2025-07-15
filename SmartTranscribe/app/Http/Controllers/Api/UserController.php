<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthCreateRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    /** @group AdminUsers */
    public function list()
    {
        return response()->json([
            'users' => $this->service->list()
        ]);
    }

    /** @group AdminUsers */
    public function create(AuthCreateRequest $request)
    {
        $user = $this->service->create($request->validated());

        return response()->json([
            'user' => $user
        ], 201);
    }
}
