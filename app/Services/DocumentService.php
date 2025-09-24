<?php

namespace App\Services;

use App\Models\Tenancy\Document;
use App\Models\Tenancy\DocumentCategory;
use App\Models\Tenancy\DocumentStatusMovement;
use App\Models\Tenancy\DocumentStatusVote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function getAllDocuments(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search');
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');

        $sortableFields = ['name', 'protocol_number', 'id'];
        if (!in_array($sortField, $sortableFields)) {
            $sortField = 'id';
        }

        $query = Document::query()
            ->with(['voteStatus', 'movementStatus'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                        ->orWhere('protocol_number', 'ilike', "%{$search}%");
                });
            })
            ->when($request->input('categories'), function ($query, $categories) {
                $categoryIds = is_array($categories) ? $categories : [$categories];
                if (!empty($categoryIds)) {
                    $query->whereIn('document_category_id', $categoryIds);
                }
            });

        $query->orderBy($sortField, $sortDirection);

        $documents = $query->paginate(15);

        return $documents->through(fn(Document $document) => [
            'id' => $document->id,
            'name' => $document->name,
            'protocol_number' => $document->protocol_number,
            'attachment_url' => $document->attachment ? Storage::url($document->attachment) : null,
            'document_status_vote_id' => $document->document_status_vote_id,
            'document_status_movement_id' => $document->document_status_movement_id,
            'status_sign' => $document->status_sign,
        ]);
    }

    public function getDocumentsForIndex(Request $request): array
    {
        $filters = $request->only(['search', 'sort', 'direction', 'categories']);

        $categories = DocumentCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'documents' => $this->getAllDocuments($request),
            'filters' => $filters,
            'categories' => $categories
        ];
    }

    public function getDocumentForEdit(int $id): array
    {
        return [
            'document' => Document::findOrFail($id),
            'vote_statuses' => DocumentStatusVote::all(),
            'movement_statuses' => DocumentStatusMovement::all(),
            'categories' => DocumentCategory::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    public function updateDocument(int $id, array $data): Document
    {
        $document = Document::findOrFail($id);

        $fieldsToUpdate = Arr::only($data, [
            'name',
            'protocol_number',
            'document_status_vote_id',
            'document_status_movement_id',
            'document_category_id',
        ]);

        $document->update($fieldsToUpdate);

        return $document;
    }

    public function destroyDocument(int $id): void
    {
        $document = Document::findOrFail($id);
        $document->delete();
    }
}
