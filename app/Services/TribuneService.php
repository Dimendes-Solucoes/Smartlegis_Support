<?php

namespace App\Services;

use App\Models\Tenancy\Tribune;
use App\Models\Tenancy\TribuneUsers;

class TribuneService
{
    public function getTribuneDetails(Tribune $tribune): array
    {
        $tribune->load('quorum.session');
        $tribuneUsers = $tribune->tribuneUsers()->with('user')->orderBy('position')->get();
        $apartiamentoUsers = $tribune->apartiamentoUsers()->with('user')->get();

        return [
            'tribune' => $tribune,
            'tribune_users' => $tribuneUsers,
            'apartiamento_users' => $apartiamentoUsers,
        ];
    }

    public function removeUserFromTribune(int $tribune_user_id): void
    {
        $tribuneUser = TribuneUsers::findOrFail($tribune_user_id);
        $tribuneUser->delete();
    }
}
