<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentCategories\StoreDocumentCategoryRequest;
use App\Http\Requests\DocumentCategories\UpdateDocumentCategoryRequest;
use App\Services\DocumentCategoryService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function __construct(
        protected DocumentCategoryService $service
    ) {}

    public function index(Request $request)
    {
        $showInactive = $request->boolean('show_inactive');
        $categories = $this->service->getAllCategories($showInactive);

        return Inertia::render('Tenancy/DocumentCategories/Index', [
            'categories' => $categories,
            'filters' => [
                'show_inactive' => $showInactive,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenancy/DocumentCategories/CreateDocumentCategoy');
    }

    public function store(StoreDocumentCategoryRequest $request)
    {
        $this->service->createCategory($request->validated());

        return redirect()->route('document-categories.index')
            ->with('success', 'Categoria de documento criada com sucesso!');
    }

    public function edit(int $id)
    {
        $data = $this->service->getCategoryForEdit($id);

        return Inertia::render('Tenancy/DocumentCategories/EditDocumentCategory', $data);
    }

    public function update(UpdateDocumentCategoryRequest $request, int $id)
    {
        $this->service->updateCategory($id, $request->validated());

        return redirect()->route('document-categories.index')
            ->with('success', 'Categoria de documento atualizada com sucesso!');
    }

    public function changeStatus(int $id)
    {
        $this->service->changeCategoryStatus($id);

        return back()->with('success', 'Status da categoria alterado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->destroyCategory($id);

        return redirect()->route('document-categories.index')
            ->with('success', 'Categoria de documento movida para a lixeira!');
    }
}
