<?php

namespace App\Services;

use App\Models\Tenancy\BigDiscussion;
use App\Models\Tenancy\BigDiscussionUsers;

class BigDiscussionService
{
    public function findBySessionId(int $session_id): array
    {
        $discussion = BigDiscussion::whereHas('quorum', fn($q) => $q->where('session_id', $session_id))->first();
        $discussion->load('quorum.session');

        $users = $discussion->bigDiscussionUsers()->with('user')->get();

        return [
            'discussion' => $discussion,
            'users' => $users
        ];
    }

    public function removeUserFromBigDiscussion(int $big_discussion_id): void
    {
        $user = BigDiscussionUsers::findOrFail($big_discussion_id);
        $user->delete();
    }
}
