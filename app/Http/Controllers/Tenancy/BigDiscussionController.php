<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\BigDiscussion;
use App\Models\Tenancy\BigDiscussionUsers;
use App\Services\BigDiscussionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BigDiscussionController extends Controller
{
    public function __construct(protected BigDiscussionService $service) {}

    public function index(Request $request)
    {
        return Inertia::render('Tenancy/BigDiscussions/Index', [
            'discussions' => $this->service->getAllBigDiscussions($request),
            'filters' => $request->only(['sort', 'direction', 'search']),
        ]);
    }

    public function edit(int $id)
    {
        $discussion = BigDiscussion::findOrFail($id);
        
        return Inertia::render('Tenancy/BigDiscussions/EditMember', [
            'discussionData' => $this->service->getBigDiscussionDetails($discussion),
        ]);
    }

    public function removeUser(int $id)
    {
        $user = BigDiscussionUsers::findOrFail($id);
        $this->service->removeUserFromDiscussion($user);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}