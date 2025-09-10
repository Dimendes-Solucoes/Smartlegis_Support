<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\TribuneService;

use Illuminate\Http\Request;

class TribuneController extends Controller
{
    public function __construct(
        protected TribuneService $service
    ) {}

    public function removeUser(int $id)
    {
        $this->service->removeUserFromTribune($id);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }

    public function reorderUsers(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:tribune_users,id'],
        ]);

        $this->service->updateUserOrder($validated['user_ids']);

        return back()->with('success', 'Ordem dos inscritos salva com sucesso!');
    }

}
