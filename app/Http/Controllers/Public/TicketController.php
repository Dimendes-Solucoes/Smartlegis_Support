<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\Tenant;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = [
            [
                'id' => 1,
                'title' => 'Ticket teste',
                'description' => 'Descricao teste',
                'ticket_status_id' => 1,
                'ticket_type_id' => 1,
                'author_id' => 1,
                'ticket_status' => [
                    'title' => 'Pendente',
                    'color' => '#FF0000'
                ],
                'ticket_type' => [
                    'title' => 'Operacional'
                ],
                'author' => [
                    'name' => 'Lucia'
                ]
            ]
        ];

        return Inertia::render('Public/Tickets/Index', [
            'tickets' => $tickets
        ]);
    }

    public function create()
    {
        $formData = [
            'ticket_types' => [
                [
                    'id' => 1,
                    'title' => 'Operacional'
                ],
                [
                    'id' => 2,
                    'title' => 'Equipamentos'
                ],
                [
                    'id' => 3,
                    'title' => 'Desconhecido'
                ]
            ],
            'tenants' => Credential::all()
        ];

        return Inertia::render('Public/Tickets/CreateTicket', [
            'formData' => $formData
        ]);
    }
}
