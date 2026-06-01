<?php

namespace App\Services;

use App\Models\Tenancy\Mandate;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserTerm;
use Exception;
use Illuminate\Support\Facades\DB;

class CouncilorTermService
{
    public function getIndexData(int $councilorId): array
    {
        $councilor = User::findOrFail($councilorId);

        $terms = UserTerm::with('mandate')
            ->where('user_id', $councilorId)
            ->orderByDesc('mandate_id')
            ->get();

        $mandates = Mandate::orderByDesc('start_at')->get();

        return [
            'councilor' => $councilor,
            'terms'     => $terms,
            'mandates'  => $mandates,
        ];
    }

    public function store(int $councilorId, array $data): UserTerm
    {
        $this->ensureNoDuplicate($councilorId, $data['mandate_id']);

        $councilor = User::findOrFail($councilorId);

        return DB::transaction(function () use ($councilorId, $data, $councilor) {
            return UserTerm::create([
                'user_id'           => $councilorId,
                'mandate_id'        => $data['mandate_id'],
                'category_party_id' => $councilor->category_party_id,
                'start_date'        => $data['start_date'],
                'end_date'          => $data['end_date'] ?? null,
            ]);
        });
    }

    public function update(int $termId, array $data): UserTerm
    {
        $term = UserTerm::findOrFail($termId);

        $this->ensureNoDuplicate($term->user_id, $data['mandate_id'], $termId);

        return DB::transaction(function () use ($term, $data) {
            $term->update([
                'mandate_id'        => $data['mandate_id'],
                'category_party_id' => $term->user->category_party_id,
                'start_date'        => $data['start_date'],
                'end_date'          => $data['end_date'] ?? null,
            ]);

            return $term->fresh('mandate');
        });
    }

    public function delete(int $termId): void
    {
        $term = UserTerm::findOrFail($termId);
        $term->delete();
    }

    private function ensureNoDuplicate(int $userId, int $mandateId, ?int $excludeTermId = null): void
    {
        $query = UserTerm::where('user_id', $userId)
            ->where('mandate_id', $mandateId);

        if ($excludeTermId) {
            $query->where('id', '!=', $excludeTermId);
        }

        if ($query->exists()) {
            throw new Exception('Este vereador já possui um mandato cadastrado para este mandato.');
        }
    }
}
