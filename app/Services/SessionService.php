<?php

namespace App\Services;

use App\Models\Tenancy\DocumentSession;
use App\Models\Tenancy\Session;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class SessionService
{
    public function getAllSessions(): LengthAwarePaginator
    {
        $sort_field = Request::input('sort', 'datetime_start');
        $sort_direction = Request::input('direction', 'desc');
        $sortable_fields = ['name', 'datetime_start'];

        if (!in_array($sort_field, $sortable_fields)) {
            $sort_field = 'datetime_start';
        }

        return Session::orderBy($sort_field, $sort_direction)
            ->paginate(15)
            ->through(fn($session) => [
                'id' => $session->id,
                'name' => $session->name,
                'datetime_start' => $session->datetime_start,
                'session_status_id' => $session->session_status_id,
            ]);
    }

    public function find(int $id): Session
    {
        return Session::findOrFail($id);
    }

    public function update(int $id, array $data): void
    {
        $session = Session::findOrFail($id);
        $session->update($data);
    }

    public function prepareForEditOrder(int $id): array
    {
        $session = Session::with('documents')->findOrFail($id);

        $documents = $session->documents;

        $documents->transform(function ($document) {
            $document->attachment = Storage::url($document->attachment);
            $document->ordem_do_dia = $document->pivot->ordem_do_dia;
            $document->order = $document->pivot->order;

            return $document;
        });

        [$agendaDocuments, $extraDocuments] = $documents->partition(fn($document) => $document->ordem_do_dia == 1);

        return [
            'session' => $session,
            'agendaDocuments' => $agendaDocuments->values(),
            'extraDocuments' => $extraDocuments->values(),
        ];
    }

    public function updateOrder(int $id, array $data): void
    {
        $session = Session::findOrFail($id);

        DB::beginTransaction();

        try {
            $this->updateDocumentOrder($session->id, $data['expediente_documents'], 0);
            $this->updateDocumentOrder($session->id, $data['ordem_do_dia_documents'], 1);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao atualizar ordem de documentos da sessão: " . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $session = Session::findOrFail($id);
        $session->delete();
    }

    private function updateDocumentOrder(int $session_id, array $document_ids, int $ordem_do_dia): void
    {
        if (empty($document_ids)) {
            return;
        }

        foreach ($document_ids as $index => $document_id) {
            DocumentSession::where('session_id', $session_id)
                ->where('document_id', $document_id)
                ->where('ordem_do_dia', $ordem_do_dia)
                ->update(['order' => $index + 1]);
        }
    }
}
