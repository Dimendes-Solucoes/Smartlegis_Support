<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tribunes\TribuneReorderUsersRequest;
use App\Services\TribuneService;

class TribuneController extends Controller
{
    public function __construct(
        protected TribuneService $service
    ) {}

    public function removeUser(int $id)
    {
        $this->service->removeUserFromTribune($id);

        return redirect()->route('sessions.tribunes', $id)->with('success', 'Inscrição removida com sucesso!');
    }

    public function reorderUsers(TribuneReorderUsersRequest $request, int $id)
    {
        $this->service->updateUserOrder($request->validated('user_ids'));

        return redirect()->route('sessions.tribunes', $id)->with('success', 'Ordem dos inscritos salva com sucesso!');
    }
}
