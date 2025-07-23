<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SaveMayorRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Tenancy\User;
use App\Services\UserService;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    public function index()
    {
        $users = $this->service->getRegularUsers();

        return Inertia::render('Tenancy/Users/Index', ['users' => $users]);
    }

    public function create()
    {
        $formData = $this->service->getCreationFormData();

        return Inertia::render('Tenancy/Users/CreateUser', $formData);
    }

    public function store(StoreUserRequest $request)
    {
        $this->service->createUser($request->validated());

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function replaceMayor()
    {
        $formData = $this->service->getReplaceMayorFormData();

        return Inertia::render('Tenancy/Users/ReplaceMayor', $formData);
    }

    public function saveMayor(SaveMayorRequest $request)
    {
        $mayor = User::findOrFail($request->mayor_id);

        $this->service->updateMayor($mayor);

        return redirect()->route('users.index')->with('success', 'Prefeito atualizado com sucesso!');
    }

    public function edit(int $id)
    {
        $data = $this->service->getUserForEdit($id);

        return Inertia::render('Tenancy/Users/EditUser', $data);
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = User::findOrFail($id);

        $this->service->updateUser($user, $request->validated());

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return redirect()->route('users.index')->with('success', 'Usuário deletado com sucesso!');
    }
}
