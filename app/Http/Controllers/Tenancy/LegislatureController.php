<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Legislatures\LegislatureStoreRequest;
use App\Http\Requests\Legislatures\LegislatureUpdateUsersRequest;
use App\Services\LegislatureService;
use Inertia\Inertia;

class LegislatureController extends Controller
{
    public function __construct(
        protected LegislatureService $service
    ) {}

    public function index()
    {
        return Inertia::render('Tenancy/Legislatures/Index', [
            'legislatures' => $this->service->list()
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenancy/Legislatures/CreateLegislature');
    }

    public function store(LegislatureStoreRequest $request)
    {
        $legislature = $this->service->create($request->validated());

        return redirect()->route('legislatures.edit', $legislature->id)
            ->with('success', 'Legislatura criada com sucesso!');
    }

    public function edit(int $id)
    {
        return Inertia::render(
            'Tenancy/Legislatures/EditLegislature',
            $this->service->getForEdit($id)
        );
    }

    public function update(LegislatureStoreRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->back()->with('success', 'Legislatura atualizada com sucesso!');
    }

    public function updateUsers(LegislatureUpdateUsersRequest $request, int $id)
    {
        $this->service->updateUsers($id, $request->validated());

        return redirect()->back()->with('success', 'Vereadores atualizados com sucesso!');
    }
}
