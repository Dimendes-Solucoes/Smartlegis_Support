<?php

namespace App\Services;

use App\Models\Tenancy\Discussion;
use App\Models\Tenancy\DiscussionUsers;
use App\Models\Tenancy\Session;

class DiscussionService
{
    public function getAllDiscussions(?array $data = []): array
    {
        $discussions = Discussion::query();

        if (isset($data['session_id'])) {
            $discussions->whereHas('quorum', fn($q) => $q->where('session_id', $data['session_id']));
        }

        $discussions = $discussions->with(['quorum.session', 'document'])
            ->whereHas('quorum.session')
            ->whereHas('document')
            ->latest('id')
            ->paginate(15);

        $discussions->through(fn(Discussion $discussion) => [
            'id' => $discussion->id,
            'session_name' => $discussion->quorum->session->name ?? 'Sessão não encontrada',
            'document_name' => $discussion->document->name ?? 'Documento não encontrado',
            'quorum' => $discussion->quorum
        ]);

        return [
            'session' => Session::find($data['session_id']),
            'discussions' => $discussions
        ];
    }

    public function findBySessionId(int $session_id, int $discussion_id): array
    {
        $discussion = Discussion::where('id', $discussion_id)
            ->whereHas('quorum', fn($q) => $q->where('session_id', $session_id))
            ->first();

        $discussion->load('quorum.session', 'document');
        $users = $discussion->discussionUsers()->with('user')->get();

        return [
            'discussion' => $discussion,
            'users' => $users,
        ];
    }

    public function removeUserFromDiscussion(int $discussion_user_id): void
    {
        $user = DiscussionUsers::findOrFail($discussion_user_id);
        $user->delete();
    }
}
