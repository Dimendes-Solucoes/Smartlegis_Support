<?php

namespace App\Services;

use App\Models\Tenancy\Comission;
use App\Models\Tenancy\ComissionDocument;
use App\Models\Tenancy\Document;
use App\Models\Tenancy\DocumentSession;
use App\Models\Tenancy\LegalNorm;
use App\Models\Tenancy\Legislature;
use App\Models\Tenancy\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegalNormConversionService
{
    public function availableSessions(Request $request): array
    {
        $name       = $request->input('name');
        $onlyOpen   = $request->boolean('only_open', false);
        $withoutAta = $request->boolean('without_ata', false);

        return Session::query()
            ->when($name, fn($q) => $q->where('name', 'ilike', "%{$name}%"))
            ->when($onlyOpen, fn($q) => $q->whereIn('session_status_id', [1, 2]))
            ->when($withoutAta, fn($q) => $q->whereDoesntHave('documents', fn($d) => $d->where('document_category_id', 7)))
            ->orderBy('datetime_start', 'desc')
            ->get()
            ->map(fn($session) => [
                'id'                => $session->id,
                'name'              => $session->name,
                'datetime_start'    => $session->datetime_start?->format('d/m/Y H:i'),
                'year'              => $session->datetime_start?->year,
                'session_status_id' => $session->session_status_id,
            ])
            ->toArray();
    }

    public function availableCommissions(): array
    {
        return Legislature::with([
            'comissions' => fn($q) => $q->where('is_active', true)->orderBy('comission_name'),
        ])
            ->orderByDesc('start_at')
            ->get()
            ->map(fn($legislature) => [
                'id'          => $legislature->id,
                'title'       => $legislature->title,
                'is_current'  => $legislature->is_current,
                'commissions' => $legislature->comissions->map(fn($c) => [
                    'id'             => $c->id,
                    'comission_name' => $c->comission_name,
                    'type'           => $c->type,
                ])->values(),
            ])
            ->filter(fn($l) => count($l['commissions']) > 0)
            ->values()
            ->toArray();
    }

    public function convertToSessionDocument(int $normId, int $sessionId): void
    {
        $norm = LegalNorm::findOrFail($normId);
        Session::findOrFail($sessionId);

        DB::transaction(function () use ($norm, $sessionId) {
            $document = Document::create([
                'name'                        => $norm->object,
                'protocol_number'             => $norm->number,
                'attachment'                  => $norm->attachment,
                'document_category_id'        => 7,
                'document_status_vote_id'     => 6,
                'document_status_movement_id' => 2,
            ]);

            DocumentSession::create([
                'session_id'   => $sessionId,
                'document_id'  => $document->id,
                'ordem_do_dia' => 0,
                'order'        => 0,
            ]);

            $norm->delete();
        });
    }

    public function convertToCommissionDocument(int $normId, int $commissionId): void
    {
        $norm = LegalNorm::findOrFail($normId);
        Comission::findOrFail($commissionId);

        DB::transaction(function () use ($norm, $commissionId) {
            $document = Document::create([
                'name'                        => $norm->object,
                'protocol_number'             => $norm->number,
                'attachment'                  => $norm->attachment,
                'document_category_id'        => 7,
                'document_status_vote_id'     => 6,
                'document_status_movement_id' => 2,
            ]);

            ComissionDocument::create([
                'comission_id' => $commissionId,
                'document_id'  => $document->id,
            ]);

            $norm->delete();
        });
    }

    public function destroy(int $normId): void
    {
        LegalNorm::findOrFail($normId)->delete();
    }
}
