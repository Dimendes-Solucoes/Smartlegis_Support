<?php

namespace App\Services;

use App\Models\Tenancy\DocumentModel;
use App\Models\Tenancy\DocumentCategory;
use Illuminate\Support\Arr;

class DocumentModelService
{
    public function getAllModels()
    {
        return DocumentModel::with(['category'])
            ->orderBy('title')
            ->get();
    }

    public function getModelForEdit(int $id): array
    {
        $documentModel = DocumentModel::findOrFail($id);
        $categories = DocumentCategory::where('is_active', true)->orderBy('name')->get();

        return [
            'documentModel' => $documentModel,
            'categories' => $categories
        ];
    }

    public function updateModel(int $id, array $data): DocumentModel
    {
        $documentModel = DocumentModel::findOrFail($id);

        $modelData = $this->prepareModelData($data);
        $documentModel->update($modelData);

        return $documentModel;
    }

    public function destroyModel(int $id): void
    {
        $documentModel = DocumentModel::findOrFail($id);
        $documentModel->delete();
    }

    private function prepareModelData(array $data): array
    {
        $fields = [
            'document_category_id',
            'title',
            'body',
        ];

        return Arr::only($data, $fields);
    }
}
