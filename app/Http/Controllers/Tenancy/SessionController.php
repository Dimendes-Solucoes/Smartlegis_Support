<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sessions\SessionStoreRequest;
use App\Http\Requests\Sessions\SessionUpdateDocumentVotesRequest;
use App\Http\Requests\Sessions\SessionUpdateOrderRequest;
use App\Services\SessionService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(
        protected SessionService $service
    ) {}

    public function index(Request $request)
    {
        $sessions = $this->service->getAllSessions();

        return Inertia::render('Tenancy/Sessions/Index', [
            'sessions' => $sessions,
            'filters' => $request->only(['sort', 'direction']),
        ]);
    }

    public function create()
    {
        $data = $this->service->prepareForCreateSesssion();

        return Inertia::render('Tenancy/Sessions/CreateSession', $data);
    }

    public function store(SessionStoreRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('sessions.index')->with('success', 'Sessão cadastrada com sucesso!');
    }

    public function edit(int $id)
    {
        $data = $this->service->prepareForEditSession($id);

        return Inertia::render('Tenancy/Sessions/EditSession', $data);
    }

    public function update(SessionStoreRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->route('sessions.index')->with('success', 'Sessão atualizada com sucesso!');
    }

    public function documents(int $id)
    {
        $data = $this->service->prepareForDocuments($id);

        return Inertia::render('Tenancy/Sessions/EditOrder', $data);
    }

    public function updateDocuments(SessionUpdateOrderRequest $request, int $id)
    {
        $this->service->updateDocuments($id, $request->validated());

        return back()->with('success', 'Ordem dos documentos salva com sucesso!');
    }

    public function resetDocuments(int $id)
    {
        $this->service->resetDocuments($id);

        return back()->with('success', 'Ordem dos documentos resetada com sucesso!');
    }

    public function documentVotes(int $id, int $document_id)
    {
        $data = $this->service->prepareForDocumentVotes($id, $document_id);

        return Inertia::render('Tenancy/Sessions/EditDocumentVotes', $data);
    }

    public function updateDocumentVotes(SessionUpdateDocumentVotesRequest $request, int $id, int $document_id)
    {
        $this->service->updateDocumentVotes($id, $document_id, $request->validated());

        return back()->with('success', 'Votos salvos com sucesso!');
    }

    public function quorums(int $id)
    {
        return Inertia::render('Tenancy/Quorums/EditMember', [
            'quorumData' => $this->service->getQuorums($id),
        ]);
    }

    public function clearQuorums(int $id)
    {
        $this->service->clearQuorums($id);

        return back()->with('success', 'Quoruns da sessão foram excluídos!');
    }

    public function tribunes(int $id)
    {
        return Inertia::render('Tenancy/Tribunes/EditMember', [
            'tribuneData' => $this->service->getTribunes($id),
        ]);
    }

    public function clearTribunes(int $id)
    {
        $this->service->clearTribunes($id);

        return back()->with('success', 'Tribunas da sessão foram excluídas!');
    }

    public function listDiscussions(Request $request, int $id)
    {
        return Inertia::render('Tenancy/Sessions/Discussions', [
            'discussionData' => $this->service->getAllDiscussionsBySession($id),
            'filters' => $request->only(['sort', 'direction', 'search']),
        ]);
    }

    public function discussions(int $id, int $discussion_id)
    {
        return Inertia::render('Tenancy/Discussions/EditMember', [
            'discussionData' => $this->service->getDiscussions($id, $discussion_id),
        ]);
    }

    public function clearDiscussions(int $id)
    {
        $this->service->clearDiscussions($id);

        return back()->with('success', 'Discussões da sessão foram excluídas!');
    }

    public function bigDiscussions(int $id)
    {
        return Inertia::render('Tenancy/BigDiscussions/EditMember', [
            'discussionData' => $this->service->getBigDiscussions($id),
        ]);
    }

    public function clearBigDiscussions(int $id)
    {
        $this->service->clearBigDiscussions($id);

        return back()->with('success', 'Explanações pessoais da sessão foram excluídas!');
    }

    public function questionOrders(int $id)
    {
        return Inertia::render('Tenancy/QuestionOrders/EditMember', [
            'questionOrderData' => $this->service->getQuestionOrders($id),
        ]);
    }

    public function clearQuestionOrders(int $id)
    {
        $this->service->clearQuestionOrders($id);

        return back()->with('success', 'Questões de ordem da sessão foram excluídas!');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return back()->with('success', 'Sessão movida para a lixeira com sucesso!');
    }

    public function reset(int $id)
    {
        $this->service->resetSession($id);

        return back()->with('success', 'Sessão resetada com sucesso!');
    }

    public function duplicate(int $id)
    {
        $this->service->duplicateSession($id);

        return redirect()->route('sessions.index')->with('success', 'Sessão duplicada com sucesso!');
    }

    public function removeDocumentFromExpedient(int $id, int $document_id)
    {
        $this->service->removeDocumentFromSession($id, $document_id, 0);

        return redirect()->route('sessions.documents', $id)->with('success', 'Documento removido do expediente com sucesso!');
    }

    public function removeDocumentFromOrder(int $id, int $document_id)
    {
        $this->service->removeDocumentFromSession($id, $document_id, 1);

        return redirect()->route('sessions.documents', $id)->with('success', 'Documento removido da ordem com sucesso!');
    }
}
