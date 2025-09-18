<?php

namespace App\Services;

use App\Models\Tenancy\Tribune;
use App\Models\Tenancy\TribuneUsers;
use Illuminate\Support\Facades\DB;

class TribuneService
{
    public function findBySessionId(int $session_id): array
    {
        $tribune = Tribune::whereHas('quorum', fn($q) => $q->where('session_id', $session_id))->first();
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

    public function updateUserOrder(array $user_ids): void
    {
        if (empty($user_ids)) {
            return;
        }

        DB::transaction(function () use ($user_ids) {
            foreach ($user_ids as $index => $user_id) {
                TribuneUsers::where('id', $user_id)
                    ->update(['position' => $index + 1]);
            }
        });
    }
}
