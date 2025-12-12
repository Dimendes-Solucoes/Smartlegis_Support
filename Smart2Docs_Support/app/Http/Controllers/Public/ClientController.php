<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use Inertia\Inertia;

class ClientController extends Controller
{   
    public function __construct(
        protected ClientService $service
    ) {}

    public function index()
    {
        $clients = $this->service->list();

        return Inertia::render('Public/Clients/Index', [
            'clients' => $clients
        ]);
    }
}
