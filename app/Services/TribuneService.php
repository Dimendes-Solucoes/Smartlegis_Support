<?php

namespace App\Services;

use App\Models\Tenancy\Tribune;
use App\Models\Tenancy\TribuneUsers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class TribuneService
{

    public function getAllTribunes(Request $request): LengthAwarePaginator
    {
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');

        $sortableFields = ['id', 'type'];
        if (!in_array($sortField, $sortableFields)) {
            $sortField = 'id';
        }

        $tribunes = Tribune::with('quorum.session')
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('quorum.session', function ($q) use ($search) {
                    $q->where('name', 'ilike', "%{$search}%");
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(15);


        $tribunes->through(fn (Tribune $tribune) => [
            'id' => $tribune->id,
            'type' => $tribune->type,
            'session_name' => $tribune->quorum->session->name ?? 'Sessão não encontrada',
        ]);

        return $tribunes;
    }

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

    public function removeUserFromTribune(TribuneUsers $tribuneUser): void
    {
        $tribuneUser->delete();
    }

    public function destroyTribune(Tribune $tribune): void
    {
        $tribune->delete();
    }
}