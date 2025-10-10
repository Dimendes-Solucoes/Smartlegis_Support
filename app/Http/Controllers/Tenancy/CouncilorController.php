<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Councilors\CouncilorStoreRequest;
use App\Http\Requests\Councilors\CouncilorUpdateRequest;
use App\Http\Requests\Councilors\StoreUserTermRequest;
use App\Http\Requests\Councilors\UpdateUserTermRequest;
use App\Services\CouncilorService;
use Inertia\Inertia;

class CouncilorController extends Controller
{
    public function __construct(
        protected CouncilorService $service
    ) {}

    public function index()
    {
        $showInactive = request()->boolean('show_inactive');
        $users = $this->service->list($showInactive);

        return Inertia::render('Tenancy/Councilors/Index', [
            'users' => $users,
            'filters' => [
                'show_inactive' => $showInactive,
            ],
        ]);
    }

    public function create()
    {
        $formData = $this->service->getCreationFormData();

        return Inertia::render('Tenancy/Councilors/CreateCouncilor', $formData);
    }

    public function store(CouncilorStoreRequest $request)
    {
        $this->service->createCouncilor($request->validated());

        return redirect()->route('councilors.index')->with('success', 'Vereador cadastrado com sucesso!');
    }

    public function edit(int $id)
    {
        $data = $this->service->getUserForEdit($id);

        return Inertia::render('Tenancy/Councilors/EditCouncilor', $data);
    }

    public function update(CouncilorUpdateRequest $request, int $id)
    {
        $this->service->updateUser($id, $request->validated());

        return redirect()->route('councilors.index')->with('success', 'Vereador atualizado com sucesso!');
    }

    public function changeStatus(int $id)
    {
        $this->service->changeStatus($id);

        return redirect()->route('councilors.index')->with('success', 'Vereador atualizado com sucesso!');
    }

    public function termsIndex(int $id)
    {
        return Inertia::render('Tenancy/Councilors/Terms/Index', [
            'data' => $this->service->getCouncilorWithTerms($id),
        ]);
    }

    public function termsStore(StoreUserTermRequest $request, int $id)
    {
        $this->service->storeTerm($id, $request->validated());
        return back()->with('success', 'Mandato adicionado com sucesso!');
    }

    public function termsUpdate(UpdateUserTermRequest $request, int $termId)
    {
        $this->service->updateTerm($termId, $request->validated());
        return back()->with('success', 'Mandato atualizado com sucesso!');
    }

    public function termsDestroy(int $termId)
    {
        $this->service->destroyTerm($termId);
        return back()->with('success', 'Mandato removido com sucesso!');
    }
}
