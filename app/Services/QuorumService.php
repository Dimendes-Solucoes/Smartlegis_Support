<?php

namespace App\Services;

use App\Models\Tenancy\Quorum;

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
}