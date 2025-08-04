<?php

namespace App\Services;

use App\Models\Tenancy\BigDiscussion;
use App\Models\Tenancy\BigDiscussionUsers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class BigDiscussionService
{
    public function getAllBigDiscussions(Request $request): LengthAwarePaginator
    {
        $paginatedResult = BigDiscussion::with('quorum.session')
            ->whereHas('quorum.session')
            ->latest('id')
            ->paginate(15);

        return $paginatedResult->through(fn (BigDiscussion $discussion) => [
            'id' => $discussion->id,
            'status' => $discussion->status_big_discussion,
            'session_name' => $discussion->quorum->session->name ?? 'Sessão não encontrada',
        ]);
    }

    public function getBigDiscussionDetails(BigDiscussion $discussion): array
    {
        $discussion->load('quorum.session');
        $users = $discussion->bigDiscussionUsers()->with('user')->get();

        return [
            'discussion' => $discussion,
            'users' => $users,
        ];
    }


    public function removeUserFromDiscussion(BigDiscussionUsers $user): void
    {
        $user->delete();
    }
}