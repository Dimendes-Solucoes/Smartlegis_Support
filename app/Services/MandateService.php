<?php

namespace App\Services;

use App\Models\Tenancy\CategoryParty;
use App\Models\Tenancy\Mandate;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserCategory;
use App\Models\Tenancy\UserTerm;
use Illuminate\Support\Collection;

class MandateService
{
    public function list(): Collection
    {
        return Mandate::orderBy('start_at', 'desc')->get();
    }

    public function getForEdit(int $id): array
    {
        $mandate = Mandate::with(['userTerms.user' => fn ($q) => $q->withTrashed()])->findOrFail($id);

        $allOtherTerms = UserTerm::with('mandate')
            ->where('mandate_id', '!=', $id)
            ->get();

        $userTerms = $mandate->userTerms->map(function ($term) use ($allOtherTerms) {
            $termStart = $term->start_date;
            $termEnd   = $term->end_date;

            $hasConflict = $allOtherTerms
                ->where('user_id', $term->user_id)
                ->contains(function ($other) use ($termStart, $termEnd) {
                    $otherStart = $other->start_date;
                    $otherEnd   = $other->end_date;

                    $endA = $termEnd   ?? now()->addYears(100);
                    $endB = $otherEnd  ?? now()->addYears(100);

                    return $termStart <= $endB && $otherStart <= $endA;
                });

            return [
                'id'                => $term->id,
                'user_id'           => $term->user_id,
                'name'              => $term->user?->name ?? '',
                'category_party_id' => $term->category_party_id,
                'start_date'        => $termStart?->format('Y-m-d'),
                'end_date'          => $termEnd?->format('Y-m-d'),
                'has_conflict'      => $hasConflict,
            ];
        })
            ->sortBy('name')
            ->values();

        $councilors = User::whereIn('user_category_id', [UserCategory::VEREADOR, UserCategory::PRESIDENTE])
            ->orderBy('name')
            ->get(['id', 'name']);

        $parties = CategoryParty::orderBy('name_party')->get(['id', 'name_party']);

        $categories = UserCategory::whereIn('id', UserCategory::LEGISLATIVO)
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'mandate'   => [
                ...$mandate->toArray(),
                'start_at' => $mandate->start_at->format('Y-m-d'),
                'end_at'   => $mandate->end_at->format('Y-m-d'),
            ],
            'userTerms'  => $userTerms,
            'councilors' => $councilors,
            'parties'    => $parties,
            'categories' => $categories,
        ];
    }

    public function create(array $data): Mandate
    {
        return Mandate::create($data);
    }

    public function update(int $id, array $data): Mandate
    {
        $mandate = Mandate::findOrFail($id);
        $mandate->update($data);
        return $mandate;
    }

    public function updateUsers(int $id, array $data): void
    {
        $mandate = Mandate::findOrFail($id);

        $mandate->userTerms()->delete();

        foreach ($data['users'] as $term) {
            $endDate = $term['end_date'] ?: null;
            if (!$endDate && !$mandate->is_current) {
                $endDate = $mandate->end_at->format('Y-m-d');
            }

            $mandate->userTerms()->create([
                'user_id'           => $term['user_id'],
                'category_party_id' => $term['category_party_id'] ?? null,
                'start_date'        => $term['start_date'],
                'end_date'          => $endDate,
            ]);
        }

        if ($mandate->is_current) {
            foreach ($data['users'] as $term) {
                User::where('id', $term['user_id'])
                    ->update(['category_party_id' => $term['category_party_id'] ?? null]);
            }
        }
    }
}
