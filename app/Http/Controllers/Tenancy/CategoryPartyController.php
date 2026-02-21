<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryParty\CategoryPartyStoreRequest;
use App\Http\Requests\CategoryParty\CategoryPartyUpdateRequest;
use App\Services\CategoryPartyService;
use Inertia\Inertia;

class CategoryPartyController extends Controller
{
    public function __construct(
        protected CategoryPartyService $service
    ) {}

    public function index()
    {
        $parties = $this->service->list();

        return Inertia::render('Tenancy/CategoryParties/Index', [
            'parties' => $parties,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenancy/CategoryParties/CreateCategoryParty');
    }

    public function store(CategoryPartyStoreRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('category-parties.index')->with('success', 'Partido cadastrado com sucesso!');
    }

    public function edit(int $id)
    {
        $party = $this->service->findOrFail($id);

        return Inertia::render('Tenancy/CategoryParties/EditCategoryParty', [
            'party' => $party,
        ]);
    }

    public function update(CategoryPartyUpdateRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->route('category-parties.index')->with('success', 'Partido atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return redirect()->route('category-parties.index')->with('success', 'Partido removido com sucesso!');
    }
}
