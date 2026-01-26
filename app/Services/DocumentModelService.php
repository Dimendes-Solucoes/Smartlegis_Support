<?php

namespace App\Services;

use App\Models\Tenancy\DocumentModel;
use App\Models\Tenancy\DocumentCategory; 
use App\Models\User; 
use Illuminate\Support\Arr;

class DocumentModelService
{

    public function getAllModels()
    {
        return DocumentModel::with(['user', 'category'])
            ->orderBy('title')
            ->get();
    }

    public function getModelForEdit(int $id): array
    {
        $documentModel = DocumentModel::findOrFail($id);
        $categories = DocumentCategory::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return [
            'documentModel' => $documentModel,
            'categories' => $categories,
            'users' => $users,
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
            'user_id',
            'document_category_id',
            'title',
            'body',
        ];

        return Arr::only($data, $fields);
    }
}