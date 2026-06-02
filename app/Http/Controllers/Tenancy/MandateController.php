<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mandates\MandateStoreRequest;
use App\Http\Requests\Mandates\MandateUpdateUsersRequest;
use App\Services\MandateService;
use Inertia\Inertia;

class MandateController extends Controller
{
    public function __construct(
        protected MandateService $service
    ) {}

    public function index()
    {
        return Inertia::render('Tenancy/Mandates/Index', [
            'mandates' => $this->service->list(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenancy/Mandates/CreateMandate');
    }

    public function store(MandateStoreRequest $request)
    {
        $mandate = $this->service->create($request->validated());

        return redirect()->route('mandates.edit', $mandate->id)
            ->with('success', 'Mandato criado com sucesso!');
    }

    public function edit(int $id)
    {
        return Inertia::render(
            'Tenancy/Mandates/EditMandate',
            $this->service->getForEdit($id)
        );
    }

    public function update(MandateStoreRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->back()->with('success', 'Mandato atualizado com sucesso!');
    }

    public function updateUsers(MandateUpdateUsersRequest $request, int $id)
    {
        $this->service->updateUsers($id, $request->validated());

        return redirect()->back()->with('success', 'Vereadores atualizados com sucesso!');
    }
}
