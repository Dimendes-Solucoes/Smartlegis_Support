<?php

namespace App\Services;

use App\Models\Tenancy\DocumentCategory;
use App\Models\Tenancy\DocumentCategoryProtocolMinimum;

class ProtocolMinimumService
{
    public function getForIndex(): array
    {
        $categories = DocumentCategory::orderBy('name')
            ->with(['protocolMinimums' => fn($q) => $q->orderBy('year', 'desc')])
            ->get();

        return [
            'categories' => $categories,
            'current_year' => now()->year,
        ];
    }

    public function store(array $data): DocumentCategoryProtocolMinimum
    {
        return DocumentCategoryProtocolMinimum::create($data);
    }

    public function update(int $id, array $data): DocumentCategoryProtocolMinimum
    {
        $minimum = DocumentCategoryProtocolMinimum::findOrFail($id);
        $minimum->update($data);
        return $minimum;
    }

    public function destroy(int $id): void
    {
        DocumentCategoryProtocolMinimum::findOrFail($id)->delete();
    }
}
