<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sessions\SessionUpdateOrderRequest;
use App\Http\Requests\Sessions\SessionUpdateRequest;
use App\Models\Tenancy\DocumentSession;
use App\Models\Tenancy\Session;
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

    public function edit(int $id)
    {
        $session = $this->service->find($id);

        return Inertia::render('Tenancy/Sessions/EditSession', [
            'session' => $session
        ]);
    }

    public function update(SessionUpdateRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return back()->with('success', 'Sessão atualizada com sucesso!');
    }

    public function editOrder(int $id)
    {
        $data = $this->service->prepareForEditOrder($id);

        return Inertia::render('Tenancy/Sessions/EditOrder', $data);
    }

    public function updateOrder(SessionUpdateOrderRequest $request, int $id)
    {
        $this->service->updateOrder($id, $request->validated());

        return back()->with('success', 'Ordem da pauta salva com sucesso!');
    }

    public function talks(int $id)
    {
        $session = $this->service->find($id);

        return Inertia::render('Tenancy/Sessions/Talks', [
            'session' => $session
        ]);
    }

    public function quorums(int $id)
    {
        return Inertia::render('Tenancy/Quorums/EditMember', [
            'quorumData' => $this->service->getQuorums($id),
        ]);
    }

    public function tribunes(int $id)
    {
        return Inertia::render('Tenancy/Tribunes/EditMember', [
            'tribuneData' => $this->service->getTribunes($id),
        ]);
    }

    public function listDiscussions(Request $request, int $id)
    {
        return Inertia::render('Tenancy/Sessions/Discussions', [
            'discussions' => $this->service->getAllDiscussionsBySession($id),
            'filters' => $request->only(['sort', 'direction', 'search']),
        ]);
    }

    public function discussions(int $id, int $discussion_id)
    {
        return Inertia::render('Tenancy/Discussions/EditMember', [
            'discussionData' => $this->service->getDiscussions($id, $discussion_id),
        ]);
    }

    public function bigDiscussions(int $id)
    {
        return Inertia::render('Tenancy/BigDiscussions/EditMember', [
            'discussionData' => $this->service->getBigDiscussions($id),
        ]);
    }

    public function questionOrders(int $id)
    {
        return Inertia::render('Tenancy/QuestionOrders/EditMember', [
            'questionOrderData' => $this->service->getQuestionOrders($id),
        ]);
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return back()->with('success', 'Sessão movida para a lixeira com sucesso!');
    }
}
