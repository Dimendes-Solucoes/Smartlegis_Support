<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\TicketStoreRequest;
use App\Http\Requests\Tickets\TicketUpdateRequest;
use App\Services\TicketService;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $service
    ) {}

    public function index()
    {
        $tickets = $this->service->list([]);

        return Inertia::render('Public/Tickets/Index', [
            'tickets' => $tickets
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
}
