<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentModels\UpdateDocumentModelRequest;
use App\Services\DocumentModelService;
use Inertia\Inertia;

class DocumentModelController extends Controller
{
    public function __construct(
        protected DocumentModelService $service
    ) {}

    public function index()
    {
        return Inertia::render('Tenancy/DocumentModels/Index', [
            'documentModels' => $this->service->getAllModels(),
        ]);
    }


    public function edit(int $id)
    {
        return Inertia::render('Tenancy/DocumentModels/EditDocumentModel', [
            'data' => $this->service->getModelForEdit($id)
        ]);
    }

    public function update(UpdateDocumentModelRequest $request, int $id)
    {
        $this->service->updateModel($id, $request->validated());

        return redirect()->route('document-models.index')
            ->with('success', 'Modelo de documento atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->destroyModel($id);

        return redirect()->route('document-models.index')
            ->with('success', 'Modelo de documento excluído com sucesso!');
    }
}
