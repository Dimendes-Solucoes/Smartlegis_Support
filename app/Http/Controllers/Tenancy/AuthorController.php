<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authors\AuthorStoreRequest;
use App\Http\Requests\Authors\AuthorUpdateRequest;
use App\Services\AuthorService;

class AuthorController extends Controller
{
    public function __construct(
        protected AuthorService $service
    ) {}

    public function store(AuthorStoreRequest $request)
    {
        $this->service->store($request->validated());
        return back()->with('success', 'Autor adicionado com sucesso.');
    }

    public function update(AuthorUpdateRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());
        return back()->with('success', 'Autor do documento atualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'Autor do documento deletado com sucesso');
    }
}
