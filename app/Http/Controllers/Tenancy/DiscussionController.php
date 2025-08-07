<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\Discussion;
use App\Models\Tenancy\DiscussionUsers;
use App\Services\DiscussionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    public function __construct(protected DiscussionService $service) {}

    public function index(Request $request)
    {
        return Inertia::render('Tenancy/Discussions/Index', [
            'discussions' => $this->service->getAllDiscussions($request),
            'filters' => $request->only(['sort', 'direction', 'search']),
        ]);
    }

    public function edit(int $id)
    {
        $discussion = Discussion::findOrFail($id);
        
        return Inertia::render('Tenancy/Discussions/EditMember', [
            'discussionData' => $this->service->getDiscussionDetails($discussion),
        ]);
    }

    public function removeUser(int $id)
    {
        $user = DiscussionUsers::findOrFail($id);
        $this->service->removeUserFromDiscussion($user);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }

    public function destroy(int $id)
    {
        $discussion = Discussion::findOrFail($id);
        $this->service->destroyDiscussion($discussion);
        return back()->with('success', 'Discussão movida para a lixeira com sucesso!');
    }
}