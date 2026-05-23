<?php

namespace App\Services;

use App\Models\Tenancy\Author;
use App\Models\Tenancy\Document;
use App\Models\Tenancy\DocumentCategory;
use App\Models\Tenancy\DocumentSession;
use App\Models\Tenancy\DocumentStatusMovement;
use App\Models\Tenancy\DocumentStatusVote;
use App\Models\Tenancy\Session;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function __construct(
        protected ClickSignService $clickSignService,
    ) {}

    public function getAllDocuments(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $voteStatusId = $request->input('vote_status_id');
        $movementStatusId = $request->input('movement_status_id');
        $statusSign = $request->input('status_sign');
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');

        $sortableFields = ['name', 'protocol_number', 'id'];
        if (!in_array($sortField, $sortableFields)) {
            $sortField = 'id';
        }

        $query = Document::query()
            ->with(['voteStatus', 'movementStatus', 'authors.user'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%")
                        ->orWhere('protocol_number', 'ilike', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                if ($categoryId !== 'null' && $categoryId !== null && $categoryId !== '') {
                    $query->where('document_category_id', $categoryId);
                }
            })
            ->when($voteStatusId, function ($query, $voteStatusId) {
                $query->where('document_status_vote_id', $voteStatusId);
            })
            ->when($movementStatusId, function ($query, $movementStatusId) {
                $query->where('document_status_movement_id', $movementStatusId);
            })
            ->when($statusSign !== null && $statusSign !== '', function ($query) use ($statusSign) {
                $query->where('status_sign', $statusSign);
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
            'authors' => $document->authors->map(fn($author) => [
                'id' => $author->id,
                'user_id' => $author->user_id,
                'name' => $author->user?->name ?? 'Usuário não encontrado',
                'email' => $author->user?->email,
                'status' => $author->status_sign
            ]),
        ]);
    }

    public function getDocumentsForIndex(Request $request): array
    {
        $filters = $request->only([
            'search',
            'category_id',
            'vote_status_id',
            'movement_status_id',
            'status_sign',
            'sort',
            'direction',
        ]);

        $categories = DocumentCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'documents' => $this->getAllDocuments($request),
            'filters'   => $filters,
            'categories' => $categories,
        ];
    }

    public function getDocumentForEdit(int $id): array
    {
        return [
            'document' => Document::findOrFail($id),
            'vote_statuses' => DocumentStatusVote::all(),
            'movement_statuses' => DocumentStatusMovement::all(),
            'categories' => DocumentCategory::orderBy('is_active', 'desc')->orderBy('name')->get(),
            'authors' => Author::with('user')
                ->where('document_id', $id)
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'user_id' => $a->user_id,
                    'name' => $a->user?->name ?? 'Usuário não encontrado',
                    'email' => $a->user?->email,
                    'status' => $a->status_sign,
                ]),
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

    public function getAvailableSessions(Request $request): array
    {
        $year = $request->input('year');
        $name = $request->input('name');
        $onlyOpen = $request->boolean('only_open', false);
        $withoutAta = $request->boolean('without_ata', false);

        return Session::query()
            ->when($year, fn($q) => $q->whereYear('datetime_start', $year))
            ->when($name, fn($q) => $q->where('name', 'ilike', "%{$name}%"))
            ->when($onlyOpen, fn($q) => $q->whereIn('session_status_id', [1, 2]))
            ->when($withoutAta, fn($q) => $q->whereDoesntHave('documents', fn($d) => $d->where('document_category_id', 7)))
            ->orderBy('datetime_start', 'desc')
            ->get()
            ->map(fn($session) => [
                'id' => $session->id,
                'name' => $session->name,
                'datetime_start' => $session->datetime_start?->format('d/m/Y H:i'),
                'session_status_id' => $session->session_status_id,
            ])
            ->toArray();
    }

    public function addDocumentToSession(int $document_id, int $session_id): void
    {
        $document = Document::findOrFail($document_id);
        Session::findOrFail($session_id);

        $exists = DocumentSession::where('session_id', $session_id)
            ->where('document_id', $document_id)
            ->exists();

        if ($exists) {
            throw new \Exception('Documento já está nesta sessão.');
        }

        DB::transaction(function () use ($document, $session_id) {
            $maxOrder = DocumentSession::where('session_id', $session_id)
                ->where('ordem_do_dia', 0)
                ->max('order') ?? 0;

            DocumentSession::create([
                'session_id' => $session_id,
                'document_id' => $document->id,
                'ordem_do_dia' => 0,
                'order' => 0,
            ]);

            $document->update(['document_status_movement_id' => DocumentStatusMovement::EM_SESSAO]);
        });
    }

    public function clicksignResend(int $id): string
    {
        return $this->clickSignService->resend($id);
    }

    public function clicksignRegenerate(int $id): string
    {
        return $this->clickSignService->regenerateStamp($id);
    }

    public function clicksignRefresh(int $id): string
    {
        return $this->clickSignService->refreshStatus($id);
    }
}
