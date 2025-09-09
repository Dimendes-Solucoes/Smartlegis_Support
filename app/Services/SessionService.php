<?php

namespace App\Services;

use App\Models\Tenancy\Document;
use App\Models\Tenancy\DocumentSession;
use App\Models\Tenancy\Quorum;
use App\Models\Tenancy\Session;
use App\Models\Tenancy\SessionStatus;
use App\Models\Tenancy\Vote;
use App\Models\Tenancy\VoteCategory;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class SessionService
{
    public function __construct(
        protected QuorumService $quorumService,
        protected TribuneService $tribuneService,
        protected DiscussionService $discussionService,
        protected BigDiscussionService $bigDiscussionService,
        protected QuestionOrderService $questionOrderService
    ) {}

    public function getAllSessions(): LengthAwarePaginator
    {
        $sort_field = Request::input('sort', 'datetime_start');
        $sort_direction = Request::input('direction', 'desc');
        $sortable_fields = ['name', 'datetime_start'];

        if (!in_array($sort_field, $sortable_fields)) {
            $sort_field = 'datetime_start';
        }

        return Session::orderBy($sort_field, $sort_direction)
            ->paginate(15)
            ->through(fn($session) => [
                'id' => $session->id,
                'name' => $session->name,
                'datetime_start' => $session->datetime_start,
                'session_status_id' => $session->session_status_id,
            ]);
    }

    public function find(int $id): Session
    {
        return Session::with([
            'quorums' => function ($query) {
                $query->with([
                    'tribunes',
                    'discussions',
                    'bigDiscussions',
                    'questionOrders'
                ]);
            }
        ])->findOrFail($id);
    }

    public function prepareForCreateSesssion()
    {
        $session_statuses = SessionStatus::orderBy('id')->get();

        return [
            'session_statuses' => $session_statuses
        ];
    }

    public function store(array $data): Session
    {
        $data['user_id'] = auth()->id();
        return Session::create($data);
    }

    public function prepareForEditSession(int $id)
    {
        $session = $this->find($id);
        $session_statuses = SessionStatus::orderBy('id')->get();

        return [
            'session' => $session,
            'session_statuses' => $session_statuses
        ];
    }

    public function update(int $id, array $data): void
    {
        $session = Session::findOrFail($id);

        if ($data['session_status_id'] == 3) {
            $data['datetime_end'] = now();
        }

        $session->update($data);
    }

    public function prepareForDocuments(int $id): array
    {
        $session = Session::with('documents.category')->findOrFail($id);
        $documents = $session->documents;

        [$agendaDocuments, $extraDocuments] = $documents->partition(fn($document) => $document->pivot->ordem_do_dia == 1);

        $sortedAgendaDocuments = $this->sortAndTransform($agendaDocuments);
        $sortedExtraDocuments = $this->sortAndTransform($extraDocuments);

        return [
            'session' => $session,
            'agendaDocuments' => $sortedAgendaDocuments,
            'extraDocuments' => $sortedExtraDocuments,
        ];
    }

    public function updateDocuments(int $id, array $data): void
    {
        $session = Session::findOrFail($id);

        DB::beginTransaction();

        try {
            $this->updateDocumentOrder($session->id, $data['expediente_documents'], 0);
            $this->updateDocumentOrder($session->id, $data['ordem_do_dia_documents'], 1);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao atualizar ordem de documentos da sessão: " . $e->getMessage());
        }
    }

    public function resetDocuments(int $id): void
    {
        $session = Session::findOrFail($id);

        DocumentSession::where('session_id', $session->id)
            ->update(['order' => 0]);
    }

    public function prepareForDocumentVotes(int $session_id, int $document_id): array
    {
        $session = Session::findOrFail($session_id);

        $document = Document::where('id', $document_id)
            ->whereHas('sessions', fn($query) => $query->where('sessions.id', $session_id))
            ->firstOrFail();

        $votes = Vote::where('session_id', $session_id)
            ->where('document_id', $document_id)
            ->with('user')
            ->get();

        $voteCategories = VoteCategory::all();

        $quorum = Quorum::where('session_id', $session_id)->first();

        $quorumUsers = $quorum ? $quorum->users : collect();

        $votedUserIds = $votes->pluck('user_id');

        $notVotedUsers = $quorumUsers->whereNotIn('id', $votedUserIds);

        return [
            'session' => $session,
            'document' => $document,
            'votes' => $votes,
            'voteCategories' => $voteCategories,
            'notVotedUsers' => $notVotedUsers,
        ];
    }

    public function updateDocumentVotes(int $session_id, int $document_id, array $data)
    {
        DB::beginTransaction();

        try {
            foreach ($data['votes'] as $voteData) {
                if ($voteData['vote_category_id'] === null) {
                    $this->removeVote($session_id, $document_id, $voteData['user_id']);
                } else {
                    $this->createOrUpdateVote($session_id, $document_id, $voteData);
                }
            }

            DB::commit();

            $this->updateVotationResult($document_id, $session_id);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
        }
    }

    public function getQuorums(int $id)
    {
        $session = Session::findOrFail($id);

        return $this->quorumService->findBySessionId($session->id);
    }

    public function clearQuorums(int $id)
    {
        $session = Session::findOrFail($id);
        $session->quorums()->delete();
    }

    public function getTribunes(int $id)
    {
        $session = Session::findOrFail($id);

        return $this->tribuneService->findBySessionId($session->id);
    }

    public function clearTribunes(int $id)
    {
        $session = Session::findOrFail($id);

        foreach ($session->quorums as $quorum) {
            $quorum->tribunes()->delete();
        }
    }

    public function getAllDiscussionsBySession($id)
    {
        $data['session_id'] = $id;

        return $this->discussionService->getAllDiscussions($data);
    }

    public function getDiscussions(int $id, int $discussion_id)
    {
        $session = Session::findOrFail($id);

        return $this->discussionService->findBySessionId($session->id, $discussion_id);
    }

    public function clearDiscussions(int $id)
    {
        $session = Session::findOrFail($id);

        foreach ($session->quorums as $quorum) {
            $quorum->discussions()->delete();
        }
    }

    public function getBigDiscussions(int $id)
    {
        $session = Session::findOrFail($id);

        return $this->bigDiscussionService->findBySessionId($session->id);
    }

    public function clearBigDiscussions(int $id)
    {
        $session = Session::findOrFail($id);

        foreach ($session->quorums as $quorum) {
            $quorum->bigDiscussions()->delete();
        }
    }

    public function getQuestionOrders(int $id)
    {
        $session = Session::findOrFail($id);

        return $this->questionOrderService->findBySessionId($session->id);
    }

    public function clearQuestionOrders(int $id)
    {
        $session = Session::findOrFail($id);

        foreach ($session->quorums as $quorum) {
            $quorum->questionOrders()->delete();
        }
    }

    public function delete(int $id): void
    {
        $session = Session::findOrFail($id);
        $session->delete();
    }

    private function updateDocumentOrder(int $session_id, array $document_ids, int $ordem_do_dia): void
    {
        if (empty($document_ids)) {
            return;
        }

        foreach ($document_ids as $index => $document_id) {
            DocumentSession::where('session_id', $session_id)
                ->where('document_id', $document_id)
                ->where('ordem_do_dia', $ordem_do_dia)
                ->update(['order' => $index + 1]);
        }
    }

    private function removeVote(int $session_id, int $document_id, int $user_id)
    {
        Vote::where('session_id', $session_id)
            ->where('document_id', $document_id)
            ->where('user_id', $user_id)
            ->delete();
    }

    private function createOrUpdateVote(int $session_id, int $document_id, array $voteData)
    {
        $findAttributes = [
            'session_id' => $session_id,
            'document_id' => $document_id,
            'user_id' => $voteData['user_id'],
            'order' => $this->getOrder($document_id, $session_id)
        ];

        $updateAttributes = [
            'vote_category_id' => $voteData['vote_category_id'],
        ];

        Vote::updateOrCreate($findAttributes, $updateAttributes);
    }

    private static function getOrder(int $document_id, int $session_id): int
    {
        $sessionVoteOrder = Vote::where('document_id', $document_id)
            ->where('session_id', $session_id)
            ->value('order');

        if ($sessionVoteOrder) {
            return $sessionVoteOrder;
        }

        $lastOrder = Vote::where('document_id', $document_id)
            ->max('order');

        return ($lastOrder ?? 0) + 1;
    }

    private function allQuorumMembersVoted(int $document_id, int $session_id): bool
    {
        $quorum = Quorum::where('session_id', $session_id)->first();

        if (!$quorum) {
            return false;
        }

        $votes = Vote::where('document_id', $document_id)
            ->where('session_id', $session_id)
            ->get();

        $quorumUserIds = $quorum->users->pluck('id')->sort()->values();
        $votedUserIds = $votes->pluck('user_id')->sort()->values();

        return $quorumUserIds->diff($votedUserIds)->isEmpty() && $votedUserIds->diff($quorumUserIds)->isEmpty();
    }

    private function updateVotationResult(int $document_id, int $session_id)
    {
        $votes = Vote::where('document_id', $document_id)
            ->where('session_id', $session_id)
            ->get();

        $last_order = $votes->max('order');

        if (!$last_order) {
            return;
        }

        $votingResult = null;

        if ($this->allQuorumMembersVoted($document_id, $session_id)) {
            $votesByOrder = $votes->where('order', $last_order);

            $results = $votesByOrder->groupBy('vote_category_id')->map->count();

            $maxCount = $results->max();
            $winnerCount = $results->filter(fn($count) => $count === $maxCount)->count();

            if ($winnerCount > 1) {
                $votingResult = 3;
            } else {
                $votingResult = $results->search($maxCount);
            }
        }

        $document = Document::findOrFail($document_id);
        $document->update(["voting_result_{$last_order}" => $votingResult]);
    }

    private function sortAndTransform(Collection $documents): Collection
    {
        $sorted = $documents->sort(function ($a, $b) {
            $orderComparison = $a->pivot->order <=> $b->pivot->order;

            if ($orderComparison !== 0) {
                return $orderComparison;
            }

            $categoryOrderComparison = $a->category->order <=> $b->category->order;

            if ($categoryOrderComparison !== 0) {
                return $categoryOrderComparison;
            }

            $categoryIdComparison = $a->category_id <=> $b->category_id;

            if ($categoryIdComparison !== 0) {
                return $categoryIdComparison;
            }

            return $a->protocol_number <=> $b->protocol_number;
        })->values();

        return $sorted->map(function ($document) {
            $document->attachment = Storage::url($document->attachment);
            $document->protocol_number = (int) $document->protocol_number;
            return $document;
        });
    }

    public function resetSession(int $id): void
    {
        $session = Session::with('documents')->findOrFail($id);

        DB::transaction(function () use ($session) {
            $this->clearQuorums($session->id);
            $this->clearTribunes($session->id);
            $this->clearDiscussions($session->id);
            $this->clearBigDiscussions($session->id);
            $this->clearQuestionOrders($session->id);

            $documentIds = $session->documents->pluck('id');

            if ($documentIds->isNotEmpty()) {
                Vote::where('session_id', $session->id)
                    ->whereIn('document_id', $documentIds)
                    ->delete();

                foreach ($session->documents as $document) {
                    $lastVoteOrder = Vote::where('document_id', $document->id)
                                         ->max('order');
                    $updateData = [
                        'document_status_vote_id' => 2, // "Aguardando Votação"
                        'document_status_movement_id' => 2, // "Em Sessão"
                    ];
                    if ($lastVoteOrder == 1) {
                        $updateData['voting_result_1'] = null;
                    } elseif ($lastVoteOrder == 2) {
                        $updateData['voting_result_2'] = null;
                    } 

                    $document->update($updateData);
                }
            }

            $session->update(['session_status_id' => 1]); // "aguardando votação"
        });
    }

    public function duplicateSession(int $id): Session
    {
        $originalSession = Session::with('documents')->findOrFail($id);

        return DB::transaction(function () use ($originalSession) {
            $newSession = $originalSession->replicate(['datetime_end']);
            $newSession->name = '(DUPLICATA) ' . $originalSession->name ;
            $newSession->session_status_id = 2; // "Aguardando Votação"
            $newSession->created_at = now();
            $newSession->updated_at = now();
            $newSession->save();

            $newDocumentsPivotData = [];
            foreach ($originalSession->documents as $originalDocument) {
                $newDocument = $originalDocument->replicate();
                $newDocument->protocol_number = '0';
                $newDocument->status_sign = 0;
                $newDocument->doc_key_clicksign = null;
                $newDocument->created_at = now();
                $newDocument->updated_at = now();
                $newDocument->save();
                
                $newDocumentsPivotData[$newDocument->id] = [
                    'ordem_do_dia' => $originalDocument->pivot->ordem_do_dia,
                    'order' => $originalDocument->pivot->order,
                ];
            }

            if (!empty($newDocumentsPivotData)) {
                $newSession->documents()->attach($newDocumentsPivotData);
            }

            return $newSession;
        });
    }

    public function removeDocumentFromSession(int $session_id, int $document_id): void
    {
        $session = Session::findOrFail($session_id);
        $session->documents()->detach($document_id);
    }
}
