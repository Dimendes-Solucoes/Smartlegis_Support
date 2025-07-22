<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\Session;
use Illuminate\Support\Facades\Storage;
use App\Services\SessionService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SessionController extends Controller
{
    public function __construct(
        protected SessionService $service
    ) {}

    public function index()
    {
        $sessions = $this->service->getAllSessions();
        return Inertia::render('Tenancy/Sessions/Index', [
            'sessions' => $sessions,
        ]);
    }

 public function edit(int $id)
    {
        $session = Session::findOrFail($id);
        
        $documents = DB::table('document_sessions')
            ->join('documents', 'document_sessions.document_id', '=', 'documents.id')
            ->where('document_sessions.session_id', $session->id)
            ->select('documents.id', 'documents.name', 'documents.attachment', 'document_sessions.ordem_do_dia', 'document_sessions.order')
            ->orderBy('document_sessions.order', 'asc')
            ->get();

        $documents->transform(function ($doc) {
            $doc->attachment = Storage::url($doc->attachment);
            return $doc;
        });

        [$agendaDocuments, $extraDocuments] = $documents->partition(fn ($doc) => $doc->ordem_do_dia == 1);

        return Inertia::render('Tenancy/Sessions/EditOrder', [
            'session' => $session,
            'agendaDocuments' => $agendaDocuments->values(),
            'extraDocuments' => $extraDocuments->values(),
        ]);
    }
}