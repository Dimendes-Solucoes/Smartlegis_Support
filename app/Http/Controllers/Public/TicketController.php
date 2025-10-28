<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\TicketStoreAttachmentRequest;
use App\Http\Requests\Tickets\TicketStoreMessageRequest;
use App\Http\Requests\Tickets\TicketStoreRequest;
use App\Http\Requests\Tickets\TicketUpdateRequest;
use App\Models\Helpdesk\TicketStatus;
use App\Models\Helpdesk\TicketType;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $service
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'ticket_type_id',
            'ticket_status_id',
            'start_date',
            'end_date',
            'author_id',
            'credential_id',
            'per_page'
        ]);

        $tickets = $this->service->list($filters);

        $ticketTypes = TicketType::orderBy('title')->get();
        $ticketStatuses = TicketStatus::orderBy('id')->get();

        return Inertia::render('Public/Tickets/Index', [
            'tickets' => $tickets,
            'ticketTypes' => $ticketTypes,
            'ticketStatuses' => $ticketStatuses,
            'filters' => $filters
        ]);
    }

    public function create()
    {
        $formData = $this->service->prepareForCreate();

        return Inertia::render('Public/Tickets/CreateTicket', [
            'formData' => $formData
        ]);
    }

    public function store(TicketStoreRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->back()->with('success', "Ticket adicionado com sucesso");
    }

    public function view(string $code)
    {
        $ticket = $this->service->findByCode($code);
        $formData = $this->service->prepareForCreate();

        return Inertia::render('Public/Tickets/ViewTicket', [
            'ticket' => $ticket,
            'formData' => $formData
        ]);
    }

    public function update(TicketUpdateRequest $request, string $code)
    {
        $this->service->updateByCode($code, $request->validated());

        return redirect()->back()->with('success', "Ticket atualizado com sucesso");
    }

    public function storeAttachment(TicketStoreAttachmentRequest $request, string $code)
    {
        $this->service->addAttachmentsByCode($code, $request->validated());

        return redirect()->back()->with('success', "Anexo enviado com sucesso");
    }

    public function deleteAttachment(string $code, int $attachment_id)
    {
        $this->service->removeAttachmentsByCode($code, $attachment_id);

        return redirect()->back()->with('success', "Anexo deletado com sucesso");
    }

    public function storeMessage(TicketStoreMessageRequest $request, string $code)
    {
        $this->service->sendMessageByCode($code, $request->validated());

        return redirect()->back()->with('success', "Mensagem enviada com sucesso");
    }

    public function deleteMessage(string $code, int $message_id)
    {
        $this->service->removeMessageByCode($code, $message_id);

        return redirect()->back()->with('success', "Mensagem deletada com sucesso");
    }
}
