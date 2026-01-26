<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentModels\UpdateDocumentModelRequest;
use App\Models\Tenancy\DocumentCategory;
use App\Models\User;
use App\Services\DocumentModelService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentModelController extends Controller
{
    protected $service;

    public function __construct(DocumentModelService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Inertia::render('Tenancy/DocumentModels/Index', [
            'documentModels' => $this->service->getAllModels(),
        ]);
    }


    public function edit(int $id)
    {
        $data = $this->service->getModelForEdit($id);

        return Inertia::render('Tenancy/DocumentModels/Edit', [
            'documentModel' => $data['documentModel'],
            
            'categories' => DocumentCategory::where('is_active', true)
                                ->orderBy('name')
                                ->get(['id', 'name']),
                                
            'users' => User::orderBy('name')
                        ->get(['id', 'name']),
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