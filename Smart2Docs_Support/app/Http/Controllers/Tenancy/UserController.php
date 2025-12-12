<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    public function index()
    {
        $users = $this->service->list();

        return Inertia::render('Tenancy/Users/Index', ['users' => $users]);
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return redirect()->route('users.index')
            ->with('success', 'Usuário deletado com sucesso!');
    }
}
