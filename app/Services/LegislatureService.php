<?php

namespace App\Services;

use App\Models\Tenancy\Legislature;
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
        $legislature = Legislature::findOrFail($id);

        return [
            'legislature' => [
                ...$legislature->toArray(),
                'start_at' => $legislature->start_at->format('Y-m-d'),
                'end_at'   => $legislature->end_at->format('Y-m-d'),
            ],
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


}
