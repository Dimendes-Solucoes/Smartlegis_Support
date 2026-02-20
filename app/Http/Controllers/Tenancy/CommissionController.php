<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Commissions\CommissionStoreRequest;
use App\Http\Requests\Commissions\CommissionUpdateUsersRequest;
use App\Services\CommissionService;
use Inertia\Inertia;

class CommissionController extends Controller
{
    public function __construct(
        protected CommissionService $service
    ) {}

    public function index()
    {
        $commissions = $this->service->list();

        return Inertia::render('Tenancy/Commissions/Index', [
            'commissions' => $commissions
        ]);
    }

    public function create()
    {
        $data = $this->service->getCreationFormData();

        return Inertia::render('Tenancy/Commissions/CreateCommission', $data);
    }

    public function store(CommissionStoreRequest $request)
    {
        $commission = $this->service->create($request->validated());

        return redirect()->route('commissions.edit', $commission->id)
            ->with('success', 'Comissão criada com sucesso!');
    }

    public function edit(int $id)
    {
        $data = $this->service->getCommissionForEdit($id);

        return Inertia::render('Tenancy/Commissions/EditCommission', $data);
    }

    public function update(CommissionStoreRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->back()->with('success', 'Comissão atualizada com sucesso!');
    }

    public function updateUsers(CommissionUpdateUsersRequest $request, int $id)
    {
        $this->service->updateUsers($id, $request->validated());

        return redirect()->back()->with('success', 'Membros da comissão atualizados com sucesso!');
    }
}
