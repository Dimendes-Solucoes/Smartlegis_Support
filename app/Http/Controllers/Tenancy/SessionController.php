<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sessions\UpdateOrderRequest;
use App\Models\Tenancy\DocumentSession;
use App\Models\Tenancy\Session;
use App\Services\SessionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(
        protected SessionService $service
    ) {}

    public function index(Request $request)
    {
        $sessions = $this->service->getAllSessions();

        return Inertia::render('Tenancy/Sessions/Index', [
            'sessions' => $sessions,
            'filters' => $request->only(['sort', 'direction']),
        ]);
    }

    public function edit(int $id)
    {
        $session = Session::findOrFail($id);

        $documents = $session->getDocumentsData();

        $documents->transform(function ($doc) {
            $doc->attachment = Storage::url($doc->attachment);
            return $doc;
        });

        [$agendaDocuments, $extraDocuments] = $documents->partition(fn($doc) => $doc->ordem_do_dia == 1);

        return Inertia::render('Tenancy/Sessions/EditOrder', [
            'session' => $session,
            'agendaDocuments' => $agendaDocuments->values(),
            'extraDocuments' => $extraDocuments->values(),
        ]);
    }

    public function update(UpdateOrderRequest $request, int $id)
    {
        $session = Session::findOrFail($id);
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $session) {

            $this->updateDocumentOrder(
                $session->id,
                $validated['expediente_documents'],
                0
            );

            $this->updateDocumentOrder(
                $session->id,
                $validated['ordem_do_dia_documents'],
                1
            );
        });

        return back()->with('success', 'Ordem da pauta salva com sucesso!');
    }

    private function updateDocumentOrder(int $sessionId, array $documentIds, int $ordemDoDia): void
    {
        if (empty($documentIds)) {
            return;
        }

        foreach ($documentIds as $index => $docId) {
            DocumentSession::where('session_id', $sessionId)
                ->where('document_id', $docId)
                ->where('ordem_do_dia', $ordemDoDia)
                ->update([
                    'order' => $index + 1,
                ]);
        }
    }
    public function destroy(int $id)
    {
        $session = Session::findOrFail($id);
        $session->delete();

        return back()->with('success', 'Sessão movida para a lixeira com sucesso!');
    }
}
