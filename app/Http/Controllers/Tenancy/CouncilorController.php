<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Councilors\CouncilorStoreRequest;
use App\Http\Requests\Councilors\CouncilorUpdateRequest;
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

        return Inertia::render('Tenancy/Coucilors/Index', [
            'users' => $users,
            'filters' => [
                'show_inactive' => $showInactive,
            ],
        ]);
    }

    public function create()
    {
        $formData = $this->service->getCreationFormData();

        return Inertia::render('Tenancy/Coucilors/CreateConcilor', $formData);
    }

    public function store(CouncilorStoreRequest $request)
    {
        $this->service->createCouncilor($request->validated());

        return redirect()->route('councilors.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function edit(int $id)
    {
        $data = $this->service->getUserForEdit($id);

        return Inertia::render('Tenancy/Coucilors/EditConcilor', $data);
    }

    public function update(CouncilorUpdateRequest $request, int $id)
    {
        $this->service->updateUser($id, $request->validated());

        return redirect()->route('councilors.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function changeStatus(int $id)
    {
        $this->service->changeStatus($id);

        return redirect()->route('councilors.index')->with('success', 'Usuário atualizado com sucesso!');
    }
}
