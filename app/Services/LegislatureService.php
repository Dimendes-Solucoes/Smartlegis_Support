<?php

namespace App\Services;

use App\Models\Tenancy\Legislature;
use App\Models\Tenancy\User;
use Illuminate\Support\Collection;

class LegislatureService
{
    public function list(): Collection
    {
        return Legislature::orderBy('start_at', 'desc')->get();
    }

    public function getCreationFormData(): array
    {
        return [];
    }

    public function getForEdit(int $id): array
    {
        $legislature = Legislature::with('userTerms.user')->findOrFail($id);

        $userTerms = $legislature->userTerms->map(fn($term) => [
            'id'         => $term->id,
            'user_id'    => $term->user_id,
            'name'       => $term->user->name,
            'start_date' => $term->start_date?->format('Y-m-d'),
            'end_date'   => $term->end_date?->format('Y-m-d'),
        ])->values();

        $councilors = User::where('status_user', User::USUARIO_VEREADOR)->get(['id', 'name']);

        return [
            'legislature' => [
                ...$legislature->toArray(),
                'start_at' => $legislature->start_at->format('Y-m-d'),
                'end_at'   => $legislature->end_at->format('Y-m-d'),
            ],
            'userTerms'   => $userTerms,
            'councilors'  => $councilors,
        ];
    }

    public function create(array $data): Legislature
    {
        return Legislature::create($data);
    }

    public function update(int $id, array $data): Legislature
    {
        $legislature = Legislature::findOrFail($id);
        $legislature->update($data);
        return $legislature;
    }

    public function updateUsers(int $id, array $data): void
    {
        $legislature = Legislature::findOrFail($id);

        $legislature->userTerms()->delete();

        foreach ($data['users'] as $term) {
            $legislature->userTerms()->create([
                'user_id'    => $term['user_id'],
                'start_date' => $term['start_date'],
                'end_date'   => $term['end_date'] ?? null,
            ]);
        }
    }
}
