<?php

namespace App\Services;

use App\Models\Tenancy\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
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
}