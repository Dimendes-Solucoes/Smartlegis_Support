<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\TicketStoreRequest;
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
        $this->service->create($request->all());

        return redirect()->back()->with('success', "Ticket adicionado com sucesso");
    }
}
