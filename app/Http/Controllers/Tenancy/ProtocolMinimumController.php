<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProtocolMinimums\StoreProtocolMinimumRequest;
use App\Http\Requests\ProtocolMinimums\UpdateProtocolMinimumRequest;
use App\Services\ProtocolMinimumService;
use Inertia\Inertia;

class ProtocolMinimumController extends Controller
{
    public function __construct(
        protected ProtocolMinimumService $service
    ) {}

    public function index()
    {
        return Inertia::render('Tenancy/ProtocolMinimums/Index', [
            'data' => $this->service->getForIndex(),
        ]);
    }

    public function store(StoreProtocolMinimumRequest $request)
    {
        $this->service->store($request->validated());
        return back()->with('success', 'Protocolo mínimo cadastrado com sucesso!');
    }

    public function update(UpdateProtocolMinimumRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());
        return back()->with('success', 'Protocolo mínimo atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'Protocolo mínimo excluído com sucesso!');
    }
}
