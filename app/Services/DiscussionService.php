<?php

namespace App\Services;

use App\Models\Tenancy\Discussion;
use App\Models\Tenancy\DiscussionUsers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class DiscussionService
{

    public function getAllDiscussions(?array $data = []): LengthAwarePaginator
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

        $discussions->through(fn (Discussion $discussion) => [
            'id' => $discussion->id,
            'session_name' => $discussion->quorum->session->name ?? 'Sessão não encontrada',
            'document_name' => $discussion->document->name ?? 'Documento não encontrado',
            'quorum' => $discussion->quorum
        ]);

        return $discussions;
    }

    public function getDiscussionDetails(Discussion $discussion): array
    {

        $discussion->load('quorum.session', 'document');
        $users = $discussion->discussionUsers()->with('user')->get();

        return [
            'discussion' => $discussion,
            'users' => $users,
        ];
    }

    public function removeUserFromDiscussion(DiscussionUsers $user): void
    {
        $user->delete();
    }

    public function destroyDiscussion(Discussion $discussion): void
    {
        $discussion->delete();
    }
}