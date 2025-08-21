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
        $session = Session::with('documents')->findOrFail($id);

        $documents = $session->documents;

        $documents->transform(function ($document) {
            $document->attachment = Storage::url($document->attachment);
            $document->ordem_do_dia = $document->pivot->ordem_do_dia;
            $document->order = $document->pivot->order;

            return $document;
        });

        [$agendaDocuments, $extraDocuments] = $documents->partition(fn($document) => $document->ordem_do_dia == 1);

        return [
            'session' => $session,
            'agendaDocuments' => $agendaDocuments->values(),
            'extraDocuments' => $extraDocuments->values(),
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
        try {
            DB::beginTransaction();

            foreach ($data['votes'] as $dataVote) {
                $findAttributes = [
                    'session_id' => $session_id,
                    'document_id' => $document_id,
                    'user_id' => $dataVote['user_id'],
                ];

                $updateAttributes = [
                    'vote_category_id' => $dataVote['vote_category_id'],
                ];

                Vote::updateOrCreate($findAttributes, $updateAttributes);
            }

            DB::commit();
        } catch (\Exception $e) {
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
}
