<?php

namespace App\Services;

use App\Models\Tenancy\Quorum;
use App\Models\Tenancy\QuorumUsers;

class QuorumService
{
    public function getQuorumDetails(Quorum $quorum): array
    {
        $quorum->load('session');
        $users = $quorum->quorumUsers()->with('user')->get();

        return [
            'quorum' => $quorum,
            'quorum_users' => $users,
        ];
    }

    public function removeUserFromQuorum(int $id): void
    {
        $quorum_user = QuorumUsers::findOrFail($id);
        $quorum_user->delete();
    }
}
