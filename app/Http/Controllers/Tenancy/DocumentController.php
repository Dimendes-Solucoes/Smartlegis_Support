<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Documents\UpdateDocumentRequest;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $service
    ) {}

    public function index(Request $request)
    {
        $data = $this->service->getDocumentsForIndex($request);

        return Inertia::render('Tenancy/Documents/Index', $data);
    }

    public function edit(int $id)
    {
        return Inertia::render('Tenancy/Documents/EditDocument', [
            'data' => $this->service->getDocumentForEdit($id),
        ]);
    }

    public function update(UpdateDocumentRequest $request, int $id)
    {
        $this->service->updateDocument($id, $request->validated());
        return redirect()->route('documents.index')->with('success', 'Documento atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->destroyDocument($id);
        return back()->with('success', 'Documento movido para a lixeira!');
    }
}
