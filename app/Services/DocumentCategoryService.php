<?php

namespace App\Services;

use App\Models\Tenancy\DocumentCategory;
use Illuminate\Support\Arr;

class DocumentCategoryService
{
    public function getAllCategories(bool $showInactive = false)
    {
    return DocumentCategory::query()
        ->when(!$showInactive, function ($query) {
            $query->where('is_active', true);
        })
        ->orderBy('is_active', 'desc')
        ->orderBy('name')
        ->get();
    }

    public function createCategory(array $data): DocumentCategory
    {
        $categoryData = $this->prepareCategoryData($data);
        return DocumentCategory::create($categoryData);
    }

    public function getCategoryForEdit(int $id): array
    {
        $category = DocumentCategory::findOrFail($id);

        return [
            'category' => $category
        ];
    }

    public function updateCategory(int $id, array $data): DocumentCategory
    {
        $category = DocumentCategory::findOrFail($id);

        $categoryData = $this->prepareCategoryData($data);
        $category->update($categoryData);

        return $category;
    }

    public function changeCategoryStatus(int $id): void
    {
        $category = DocumentCategory::findOrFail($id);
        $category->update([
            'is_active' => !$category->is_active,
        ]);
    }

    public function destroyCategory(int $id)
    {
        $category = DocumentCategory::findOrFail($id);
        $category->delete();
    }

    private function prepareCategoryData(array $data): array
    {
        $fields = [
            'name',
            'abbreviation',
            'is_active'
        ];

        return Arr::only($data, $fields);
    }
}
