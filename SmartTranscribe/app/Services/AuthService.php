<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected WebhookService $webhookService,
    ) {}

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new AuthenticationException();
        }

        $user->tokens()->delete();

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function profile()
    {
        return auth()->user();
    }

    public function listUsers()
    {
        return User::all();
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
