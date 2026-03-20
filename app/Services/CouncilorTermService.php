<?php

namespace App\Services;

use App\Models\Tenancy\Legislature;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserTerm;
use Exception;
use Illuminate\Support\Facades\DB;

class CouncilorTermService
{
    public function getIndexData(int $councilorId): array
    {
        $councilor = User::findOrFail($councilorId);

        $terms = UserTerm::with('legislature')
            ->where('user_id', $councilorId)
            ->orderByDesc('legislature_id')
            ->get();

        $legislatures = Legislature::orderByDesc('start_at')->get();

        return [
            'councilor'    => $councilor,
            'terms'        => $terms,
            'legislatures' => $legislatures,
        ];
    }

    public function store(int $councilorId, array $data): UserTerm
    {
        $this->ensureNoDuplicate($councilorId, $data['legislature_id']);

        return DB::transaction(function () use ($councilorId, $data) {
            return UserTerm::create([
                'user_id'        => $councilorId,
                'legislature_id' => $data['legislature_id'],
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date'] ?? null,
            ]);
        });
    }

    public function update(int $termId, array $data): UserTerm
    {
        $term = UserTerm::findOrFail($termId);

        $this->ensureNoDuplicate($term->user_id, $data['legislature_id'], $termId);

        return DB::transaction(function () use ($term, $data) {
            $term->update([
                'legislature_id' => $data['legislature_id'],
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date'] ?? null,
            ]);

            return $term->fresh('legislature');
        });
    }

    public function delete(int $termId): void
    {
        $term = UserTerm::findOrFail($termId);
        $term->delete();
    }

    private function ensureNoDuplicate(int $userId, int $legislatureId, ?int $excludeTermId = null): void
    {
        $query = UserTerm::where('user_id', $userId)
            ->where('legislature_id', $legislatureId);

        if ($excludeTermId) {
            $query->where('id', '!=', $excludeTermId);
        }

        if ($query->exists()) {
            throw new Exception('Este vereador já possui um mandato cadastrado para esta legislatura.');
        }
    }
}
