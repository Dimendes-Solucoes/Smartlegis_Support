<?php

namespace App\Services;

use App\Models\Tenancy\BigDiscussion;
use App\Models\Tenancy\BigDiscussionUsers;

class BigDiscussionService
{
    public function getBigDiscussionDetails(BigDiscussion $discussion): array
    {
        $discussion->load('quorum.session');
        $users = $discussion->bigDiscussionUsers()->with('user')->get();

        return [
            'discussion' => $discussion,
            'users' => $users,
        ];
    }

    public function removeUserFromDiscussion(int $big_discussion_id): void
    {
        $user = BigDiscussionUsers::findOrFail($big_discussion_id);
        $user->delete();
    }

    public function destroyBigDiscussion(BigDiscussion $discussion): void
    {
        $discussion->delete();
    }
}