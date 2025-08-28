<?php

namespace App\Services;

use App\Models\Tenancy\Document;
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
        $documents = Document::with(['category', 'voteStatus', 'movementStatus'])
            ->latest('id')
            ->paginate(15);

        $documents->through(fn (Document $document) => [
            'id' => $document->id,
            'name' => $document->name,
            'attachment_url' => $document->attachment ? Storage::url($document->attachment) : null,
            'category_name' => $document->category->name ?? 'N/A',
            'vote_status_name' => $document->voteStatus->name ?? 'N/A',
            'movement_status_name' => $document->movementStatus->name ?? 'N/A',
            'status_sign' => $document->status_sign,
        ]);

        return $documents;
    }

    public function getDocumentForEdit(int $id): array
    {
        return [
            'document' => Document::findOrFail($id),
            'vote_statuses' => DocumentStatusVote::all(),
            'movement_statuses' => DocumentStatusMovement::all(),
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